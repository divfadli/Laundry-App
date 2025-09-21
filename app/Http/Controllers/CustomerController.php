<?php

namespace App\Http\Controllers;

use App\Traits\AlertMessage;
use Illuminate\Http\Request;
use App\Models\Customers;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    use AlertMessage;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $customers = Customers::latest()->get();

            // Jika confirmDeleteCustomized menambahkan flash atau JS, tetap bisa dipanggil
            $this->confirmDeleteCustomized("Hapus Data Customer", "Yakin ingin menghapus data customer ini?");

            return view('customers.index', compact('customers'));
        } catch (Exception $e) {
            Log::error("Error saat menampilkan daftar customer: " . $e->getMessage());
            return redirect()->back()->with('error_message', 'Gagal menampilkan data customer.');
        }
    }

    public function create()
    {
        return view('customers.operator.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|max:50',
                'phone' => 'required|string|max:13',
                'address' => 'required|string'
            ]);

            Customers::create($request->all());

            if (Auth::user()->isOperator()) {
                return redirect()->route('orders.create')->with('success_message', 'Customer berhasil ditambahkan');
            }

            return redirect()->route('customers.index')->with('success_message', 'Customer berhasil ditambahkan');
        } catch (Exception $e) {
            Log::error("Error saat menambahkan customer: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Gagal menambahkan customer.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $customer = Customers::with('transOrders.transOrderDetails.typeOfService')
                ->findOrFail($id);

            return view('customers.show', compact('customer'));
        } catch (Exception $e) {
            Log::error("Error saat menampilkan detail customer: " . $e->getMessage());
            return redirect()->route('customers.index')->with('error_message', 'Terjadi kesalahan saat menampilkan data customer.');
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|max:50',
                'phone' => 'required|string|max:13',
                'address' => 'required|string'
            ]);

            $customer = Customers::findOrFail($id);
            $customer->update($request->all());

            return redirect()->route('customers.index')->with('success_message', 'Customer berhasil diupdate');
        } catch (Exception $e) {
            Log::error("Error saat update customer: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Gagal mengupdate customer.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $customer = Customers::findOrFail($id);
            $customer->delete();

            return redirect()->route('customers.index')->with('success_message', 'Customer berhasil dihapus');
        } catch (Exception $e) {
            Log::error("Error saat menghapus customer: " . $e->getMessage());
            return redirect()->route('customers.index')->with('error_message', 'Gagal menghapus customer.');
        }
    }
}