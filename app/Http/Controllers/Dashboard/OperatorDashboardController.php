<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperatorDashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Operator.
     */
    public function index()
    {
        return view('dashboard.operator');
    }
}
