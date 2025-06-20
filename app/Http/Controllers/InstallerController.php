<?php

namespace App\Http\Controllers;

use dacoto\EnvSet\Facades\EnvSet;
use dacoto\LaravelWizardInstaller\Controllers\InstallFolderController;
use dacoto\LaravelWizardInstaller\Controllers\InstallServerController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InstallerController extends Controller {
    public function purchaseCodeIndex() {
        if (!(new InstallServerController())->check() || !(new InstallFolderController())->check()) {
            return redirect()->route('LaravelWizardInstaller::install.folders');
        }
        return view('vendor.installer.steps.purchase-code');
    }


    public function checkPurchaseCode(Request $request) {
        try {
            $app_url = (string)url('/');
            $app_url = preg_replace('#^https?://#i', '', $app_url);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            => 'https://wrteam.in/validator/eschoolsaas_validator?purchase_code=' . $request->input('purchase_code') . '&domain_url=' . $app_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            if ($response['error']) {
                return view('installer::steps.purchase-code', ['error' => $response["message"]]);
            }
            
            EnvSet::setKey('APPSECRET', $request->input('purchase_code'));
            EnvSet::setKey('APP_URL', (string)url('/'));
            EnvSet::save();
            return redirect()->route('install.php-function.index');
        } catch (Exception $e) {
            $values = [
                'purchase_code' => $request->get("purchase_code"),
            ];
            return view('vendor.installer.steps.purchase-code', ['values' => $values, 'error' => $e->getMessage()]);
        }
    }

    public function phpFunctionIndex() {
        if (!(new InstallServerController())->check() || !(new InstallFolderController())->check()) {
            return redirect()->route('LaravelWizardInstaller::install.purchase_code');
        }
        return view('vendor.installer.steps.symlink_basedir_check', [
            'result' => $this->checkSymlink(),
            // 'baseDir' =>$this->checkBaseDir()
        ]);
    }

    public function checkSymlink(): bool
    {
        return function_exists('symlink');
    }
    public function checkBaseDir(): bool
    {
        $openBaseDir = ini_get('open_basedir');
        if ($openBaseDir) {
            return false;
        }
        return true;
    }

}
