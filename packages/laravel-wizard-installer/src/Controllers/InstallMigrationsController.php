<?php

namespace dacoto\LaravelWizardInstaller\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class InstallMigrationsController extends Controller
{
    public function __invoke(): View|Factory|Application|RedirectResponse
    {
        if (!env('APPSECRET')) {
            return redirect()->route('install.purchase-code.index');
        }
        if (
            !DB::connection()->getPdo() ||
            !(new InstallServerController())->check() ||
            !(new InstallFolderController())->check()
        ) {
            return redirect()->route('LaravelWizardInstaller::install.database');
        }

        return view('installer::steps.migrations');
    }
}
