<?php

namespace dacoto\LaravelWizardInstaller\Components;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SuccessCheckIconComponent extends Component
{
    public function render(): View|Factory|Htmlable|Closure|string|Application
    {
        return view('installer::components.success-check-icon');
    }
}
