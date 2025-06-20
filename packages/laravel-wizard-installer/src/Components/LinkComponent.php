<?php

namespace dacoto\LaravelWizardInstaller\Components;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LinkComponent extends Component
{
    public function __construct(
        public string $href,
        public string $color = 'blue',
    ) {
    }

    public function render(): View|Factory|Htmlable|Closure|string|Application
    {
        return view('installer::components.link');
    }
}
