# Laravel wizard installer package

![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/dacoto/laravel-wizard-installer/CI/master)
![GitHub](https://img.shields.io/github/license/dacoto/laravel-wizard-installer)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/dacoto/laravel-wizard-installer)

A Laravel wizard installer package for Laravel 9.x and higher.

## Installation

Require this package:

```
composer require dacoto/laravel-wizard-installer
```

You need to publish the config file for this package. This will add the file `config/installer.php`, where you can configure this package.

```
php artisan vendor:publish --provider="dacoto\LaravelWizardInstaller\Providers\LaravelWizardInstallerProvider" --tag=config
```

In order to edit the default template, the views must be published as well. The views will then be placed in `resources/views/vendor/wizzard-installer`.

```
php artisan vendor:publish --provider="dacoto\LaravelWizardInstaller\Providers\LaravelWizardInstallerProvider" --tag=views
```
