<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\CachingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;
use ZipArchive;

class SystemUpdateController extends Controller {
    private string $destinationPath;
    private CachingService $cache;

    public function __construct(CachingService $cachingService) {
        $this->destinationPath = base_path() . '/update/tmp/';
        $this->cache = $cachingService;
    }

    public function index() {
        if (!Auth::user()->hasRole('Super Admin')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $system_version = SystemSetting::where('name', 'system_version')->first();
        return view('system-update.index', compact('system_version'));
    }

    public function update(Request $request) {
        if (!Auth::user()->hasRole('Super Admin')) {
            $response = array(
                'error'   => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        $validator = Validator::make($request->all(), [
            'purchase_code' => 'required',
            'file'          => 'required|file|mimes:zip',
        ]);

        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            $app_url = (string)url('/');
            $app_url = preg_replace('#^https?://#i', '', $app_url);
            $current_version = SystemSetting::where('name', 'system_version')->first()['data'];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            => 'https://wrteam.in/validator/eschoolsaas_validator?purchase_code=' . $request->purchase_code . '&domain_url=' . $app_url,
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
                ResponseService::errorResponse($response["message"]);
            }

            if (!is_dir($this->destinationPath) && !mkdir($concurrentDirectory = $this->destinationPath, 0777, TRUE) && !is_dir($concurrentDirectory)) {
//                sprintf('Directory "%s" was not created', $concurrentDirectory)
                ResponseService::errorResponse("Permission Error while crating Temp Directory");
            }

            // zip upload
            $zipfile = $request->file('file');
            $fileName = $zipfile->getClientOriginalName();
            $zipfile->move($this->destinationPath, $fileName);
            //This will add public in path
//$target_path = getcwd() . DIRECTORY_SEPARATOR;

            $target_path = base_path() . DIRECTORY_SEPARATOR;

            $zip = new ZipArchive();
            $filePath = $this->destinationPath . '/' . $fileName;
            $zipStatus = $zip->open($filePath);
            if ($zipStatus !== TRUE) {
                ResponseService::errorResponse('something_wrong_try_again');
            }

            $zip->extractTo($this->destinationPath);
            $zip->close();
            unlink($filePath);

            $ver_file = $this->destinationPath . 'version_info.php';
            $source_path = $this->destinationPath . 'source_code.zip';
            if (!file_exists($ver_file) && !file_exists($source_path)) {
                ResponseService::errorResponse('Zip File is not Uploaded to Correct Path');
            }
            $ver_file1 = $target_path . 'version_info.php';
            $source_path1 = $target_path . 'source_code.zip';
            // MOVE File
            if (!rename($ver_file, $ver_file1) || !rename($source_path, $source_path1)) {
                ResponseService::errorResponse('Error Occurred while moving a Zip File');
            }

            $version_file = require($ver_file1);

            if ($current_version != $version_file['update_version']) {
                // unlink($ver_file1);
                // unlink($source_path1);
                // ResponseService::errorResponse('System is already upto date');
                if ($current_version != $version_file['current_version']) {
                    unlink($ver_file1);
                    unlink($source_path1);
                    ResponseService::errorResponse($current_version . ' ' . trans('Please update nearest version first'));
                }
            }

            

            $zip1 = new ZipArchive();
            $zipFile1 = $zip1->open($source_path1);

            if ($zipFile1 !== TRUE) {
                unlink($ver_file1);
                unlink($source_path1);
                ResponseService::errorResponse('Source Code Zip Extraction Failed');
            }

            $zip1->extractTo($target_path);
            $zip1->close();

            Artisan::call('migrate');
            Artisan::call('db:seed --class=InstallationSeeder');

            unlink($source_path1);
            unlink($ver_file1);

            SystemSetting::where('name', 'system_version')->update([
                'data' => $version_file['update_version']
            ]);

            $wizardSettings = [
                [
                    'name' => 'wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ],
                [
                    'name' => 'system_settings_wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ],
                [
                    'name' => 'notification_settings_wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ],
                [
                    'name' => 'email_settings_wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ],
                [
                    'name' => 'verify_email_wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ],
                [
                    'name' => 'email_template_settings_wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ],
                [
                    'name' => 'payment_settings_wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ],
                [
                    'name' => 'third_party_api_settings_wizard_checkMark',
                    'data' => 1,
                    'type' => 'integer'
                ]
            ];
    
            SystemSetting::upsert($wizardSettings, ["name"], ["data","type"]);

            $this->cache->removeSystemCache(config('constants.CACHE.SYSTEM.SETTINGS'));
            ResponseService::successResponse('System Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }
}
