<?php

namespace dacoto\LaravelWizardInstaller\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class InstallIndexController extends Controller
{
    public function __invoke(): Factory|View|Application
    {
        return view('installer::steps.welcome');
    }
}
