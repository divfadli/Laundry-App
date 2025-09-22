<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\TransLaundryPickups;
use App\Models\TransOrders;
use App\Models\TransOrderDetails;
use App\Models\TypeOfServices;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\AlertMessage;

class TransOrderController extends Controller
{
    use AlertMessage;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = TransOrders::with([
                'customer' => function ($q) {
                    $q->withTrashed(); // Sertakan customer yang sudah soft delete
                }
            ]);

            // fitur search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_code', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q2) use ($search) {
                            $q2->withTrashed()->where('customer_name', 'like', "%{$search}%");
                        });
                });
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            return back()->with('error_message', 'Gagal memuat data orders: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $order_code = sprintf(
                'LDY-%s-%04d',
                Carbon::now()->format('Ymd'),
                (TransOrders::whereDate('created_at', Carbon::today())
                    ->orderByDesc('id')
                    ->first()?->id ?? 0) + 1
            );
            $customers = Customers::all();
            $services = TypeOfServices::all();
            return view('orders.create', compact('customers', 'services', 'order_code'));
        } catch (\Exception $e) {
            return back()->with('error_message', 'Gagal memuat form create: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_code' => 'required|string',
            'id_customer' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'order_end_date' => 'required|date|after_or_equal:order_date',
            'services' => 'required|array|min:1',
            'services.*.id_service' => 'required|exists:type_of_services,id',
            'services.*.qty' => 'required|numeric|min:0.1',
            'services.*.notes' => 'nullable|string',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $serviceIds = collect($validated['services'])->pluck('id_service');
                $services = TypeOfServices::whereIn('id', $serviceIds)->get()->keyBy('id');


                $total = collect($validated['services'])->reduce(function ($carry, $item) use ($services) {
                    return $carry + ($services[$item['id_service']]->price * $item['qty']);
                }, 0);

                $order = TransOrders::create([
                    'id_customer' => $validated['id_customer'],
                    'order_code' => $validated['order_code'],
                    'order_date' => $validated['order_date'],
                    'order_end_date' => $validated['order_end_date'],
                    'order_status' => 0,
                    'total' => $total,
                    'order_pay' => 0,
                    'order_change' => 0,
                ]);

                $orderDetails = collect($validated['services'])->map(function ($item) use ($services, $order) {
                    $price = $services[$item['id_service']]->price;
                    return [
                        'id_order' => $order->id,
                        'id_service' => $item['id_service'],
                        'qty' => $item['qty'],
                        'subtotal' => $price * $item['qty'],
                        'notes' => $item['notes'],
                    ];
                });

                TransOrderDetails::insert($orderDetails->toArray());

                return $order;
            });

            return redirect()
                ->route('orders.print', $order->id)
                ->with('success_message', 'Order berhasil dibuat!!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error_message', 'Terjadi kesalahan saat membuat order: ' . $e->getMessage());
        }
    }

    public function print(string $id)
    {
        try {
            $order = TransOrders::with(['customer', 'transOrderDetails.typeOfService'])
                ->findOrFail($id);

            session()->reflash();

            return view('orders.print', compact('order'));
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with('error_message', 'Gagal memuat struk: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $order = TransOrders::with([
                'customer' => fn($q) => $q->withTrashed(),
                'transOrderDetails.typeOfService',
                'transLaundryPickups'
            ])->findOrFail($id);

            return view('orders.show', compact('order'));
        } catch (\Throwable $e) {
            return redirect()
                ->route('orders.index')
                ->with('error_message', 'Gagal memuat detail order: ' . $e->getMessage());
        }
    }


    public function edit(string $id)
    {
        try {
            $order = TransOrders::with('transOrderDetails')->findOrFail($id);
            $customers = Customers::select('id', 'customer_name', 'phone')->get();
            $services = TypeOfServices::select('id', 'service_name', 'price')->get();

            return view('orders.edit', compact('order', 'customers', 'services'));
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with('error_message', 'Gagal memuat form edit: ' . $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    }

    /**
     * Selesaikan Order (hanya jika pembayaran cukup)
     */
    public function complete(Request $request, string $id)
    {
        $request->validate([
            'order_pay' => 'required|numeric|min:0',
            'notes' => 'nullable|string', // optional untuk pickup
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $order = TransOrders::findOrFail($id);

                if ($request->order_pay < $order->total) {
                    throw new \Exception('Pembayaran tidak mencukupi!');
                }

                $orderChange = $request->order_pay - $order->total;

                // Update status order
                $order->update([
                    'order_pay' => $request->order_pay,
                    'order_change' => $orderChange,
                    'order_status' => 1, // selesai
                ]);

                TransLaundryPickups::create([
                    'id_order' => $order->id,
                    'id_customer' => $order->id_customer,
                    'pickup_date' => Carbon::now(),
                    'notes' => $request->notes ?? null,
                ]);
            });

            return redirect()->route('orders.index')
                ->with('success_message', 'Order berhasil diselesaikan, pembayaran dicatat, dan pickup tersimpan.');
        } catch (\Exception $e) {
            return redirect()->route('orders.show', $id)
                ->with('error_message', 'Gagal menyelesaikan order: ' . $e->getMessage());
        }
    }

    // New
    public function showTransaction()
    {
        $customers = Customers::get();
        $services = TypeOfServices::get();
        return view('orders.transaction', compact('customers', 'services'));
    }
    public function OrderStore(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'customer.id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'total' => 'required|numeric',
            'order_date' => 'required|date',
            'order_status' => 'required|bool'
        ]);

        DB::beginTransaction();
        try {
            // Simpan Order
            $order = TransOrders::create([
                'order_code' => $request->id,
                'id_customer' => $request->customer['id'],
                'order_date' => $request->order_date,
                'order_status' => $request->order_status,
                'total' => $request->total
            ]);

            // Simpan Detail Orders
            foreach ($request->items as $item) {
                TransOrderDetails::create([
                    'id_order' => $order->id,
                    'id_service' => $item['id_service'],
                    'qty' => $item['weight'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'notes' => $item['notes'] ?? null
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => $order->load('transOrderDetails.typeOfService', 'customer')
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal Menyimpan Transaksi',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function getAllDataOrders()
    {
        $transactions = TransOrders::with(['customer', 'transOrderDetails.typeOfService'])->get();
        $data = $transactions->map(function ($trx) {
            return [
                'id' => $trx->order_code,
                'customer' => [
                    'id' => $trx->customer->id ?? null,
                    'name' => $trx->customer->customer_name ?? null,
                    'phone' => $trx->customer->phone ?? null,
                    'address' => $trx->customer->address ?? null,
                ],
                'items' => $trx->transOrderDetails->map(function ($orderDetail) {
                    return [
                        'id' => $orderDetail->id,
                        'service' => $orderDetail->typeOfService->service_name ?? null,
                        'weight' => $orderDetail->qty,
                        'price' => $orderDetail->typeOfService->price,
                        'subtotal' => $orderDetail->subtotal,
                        'notes' => $orderDetail->notes ?? null
                    ];
                }),
                'total' => $trx->total,
                'date' => $trx->order_date,
                'status' => $trx->getStatusTextAttribute(),
                'order_status' => $trx->order_status,
            ];
        });
        return response()->json($data);
    }
    public function pickupLaundry(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:0,1' // misalnya ada banyak status
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $order = TransOrders::where('order_code', $id)->firstOrFail();

                $order->update([
                    'order_pay' => $order->total,
                    'order_status' => $request->order_status,
                    'order_end_date' => Carbon::now()->toDateString()
                ]);

                if ($request->order_status == 1) {
                    TransLaundryPickups::create([
                        'id_order' => $order->id,
                        'id_customer' => $order->id_customer,
                        'pickup_date' => Carbon::now(),
                        'notes' => $request->notes ?? null,
                    ]);
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Status berhasil diupdate',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error' . $th->getMessage(),
            ], 500);
        }

    }
}