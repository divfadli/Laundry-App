<?php

namespace App\Http\Controllers;

use App\Traits\AlertMessage;
use Illuminate\Http\Request;
use App\Models\TypeOfServices;
use Illuminate\Support\Facades\Log;

class TypeOfServiceController extends Controller
{
    use AlertMessage;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->confirmDeleteCustomized("Hapus Data Service", "Yakin ingin menghapus data service ini?");
            $services = TypeOfServices::latest()->get();
            return view('services.index', compact('services'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch services: ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'Gagal menampilkan daftar service.' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        try {
            TypeOfServices::create($request->all());
            return redirect()->route('services.index')->with('success_message', 'Jenis service berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Failed to create service: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Gagal menambahkan service.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'service_name' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        try {
            $service = TypeOfServices::findOrFail($id);
            $service->update($request->all());
            return redirect()->route('services.index')->with('success_message', 'Jenis service berhasil diupdate');
        } catch (\Exception $e) {
            Log::error('Failed to update service: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Gagal mengupdate service.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $service = TypeOfServices::findOrFail($id);
            $service->delete();

            return redirect()->route('services.index')->with('success_message', 'Jenis service berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Failed to delete service: ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'Gagal menghapus service.');
        }
    }
}