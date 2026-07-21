<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

// Kelas Induk (Base Controller) untuk seluruh Controller di dalam aplikasi ini.
// Semua controller lain wajib mewarisi (extends) kelas ini agar secara otomatis dibekali kemampuan Otorisasi dan Validasi.
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
