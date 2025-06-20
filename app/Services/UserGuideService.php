<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class UserGuideService
{
    private $guidePath;
    private $guideContent;
    private const CACHE_KEY = 'module_guides';
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct()
    {
        $this->guidePath = public_path('assets/user-guide/module-guides.json');
        $this->loadGuideContent();
    }

    private function loadGuideContent()
    {
        $this->guideContent = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            if (file_exists($this->guidePath)) {
                $content = file_get_contents($this->guidePath);
                return json_decode($content, true);
            }
            return [];
        });
    }

    public function getModuleGuide($role)
    {
        return $this->guideContent[$role] ?? null;
    }

    public function getAllModules()
    {
        return array_keys($this->guideContent);
    }

    public function moduleExists($role, $module)
    {
        return isset($this->guideContent[$role][$module]);
    }

    public function getModuleSections($role)
    {
        if (!isset($this->guideContent[$role])) {
            return [];
        }

        // Remove 'title' from sections list
        $sections = array_keys($this->guideContent[$role]);
        return array_filter($sections, function($key) {
            return $key !== 'title';
        });
    }

    public function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
        $this->loadGuideContent();
    }
} 