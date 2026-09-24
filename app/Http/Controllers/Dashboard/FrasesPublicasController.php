<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class FrasesPublicasController extends Controller
{
    public function index()
    {
        return view('dashboard.frases-publicas.index');
    }
}
