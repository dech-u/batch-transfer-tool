<?php

use App\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn('other');
        });

        // Add file_upload_size_limit to System Setting
        $data = SystemSetting::where('name', 'file_upload_size_limit')->first();
        if(!$data) {
            $systemSettings = [
                [
                    'name' => 'file_upload_size_limit',
                    'data' => '2',
                    'type' => 'string'
                ],       
            ];
    
            SystemSetting::upsert($systemSettings, ["name"], ["data","type"]);
            Cache::flush();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->text('other')->nullable()->comment('extra HTML attributes');
        });
    }
};
