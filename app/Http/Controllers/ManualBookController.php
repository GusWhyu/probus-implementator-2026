<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManualBookController extends Controller
{
    /**
     * Tampilan UI Manual Book & Tata Cara Penggunaan Sistem
     */
    public function index(Request $request)
    {
        return view('manualbook');
    }
}

