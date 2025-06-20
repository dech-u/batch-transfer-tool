<?php

namespace dacoto\LaravelWizardInstaller\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class InstallSetMigrationsController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true, '--seed' => config('installer.database.seeders', false)]);
            return redirect()->route('LaravelWizardInstaller::install.keys');
        } catch (Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
}
