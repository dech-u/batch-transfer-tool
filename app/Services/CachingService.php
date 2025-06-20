<?php

namespace App\Services;

use App\Repositories\Languages\LanguageInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\Semester\SemesterInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use stdClass;

class CachingService {

    /**
     * @param $key
     * @param callable $callback - Callback function must return a value
     * @param int $time = 3600
     * @return mixed
     */
    public function systemLevelCaching($key, callable $callback, int $time = 3600) {
        return Cache::remember($key, $time, $callback);
    }

    /**
     * @param array|string $key
     * @return mixed|string
     */
    public function getSystemSettings(array|string $key = '*') {
        $systemSettings = app(SystemSettingInterface::class);
        $settings = $this->systemLevelCaching(config('constants.CACHE.SYSTEM.SETTINGS'), function () use ($systemSettings) {
            return $systemSettings->all()->pluck('data', 'name');
        });
        if (($key != '*')) {
            /* There is a minor possibility of getting a specific key from the $systemSettings
             * So I have not fetched Specific key from DB. Otherwise, Specific key will be fetched here
             * And it will be appended to the cached array here
             */
            $specificSettings = [];

            // If array is given in Key param
            if (is_array($key)) {
                foreach ($key as $row) {
                    if ($settings && is_array($settings) && array_key_exists($row, $settings)) {
                        $specificSettings[$row] = $settings[$row] ?? '';
                    }
                }
                return $specificSettings;
            }

            // If String is given in Key param
            if ($settings && is_object($settings) && $settings->has($key)) {
                return $settings[$key] ?? '';
            }

            return "";
        }
        return $settings;
    }

    public function getLanguages() {
        $languages = app(LanguageInterface::class);
        return $this->systemLevelCaching(config('constants.CACHE.SYSTEM.LANGUAGE'), function () use ($languages) {
            return $languages->all();
        });
    }

    /**
     * @param $key
     * @param callable $callback
     * @param null $schoolId
     * @param int $time
     * @return mixed
     */
    public function schoolLevelCaching($key, callable $callback, $schoolId = null, int $time = 900) {
        if($schoolId){
            $key .= "_" . $schoolId;
        }else{
            $key .= "_" . Auth::user()->school_id;
        }

        return Cache::remember($key, $time, $callback);
    }

    /**
     * @param array|string $key
     * @param null $schoolID
     * @return mixed|string
     */
    public function getSchoolSettings(array|string $key = '*', $schoolID = null) {
        $schoolSettings = app(SchoolSettingInterface::class);
        $schoolID = (!empty($schoolID)) ? $schoolID : Auth::user()->school_id;
        $settings = $this->schoolLevelCaching(config('constants.CACHE.SCHOOL.SETTINGS'), function () use ($schoolSettings, $schoolID) {
            return $schoolSettings->builder()->where('school_id', $schoolID)->get()->pluck('data', 'name');
        },$schoolID);
        if (($key[0] != '*')) {
            /* There is a minor possibility of getting a specific key from the $systemSettings
             * So I have not fetched Specific key from DB. Otherwise, Specific key will be fetched here
             * And it will be appended to the cached array here
             */

            // If array is given in Key param
            if (is_array($key)) {
                $specificSettings = new stdClass();
                foreach ($key as $row) {
                    if ($settings && is_object($settings) && $settings->has($row)) {
                        $specificSettings->$row = $settings->get($row) ?? '';
                    }
                }
                return $specificSettings;
            }

            // If String is given in Key param
            if ($settings && is_object($settings) && $settings->has($key)) {
                return $settings->get($key);
            }

            return "";
        }
        return $settings;
    }

    public function removeSchoolCache($key, $schoolID = null) {
        if ($schoolID) {
            $key .= "_" . $schoolID;
        } else {
            $key .= "_" . Auth::user()->school_id;
        }

        Cache::forget($key);
    }

    public function removeSystemCache($key) {
        Cache::forget($key);
    }

    /**
     * @param null $schoolId
     * @return mixed
     */
    public function getDefaultSessionYear($schoolId = null) {
        $sessionYear = app(SessionYearInterface::class);
        return $this->schoolLevelCaching(config('constants.CACHE.SCHOOL.SESSION_YEAR'), function () use ($sessionYear, $schoolId) {
            return $sessionYear->default($schoolId);
        },$schoolId);
    }
    public function getDefaultSemesterData($schoolId = null) {
        $semester = app(SemesterInterface::class);
        $timetable = $this->schoolLevelCaching(config('constants.CACHE.SCHOOL.SEMESTER'), function () use ($semester, $schoolId) {
            return $semester->default($schoolId);
        },$schoolId);

        /*Added empty values so that wherever the code is used, we don't need to add isset over there*/
        return empty($timetable) ? (object)[
            'id'=>null,
            "name"=>null,
            "start_month"=>null,
            "end_month"=>null,
            "school_id"=>null,
            "created_at"=>null,
            "updated_at"=>null,
            "deleted_at"=>null,
        ] : $timetable;
    }
}
