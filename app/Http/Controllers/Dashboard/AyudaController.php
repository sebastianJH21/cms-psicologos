<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class AyudaController extends Controller
{
    public function index()
    {
        return view('dashboard.ayuda.index');
    }
}
