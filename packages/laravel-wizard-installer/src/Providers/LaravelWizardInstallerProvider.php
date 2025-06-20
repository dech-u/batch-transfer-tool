<?php

namespace dacoto\LaravelWizardInstaller\Providers;

use dacoto\LaravelWizardInstaller\Middleware\InstallerMiddleware;
use dacoto\LaravelWizardInstaller\Middleware\ToInstallMiddleware;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelWizardInstallerProvider extends ServiceProvider
{
    public function register(): void
    {
        $configPath = __DIR__.'/../../config/installer.php';
        $this->mergeConfigFrom($configPath, 'installer');
        $this->publishes([$configPath => config_path('installer.php')], 'config');

        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }

    public function boot(Router $router, Kernel $kernel): void
    {
        $kernel->prependMiddlewareToGroup('web', ToInstallMiddleware::class);
        $router->pushMiddlewareToGroup('installer', InstallerMiddleware::class);

        $viewPath = __DIR__.'/../../resources/views';
        $this->loadViewsFrom($viewPath, 'installer');
        $this->publishes([
            $viewPath => base_path('resources/views/vendor/installer'),
        ], 'views');

        $this->publishes([__DIR__.'/../../public' => public_path('vendor/wizard-installer')], 'laravel-assets');
    }
}
