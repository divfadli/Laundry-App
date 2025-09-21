<?php

namespace App\Http\Controllers;

use App\Traits\AlertMessage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use AlertMessage;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        try {
            // Validasi input
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'min:8'],
            ]);

            // Coba autentikasi
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return back()->with('success_message', 'Login berhasil!')->with('redirect_to', route('dashboard'));
            }
            // Jika gagal autentikasi
            return back()
                ->with('error_message', 'Email atau password salah.')
                ->withInput($request->only('email'));

        } catch (Exception $e) {
            // Tangkap error tak terduga
            return back()
                ->with('error_message', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput($request->only('email'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}