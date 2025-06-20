<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Migration all table commons
        $this->createCommonsTables();

        // Step 2: Perform the data migration (data swap) after all tables are created
        $this->migrateData();

        Schema::dropIfExists('extra_student_datas');
    }

    /**
     * Create the common tables
     */
    private function createCommonsTables(): void
    {
      
        if (!Schema::hasColumn('users', 'two_factor_enabled') && !Schema::hasColumn('users', 'two_factor_secret') && !Schema::hasColumn('users', 'two_factor_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                // Add the required columns
                $table->tinyInteger('two_factor_enabled')->default(1)->after('email_verified_at');
                $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
                $table->string('two_factor_expires_at')->nullable()->after('two_factor_secret');
            });
        }

        // extra_student_datas table drop and create extra_user_datas table 
        if(!Schema::hasTable('extra_user_datas')) {
            Schema::create('extra_user_datas', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreignId('form_field_id')->references('id')->on('form_fields')->onDelete('cascade');
                $table->text('data')->nullable();
                $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Add user_type to form_fields table

        if(!Schema::hasColumn('form_fields', 'user_type')) {
            Schema::table('form_fields', function (Blueprint $table) {
                $table->integer('user_type')->default(1)->after('school_id')->comment('1 => Student, 2 => Teacher/Staff');
            });
        }
        
        if (DB::select("SHOW INDEXES FROM form_fields WHERE Key_name = 'name'")) {
            Schema::table('form_fields', function (Blueprint $table) {
                $table->dropIndex('name'); // Drop the index
            });
        }
        
        // Create 'lesson_commons' table
        if (!Schema::hasTable('lesson_commons')) {
            Schema::create('lesson_commons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
                $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Create 'topic_commons' table
        if (!Schema::hasTable('topic_commons')) {
            Schema::create('topic_commons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lesson_topics_id')->constrained('lesson_topics')->onDelete('cascade');
                $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Create 'assignment_commons' table
        if (!Schema::hasTable('assignment_commons')) {
            Schema::create('assignment_commons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
                $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Create 'online_exam_commons' table
        if (!Schema::hasTable('online_exam_commons')) {
            Schema::create('online_exam_commons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('online_exam_id')->constrained('online_exams')->onDelete('cascade');
                $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Create 'online_exam_question_commons' table
        if (!Schema::hasTable('online_exam_question_commons')) {
            Schema::create('online_exam_question_commons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('online_exam_question_id')->constrained('online_exam_questions')->onDelete('cascade');
                $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
                $table->timestamps();
            });
        }

        Schema::table('exams', function (Blueprint $table) {
            $table->date('last_result_submission_date')->nullable()->after('end_date');
        });
    }

    /**
     * Perform the data migration (data swap)
     */
    private function migrateData(): void
    { 
            \Log::info('Current Database: ' . DB::connection('school')->getDatabaseName());
            
            $tables = DB::select('SHOW TABLES');
            
            // dd($tables,DB::connection('school')->getDatabaseName());
            $commonsTables = [
                'lesson_commons', 
                'topic_commons', 
                'assignment_commons', 
                'online_exam_commons', 
                'online_exam_question_commons',
                // 'announcement_commons'
            ];

            foreach ($commonsTables as $commonsTable) {
                // Handle data migration for each commons table
                $this->migrateTableData($commonsTable, $tables);
            }
        // }
    }

    /**
     * Migrate data for each common table
     */
    private function migrateTableData(string $commonsTable, array $tables): void
    {
        foreach ($tables as $table) {
            $tableName = current((array) $table);

            if (Schema::hasTable($tableName) && Schema::hasTable($commonsTable) && $tableName == $commonsTable) {
                if(DB::table($tableName)->count() == 0) {
                    $mainTableName = str_replace('_common', '', $tableName);
                    if ($commonsTable == 'topic_commons') {
                        $mainTableName = 'lesson_topics';
                    }
    
                    $tableData = [];
                    $tableData = DB::connection('school')->table($mainTableName)->get();
    
                    if($commonsTable == 'topic_commons') {
                        $tableDatalessons = DB::connection('school')->table('lessons')->get();
                        $lessonData = array();
                        foreach ($tableDatalessons as $lesson) {
                            foreach ($tableData as $data) {
                                if($lesson->id == $data->lesson_id) {
                                    $lessonData[] = array_merge($tableData->toArray(), ['class_section_id' => $lesson->class_section_id]);
                                }
                            }
                        }
                        foreach ($lessonData as $data) {
                            // Insert data into the corresponding common table
                            $this->insertIntoCommonsTable($commonsTable, $data);
                        }
                    } else {
                        foreach ($tableData as $data) {
                            // Insert data into the corresponding common table
                            $this->insertIntoCommonsTable($commonsTable, $data);
                        }
                    }
                }
            }

            // extra_student_datas to extra_user_datas data migration
            if ($tableName == 'extra_student_datas') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $tableData = DB::connection('school')->table('extra_student_datas')->get();
                DB::beginTransaction();

                try {
                    foreach ($tableData as $data) {
                        $data = (array) $data;
                        unset($data['id']);
                        $data['user_id'] =  $data['student_id'];
                        unset($data['student_id']);
                        DB::table('extra_user_datas')->insert($data);
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }

                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
               
            }
        }
    }

    /**
     * Insert data into common tables based on table type
     */
    private function insertIntoCommonsTable(string $commonsTable, $data): void
    {
        $commonData = [
            'created_at' => now(),
            'updated_at' => now(),
        ];

        switch ($commonsTable) {
            case 'lesson_commons':
                $commonData['lesson_id'] = $data->id;
                $commonData['class_section_id'] = $data->class_section_id;
                break;
            case 'topic_commons':
                $commonData['lesson_topics_id'] = $data[0]->id;
                $commonData['class_section_id'] = $data['class_section_id'];
                break;
            case 'assignment_commons':
                $commonData['assignment_id'] = $data->id;
                $commonData['class_section_id'] = $data->class_section_id;
                break;
            case 'online_exam_commons':
                $commonData['online_exam_id'] = $data->id;
                $commonData['class_section_id'] = $data->class_section_id;
                break;
            case 'online_exam_question_commons':
                $commonData['online_exam_question_id'] = $data->id;
                $commonData['class_section_id'] = $data->class_section_id;
                break;
        }

        DB::table($commonsTable)->insert($commonData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the common tables
        Schema::dropIfExists('lesson_commons');
        Schema::dropIfExists('topic_commons');
        Schema::dropIfExists('assignment_commons');
        Schema::dropIfExists('online_exam_commons');
        Schema::dropIfExists('online_exam_question_commons');
        // Schema::dropIfExists('announcement_commons');
        
        // Remove columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_enabled', 'two_factor_secret', 'two_factor_expires_at']);
        });

      
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
        
        Schema::table('form_fields', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::create('extra_student_datas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('form_field_id')->references('id')->on('form_fields')->onDelete('cascade');
            $table->text('data')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
            
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('last_result_submission_date');

        });
    }
};
