<?php

namespace dacoto\LaravelWizardInstaller\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class InstallServerController extends Controller
{
    public function __invoke(): Factory|View|Application
    {
        return view('installer::steps.server', [
            'result' => $this->check()
        ]);
    }

    public function check(): bool
    {
        foreach (config('installer.server') as $check) {
            if (!$check['check']()) {
                return false;
            }
        }

        return true;
    }
}
