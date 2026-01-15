<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Create a new component instance.
     */
    public string $variant;
    public string $type;

    /**
     * Create a new component instance.
     */
    public function __construct($variant = 'primary', $type = 'button')
    {
        $this->variant = $variant;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
