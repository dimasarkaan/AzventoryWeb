<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    // Variabel publik (props).
    public string $variant;
    public string $type;

    // Inisialisasi komponen dengan varian dan tipe.
    public function __construct($variant = 'primary', $type = 'button')
    {
        $this->variant = $variant;
        $this->type = $type;
    }

    // Render view komponen button.
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
