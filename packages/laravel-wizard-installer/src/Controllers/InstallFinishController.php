<?php

namespace dacoto\LaravelWizardInstaller\Controllers;

use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use JsonException;

class InstallFinishController extends Controller
{
    /**
     * @throws JsonException
     */
    public function __invoke(): View|Factory|Application|RedirectResponse
    {
        if (!env('APPSECRET')) {
            return redirect()->route('install.purchase-code.index');
        }
        if (
            empty(EnvSet::getValue('APP_KEY')) ||
            !DB::connection()->getPdo() ||
            !(new InstallServerController())->check() ||
            !(new InstallFolderController())->check()
        ) {
            return redirect()->route('LaravelWizardInstaller::install.database');
        }
        $data = [
            'date' => date('Y/m/d h:i:s')
        ];
        file_put_contents(storage_path('installed'), json_encode($data, JSON_THROW_ON_ERROR), FILE_APPEND | LOCK_EX);

        Artisan::call('route:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        return view('installer::steps.finish', ['path' => (string)url('/')]);
    }
}
