<?php

namespace dacoto\LaravelWizardInstaller\Components;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LabelComponent extends Component
{
    public function __construct(
        public bool $required = false
    ) {
    }

    public function render(): View|Factory|Htmlable|Closure|string|Application
    {
        return view('installer::components.label');
    }
}
