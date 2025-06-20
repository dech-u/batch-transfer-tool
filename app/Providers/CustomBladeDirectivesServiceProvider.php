<?php

namespace App\Providers;

use App\Services\FeaturesService;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class CustomBladeDirectivesServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {

        $this->callAfterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $this->registerBladeExtensions($bladeCompiler);
        });

    }

    protected function registerBladeExtensions($bladeCompiler) {

        $bladeCompiler->directive('hasFeature', function ($arguments) {
            return "<?php if(\\App\Services\FeaturesService::hasFeature($arguments)): ?>";
        });
        $bladeCompiler->directive('endHasFeature', function () {
            return '<?php endif; ?>';
        });


        $bladeCompiler->directive('hasNotFeature', function ($arguments) {
            return "<?php if(!\\App\Services\FeaturesService::hasFeature($arguments)): ?>";
        });
        $bladeCompiler->directive('endHasNotFeature', function () {
            return '<?php endif; ?>';
        });

        $bladeCompiler->directive('hasAnyFeature', function ($arguments) {
            return "<?php if(\App\Services\FeaturesService::hasAnyFeature($arguments)): ?>";
        });
        $bladeCompiler->directive('endHasAnyFeature', function () {
            return '<?php endif; ?>';
        });

        $bladeCompiler->directive('hasAllFeatures', function ($arguments) {
            return "<?php if(\App\Services\FeaturesService::hasAllFeature($arguments)): ?>";
        });
        $bladeCompiler->directive('endHasAllFeatures', function () {
            return '<?php endif; ?>';
        });

        /** This will function directly return True/False **/
        $bladeCompiler->directive('hasFeatureAccess', function (string $arguments) {
            $arguments = str_replace(["'", '"'], '', $arguments);
            return FeaturesService::hasFeature($arguments) ? 'true' : 'false';
        });

        $bladeCompiler->directive('hasAnyFeatureAccess', function ($arguments) {
            $arguments = str_replace(array('[', ']', "'", '"'), '', $arguments);
            $arguments = explode(',', $arguments);
            return FeaturesService::hasAnyFeature($arguments) ? 'true' : 'false';
        });
    }
}
