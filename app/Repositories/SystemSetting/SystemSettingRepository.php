<?php

namespace App\Repositories\SystemSetting;

use App\Models\SystemSetting;
use App\Repositories\Base\BaseRepository;
use App\Services\UploadService;
use Illuminate\Http\UploadedFile;

class SystemSettingRepository extends BaseRepository implements SystemSettingInterface {
    protected string $uploadFolder = 'system-settings';

    public function __construct(SystemSetting $model) {
        parent::__construct($model,'system-settings');
    }

    public function getSpecificData($name) {
        $settings_data = SystemSetting::where('name', $name)->first();
        return $settings_data->data ?? null;
    }

    // Using Upsert Code According to System Settings Data
    public function upsert(array $payload, array $uniqueColumns, array $updatingColumn): bool {
        foreach ($payload as $column => $value) {
            // Check that $value['data'] is File , Upload File
            if ($value['data'] instanceof UploadedFile) {
                // Check the Data Exists
                $dataExists = SystemSetting::where('name', $value['name'])->first();
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
        $schoolSettingsData = SystemSetting::whereIn('name', $array)->get();
        foreach ($schoolSettingsData as $row) {
            if ($row->name == 'mail_port') {
                $data[$row->name] = (int)$row->data;
            } else {
                $data[$row->name] = $row->data;
            }
        }
        return $data ?? null;
    }
}
