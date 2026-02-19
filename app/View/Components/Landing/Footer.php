<?php

namespace App\View\Components\Landing;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    // Inisialisasi komponen.
    public function __construct()
    {
        //
    }

    // Render view komponen landing footer.
    public function render(): View|Closure|string
    {
        return view('components.landing.footer');
    }
}
