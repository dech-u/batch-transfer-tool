<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WizardSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $is_wizard_checkMark = SystemSetting::where('name', 'wizard_checkMark')->first();
            if (isset($is_wizard_checkMark) && $is_wizard_checkMark->data == "0" && $request->is('dashboard') ) {
                return redirect()->route('wizard-settings.index');
            }
        } catch (\Exception $e) {
            
        }

        return $next($request);
    }
}
