<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextInput extends Component
{
    // Inisialisasi komponen.
    public function __construct()
    {
        //
    }

    // Render view komponen text-input.
    public function render(): View|Closure|string
    {
        return view('components.text-input');
    }
}
