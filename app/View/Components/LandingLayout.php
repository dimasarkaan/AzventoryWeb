<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LandingLayout extends Component
{
    // Inisialisasi komponen.
    public function __construct()
    {
        //
    }

    // Render view komponen landing-layout.
    public function render(): View|Closure|string
    {
        return view('components.landing-layout');
    }
}
