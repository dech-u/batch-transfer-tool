<?php

use App\Models\School;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //


        if (!Schema::connection('school')->hasColumn('form_fields', 'display_on_id')) {
            // If the column doesn't exist, run the migration
            Schema::table('form_fields', static function (Blueprint $table) {
                $table->integer('display_on_id')->default(0)->comment('0 => No, 1 => Yes')->after('rank');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('form_fields', static function (Blueprint $table) {
            $table->dropColumn('display_on_id');
        });
    }
};
