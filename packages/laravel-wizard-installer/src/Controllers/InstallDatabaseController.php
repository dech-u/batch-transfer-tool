<?php

namespace dacoto\LaravelWizardInstaller\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class InstallDatabaseController extends Controller
{
    public function __invoke(): View|Factory|Application|RedirectResponse
    {
        if (!env('APPSECRET') ||
            !(new InstallServerController())->check() ||
            !(new InstallFolderController())->check()
        ) {
            return redirect()->route('install.purchase-code.index');
        }

        return view('installer::steps.database');
    }
}
