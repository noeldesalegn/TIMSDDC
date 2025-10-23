<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaxpayerController extends Controller
{
        public function index()
    {
        return view('taxpayer.dashboard');
    }
}
