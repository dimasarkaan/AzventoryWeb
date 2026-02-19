<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Admin.
     */
    public function index()
    {
        return view('dashboard.admin');
    }
}
