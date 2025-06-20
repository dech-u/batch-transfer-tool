<?php

namespace dacoto\LaravelWizardInstaller\Controllers;

use dacoto\EnvSet\Facades\EnvSet;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PDO;

class InstallSetDatabaseController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if (
            !(new InstallServerController())->check() ||
            !(new InstallFolderController())->check()
        ) {
            return redirect()->route('LaravelWizardInstaller::install.folders');
        }

        try {
            $connection = new PDO(
                sprintf("mysql:host=%s:%s;dbname=%s", $request->input('database_hostname'), $request->input('database_port'), $request->input('database_name')),
                $request->input('database_username'),
                $request->input('database_password', '')
            );
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        try {
            EnvSet::setKey('DB_HOST', $request->input('database_hostname'));
            EnvSet::setKey('DB_PORT', $request->input('database_port', 3306));
            EnvSet::setKey('DB_DATABASE', $request->input('database_name'));
            EnvSet::setKey('DB_USERNAME', $request->input('database_username'));
            EnvSet::setKey('DB_PASSWORD', $request->input('database_password'));
            if ($request->input('database_prefix')) {
                EnvSet::setKey('DB_PREFIX', $request->input('database_prefix'));
            }
            EnvSet::save();
        } catch (Exception $e) {
            return back()->withErrors($e->getMessage())->withInput();
        }

        return redirect()->route('LaravelWizardInstaller::install.migrations');
    }
}
