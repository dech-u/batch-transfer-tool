<?php

namespace App\Repositories\SchoolSetting;

use App\Models\SchoolSetting;
use App\Repositories\Saas\SaaSRepository;
use App\Services\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class SchoolSettingRepository extends SaaSRepository implements SchoolSettingInterface {

    public function __construct(SchoolSetting $model) {
        parent::__construct($model, 'school-settings');
    }

    public function getSpecificData($name) {
        $settings_data = SchoolSetting::Owner()->where('name', $name)->first();
        return $settings_data->data ?? null;
    }

    // Using Upsert Code According to System Settings Data
    public function upsert(array $payload, array $uniqueColumns, array $updatingColumn): bool {
        $payload = array_map(static function ($d) {
            $d['school_id'] = Auth::user()->school_id;
            return $d;
        }, $payload);
        $uniqueColumns[] = 'school_id';
        foreach ($payload as $column => $value) {
            // Check that $value['data'] is File , Upload File
            if ($value['data'] instanceof UploadedFile) {
                // Check the Data Exists

                $dataExists = app(SchoolSettingInterface::class)->builder()->where('name', $value['name'])->first();
                if ($dataExists) {
                    // Get the Row Attribute Of Data Of Specific $dataExists Row
                    $data = $dataExists->getAttributes()['data'];
                    //Delete the Old File
                    UploadService::delete($data);
                }
                // Upload New File
                $payload[$column]['data'] = UploadService::upload($value['data'], $this->uploadFolder);
            }
        }
        return $this->defaultModel()->upsert($payload, $uniqueColumns, $updatingColumn);
    }

    public function getBulkData($array) {
        $schoolSettingsData = SchoolSetting::Owner()->whereIn('name', $array)->get();
        foreach ($schoolSettingsData as $row) {
            $data[$row->name] = $row->data;
        }
        return $data ?? null;
    }
}
