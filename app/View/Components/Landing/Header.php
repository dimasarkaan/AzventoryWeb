<?php

namespace App\View\Components\Landing;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    // Inisialisasi komponen.
    public function __construct()
    {
        //
    }

    // Render view komponen landing header.
    public function render(): View|Closure|string
    {
        return view('components.landing.header');
    }
}
