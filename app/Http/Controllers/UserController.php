<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Levels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Traits\AlertMessage;

class UserController extends Controller
{
    use AlertMessage;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $this->confirmDeleteCustomized("Hapus Data User", "Yakin ingin menghapus data user ini?");
            $users = User::with('level')->latest()->get();
            return view('users.index', compact('users'));
        } catch (Exception $e) {
            Log::error("Error fetching users: " . $e->getMessage());
            return redirect()->back()->with('error_message', 'Gagal mengambil data users');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $levels = Levels::all();
            return view('users.create', compact('levels'));
        } catch (Exception $e) {
            Log::error("Error fetching levels: " . $e->getMessage());
            return redirect()->back()->with('error_message', 'Gagal mengambil data levels');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_level' => 'required|exists:levels,id',
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        try {
            User::create([
                'id_level' => $request->id_level,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('users.index')->with('success_message', 'User berhasil ditambahkan');
        } catch (Exception $e) {
            Log::error("Error creating user: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Gagal menambahkan user');
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
        try {
            $user = User::findOrFail($id);
            $levels = Levels::all();
            return view('users.edit', compact('user', 'levels'));
        } catch (Exception $e) {
            Log::error("Error fetching user or levels: " . $e->getMessage());
            return redirect()->back()->with('error_message', 'Gagal membuka form edit user');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_level' => 'required|exists:levels,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $data = [
            'id_level' => $request->id_level,
            'name' => $request->name,
            'email' => $request->email
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        try {
            $user = User::findOrFail($id);
            $user->update($data);
            return redirect()->route('users.index')->with('success_message', 'User berhasil diupdate');
        } catch (Exception $e) {
            Log::error("Error updating user: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'Gagal mengupdate user');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error_message', 'Tidak dapat menghapus akun sendiri');
        }

        try {
            $user->delete();
            return redirect()->route('users.index')->with('success_message', 'User berhasil dihapus');
        } catch (Exception $e) {
            Log::error("Error deleting user: " . $e->getMessage());
            return redirect()->back()->with('error_message', 'Gagal menghapus user');
        }
    }
}