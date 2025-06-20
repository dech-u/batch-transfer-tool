<?php

use dacoto\LaravelWizardInstaller\Controllers\InstallDatabaseController;
use dacoto\LaravelWizardInstaller\Controllers\InstallFinishController;
use dacoto\LaravelWizardInstaller\Controllers\InstallFolderController;
use dacoto\LaravelWizardInstaller\Controllers\InstallIndexController;
use dacoto\LaravelWizardInstaller\Controllers\InstallKeysController;
use dacoto\LaravelWizardInstaller\Controllers\InstallMigrationsController;
use dacoto\LaravelWizardInstaller\Controllers\InstallServerController;
use dacoto\LaravelWizardInstaller\Controllers\InstallSetDatabaseController;
use dacoto\LaravelWizardInstaller\Controllers\InstallSetKeysController;
use dacoto\LaravelWizardInstaller\Controllers\InstallSetMigrationsController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'install',
    'as' => 'LaravelWizardInstaller::',
    'namespace' => 'dacoto\LaravelWizardInstaller\Controllers',
    'middleware' => ['web', 'installer']
], static function () {
    Route::get('/', ['as' => 'install.index', 'uses' => InstallIndexController::class]);
    Route::get('/server', ['as' => 'install.server', 'uses' => InstallServerController::class]);
    Route::get('/folders', ['as' => 'install.folders', 'uses' => InstallFolderController::class]);
    Route::get('/database', ['as' => 'install.database', 'uses' => InstallDatabaseController::class]);
    Route::post('/database', ['uses' => InstallSetDatabaseController::class]);
    Route::get('/migrations', ['as' => 'install.migrations', 'uses' => InstallMigrationsController::class]);
    Route::post('/migrations', ['uses' => InstallSetMigrationsController::class]);
    Route::get('/keys', ['as' => 'install.keys', 'uses' => InstallKeysController::class]);
    Route::post('/keys', ['uses' => InstallSetKeysController::class]);
    Route::get('/finish', ['as' => 'install.finish', 'uses' => InstallFinishController::class]);
});
