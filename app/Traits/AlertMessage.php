<?php

namespace App\Traits;

use RealRashid\SweetAlert\Facades\Alert;

trait AlertMessage
{
    public function __construct()
    {
        if (session('success_message')) {
            Alert::success('Berhasil!!', session('success_message'));
        }
        if (session('error_message')) {
            Alert::error('Gagal!!', session('error_message'));
        }
    }
    public function confirmDeleteCustomized($title, $text)
    {
        confirmDelete($title, $text);
    }
}