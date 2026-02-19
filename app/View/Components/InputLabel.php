<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputLabel extends Component
{
    // Inisialisasi komponen.
    public function __construct()
    {
        //
    }

    // Render view komponen input-label.
    public function render(): View|Closure|string
    {
        return view('components.input-label');
    }
}
