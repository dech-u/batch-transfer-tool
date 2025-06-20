<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //        set_time_limit(100);


        /************ Master Tables Started *******/
        Schema::create('schools', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('support_phone');
            $table->string('support_email');
            $table->string('tagline');
            $table->string('logo');
            $table->foreignId('admin_id')->nullable()->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->tinyInteger('status')->default(0)->comment('0 => Deactivate, 1 => Active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('roles', static function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('guard_name')->references('id')->on('schools')->onDelete('cascade');
            $table->boolean('custom_role')->after('school_id')->default(1);
            $table->boolean('editable')->after('custom_role')->default(1);
            $table->dropUnique(['name', 'guard_name']);
            $table->unique(['name', 'guard_name', 'school_id']);
        });
        Schema::table('users', static function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('fcm_id')->references('id')->on('schools')->onDelete('cascade');
        });
        /*TODO : Review this*/
        Schema::create('categories', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sections', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mediums', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('session_years', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->tinyInteger('default')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['name', 'school_id']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('semesters', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('start_month');
            $table->tinyInteger('end_month');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('languages', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->string('code', 64)->unique();
            $table->string('file', 512);
            $table->tinyInteger('status')->default(0)->comment('1=>active');
            $table->tinyInteger('is_rtl')->default(0);
            $table->timestamps();
        });
        Schema::create('sliders', static function (Blueprint $table) {
            $table->id();
            $table->string('image', 1024);
            $table->string('link')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
        /************ Master Tables Ends *******/

        Schema::create('subjects', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->string('code', 64)->nullable();
            $table->string('bg_color', 32);
            $table->string('image', 512);
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->string('type', 64)->comment('Theory / Practical');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('streams', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shifts', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time', 0);
            $table->time('end_time', 0);
            $table->integer('status')->default(1);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('classes', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->tinyInteger('include_semesters')->comment('0 - no 1 - yes')->default(0);
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->references('id')->on('shifts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stream_id')->nullable()->references('id')->on('streams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('elective_subject_groups', static function (Blueprint $table) {
            $table->id();
            $table->integer('total_subjects');
            $table->integer('total_selectable_subjects');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->references('id')->on('semesters')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('class_subjects', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->string('type', 32)->comment('Compulsory / Elective');
            $table->foreignId('elective_subject_group_id')->nullable()->comment('if type=Elective')->references('id')->on('elective_subject_groups')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->references('id')->on('semesters')->onDelete('cascade');
            $table->integer('virtual_semester_id')->virtualAs('CASE WHEN semester_id IS NOT NULL THEN semester_id ELSE 0 END');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_id', 'subject_id', 'virtual_semester_id'], 'unique_ids');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('staffs', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('qualification', 512)->nullable();
            $table->double('salary')->default(0);
            $table->timestamps();
        });
        Schema::create('class_sections', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreignId('medium_id')->references('id')->on('mediums')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_id', 'section_id', 'medium_id'], 'unique_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('students', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->string('admission_no', 512);
            $table->integer('roll_number')->nullable();
            $table->date('admission_date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('guardian_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('student_subjects', static function (Blueprint $table) {
            $table->id();

            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subject_teachers', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['class_section_id', 'class_subject_id', 'teacher_id'], 'unique_ids');
        });

        Schema::create('lessons', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->string('description', 1024)->nullable();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('lesson_topics', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->string('name', 128);
            $table->string('description', 1024)->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('assignments', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->string('name', 128);
            $table->string('instructions', 1024)->nullable();
            $table->dateTime('due_date');
            $table->integer('points')->nullable();
            $table->boolean('resubmission')->default(0);
            $table->integer('extra_days_for_resubmission')->nullable();
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('created_by')->comment('teacher_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('edited_by')->nullable()->comment('teacher_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('assignment_submissions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->text('feedback')->nullable();
            $table->integer('points')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = Pending/In Review , 1 = Accepted , 2 = Rejected , 3 = Resubmitted');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('exams', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('description', 1024)->nullable();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('publish')->default(0);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_timetables', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->float('total_marks');
            $table->float('passing_marks');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('exam_marks', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_timetable_id')->references('id')->on('exam_timetables')->onDelete('cascade');
            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->float('obtained_marks');
            $table->string('teacher_review', 1024)->nullable()->default(NULL);
            $table->boolean('passing_status')->comment('1=Pass, 0=Fail');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyText('grade')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('exam_results', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('total_marks');
            $table->float('obtained_marks');
            $table->float('percentage');
            $table->tinyText('grade');

            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('grades', static function (Blueprint $table) {
            $table->id();
            $table->integer('starting_range');
            $table->integer('ending_range');
            $table->tinyText('grade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('timetables', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_teacher_id')->nullable()->references('id')->on('subject_teachers')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->references('id')->on('subjects')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('note', 1024)->nullable();
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->enum('type', ['Lecture', 'Break']);
            $table->foreignId('semester_id')->nullable()->references('id')->on('semesters')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('announcements', static function (Blueprint $table) {
            $table->id();
            $table->string('title', 128);
            $table->string('description', 1024)->nullable();
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('academic_calendars', static function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('title', 512);
            $table->string('description', 1024)->nullable();
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('attendances', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyInteger('type')->comment('0=Absent, 1=Present');
            $table->date('date');
            $table->string('remark', 512);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('files', static function (Blueprint $table) {
            $table->id();
            $table->morphs('modal');
            $table->string('file_name', 1024)->nullable();
            $table->string('file_thumbnail', 1024)->nullable();
            $table->tinyText('type')->comment('1 = File Upload, 2 = Youtube Link, 3 = Video Upload, 4 = Other Link');
            $table->string('file_url', 1024);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('holidays', static function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('title', 128);
            $table->string('description', 1024)->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('promote_students', static function (Blueprint $table) {
            $table->id();
            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->tinyInteger('result')->default(1)->comment('1=>Pass,0=>fail');
            $table->tinyInteger('status')->default(1)->comment('1=>continue,0=>leave');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['student_id', 'class_section_id', 'session_year_id'], 'unique_columns');
            $table->softDeletes();
        });

        Schema::create('online_exams', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->string('title', 128);
            $table->bigInteger('exam_key');
            $table->integer('duration')->comment('in minutes');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('online_exam_questions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->references('id')->on('class_subjects')->onDelete('cascade');
            $table->string('question', 1024);
            $table->string('image_url', 1024)->nullable();
            $table->string('note', 1024)->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('last_edited_by')->comment('teacher_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('online_exam_question_options', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->references('id')->on('online_exam_questions')->onDelete('cascade');
            $table->string('option', 1024);
            $table->tinyInteger('is_answer')->comment('1 - yes, 0 - no');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('online_exam_question_choices', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');
            $table->foreignId('question_id')->references('id')->on('online_exam_questions')->onDelete('cascade');
            $table->integer('marks')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_online_exam_statuses', static function (Blueprint $table) {
            $table->id();
            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');
            $table->tinyInteger('status')->comment('1 - in progress 2 - completed');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('online_exam_student_answers', static function (Blueprint $table) {
            $table->id();

            // TODO : check this
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('online_exam_id')->references('id')->on('online_exams')->onDelete('cascade');
            $table->foreignId('question_id')->references('id')->on('online_exam_question_choices')->onDelete('cascade');
            $table->foreignId('option_id')->references('id')->on('online_exam_question_options')->onDelete('cascade');
            $table->date('submitted_date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_fields', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->string('type', 128)->comment('text,number,textarea,dropdown,checkbox,radio,fileupload');
            $table->boolean('is_required')->default(0);
            $table->text('default_values')->nullable()->comment('values of radio,checkbox,dropdown,etc');
            $table->text('other')->nullable()->comment('extra HTML attributes');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->integer('rank')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['name', 'school_id'], 'name');
        });


        Schema::create('extra_student_datas', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('form_field_id')->references('id')->on('form_fields')->onDelete('cascade');
            $table->text('data')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('school_settings', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('data');
            $table->string('type')->comment('datatype like string , file etc')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['name', 'school_id']);
        });

        Schema::create('system_settings', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('data');
            $table->string('type')->comment('datatype like string , file etc')->nullable();
        });

        Schema::create('class_teachers', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('teacher_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_section_id', 'teacher_id'], 'unique_id');
            $table->timestamps();
        });

        Schema::create('packages', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->string('description')->nullable(true);
            $table->string('tagline')->nullable(true);
            $table->float('student_charge', 8, 4)->default(0);
            $table->float('staff_charge', 8, 4)->default(0);
            $table->tinyInteger('status')->default(0)->comment('0 => Unpublished, 1 => Published');
            $table->tinyInteger('highlight')->default(0)->comment('0 => No, 1 => Yes');
            $table->integer('rank')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('announcement_classes', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->nullable()->unsigned()->index()->references('id')->on('announcements')->onDelete('cascade');
            $table->foreignId('class_section_id')->nullable()->unsigned()->index()->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreignId('class_subject_id')->nullable(true)->references('id')->on('class_subjects')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['announcement_id', 'class_section_id', 'school_id'], 'unique_columns');
        });

        Schema::create('features', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('is_default')->default(0)->comment('0 => No, 1 => Yes');
            $table->timestamps();
        });

        Schema::create('package_features', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->unsigned()->index()->references('id')->on('packages')->onDelete('cascade');
            $table->foreignId('feature_id')->unsigned()->index()->references('id')->on('features')->onDelete('cascade');
            $table->unique(['package_id', 'feature_id'], 'unique');
            $table->timestamps();
        });


        Schema::create('fees', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('due_date');
            $table->float('due_charges')->comment('in percentage (%)');
            $table->enum('include_fee_installments', [0, 1])->comment('0 - no, 1 - yes')->default(0);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('installment_fees', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('due_date');
            $table->integer('due_charges')->comment('in percentage (%)');
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fees_types', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('fees_classes', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('fees_type_id')->references('id')->on('fees_types')->onDelete('cascade');
            $table->float('amount');
            $table->enum('choiceable', [0, 1])->comment('0 - no, 1 - yes')->default(0);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['class_id', 'fees_type_id', 'school_id'], 'unique_ids');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payment_transactions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('amount', 8, 2);
            $table->enum('payment_gateway', [1, 2])->comment('1 - razorpay 2 - stripe');
            $table->string('order_id')->comment('order_id / payment_intent_id');
            $table->string('payment_id')->nullable(true);
            $table->string('payment_signature')->nullable(true);
            $table->enum('payment_status', [0, 1, 2])->comment('0 - failed 1 - succeed 2 - pending');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fees_paids', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('fees_id')->references('id')->on('fees')->onDelete('cascade');
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->enum('is_fully_paid', [0, 1])->comment('0 - no 1 - yes');
            $table->double('amount', 8, 2);
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->unique(['student_id', 'class_id', 'school_id', 'session_year_id'], 'unique_ids');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('compulsory_fees', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('payment_transaction_id')->nullable(true)->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->enum('type', [1, 2])->comment('1 - Full Payment , 2 - Installment Payment');
            $table->foreignId('installment_id')->nullable(true)->references('id')->on('installment_fees')->onDelete('cascade');
            $table->enum('mode', [1, 2, 3])->comment('1 - cash, 2 - cheque, 3 - online');
            $table->string('cheque_no')->nullable(true);
            $table->double('amount', 8, 2);
            $table->double('due_charges', 8, 2)->nullable(true);
            $table->foreignId('fees_paid_id')->nullable(true)->references('id')->on('fees_paids')->onDelete('cascade');
            $table->enum('status', [1, 2])->comment('1 - succeed 2 - pending');
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('optional_fees', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->comment('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreignId('payment_transaction_id')->nullable(true)->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->foreignId('fees_class_id')->nullable(true)->references('id')->on('fees_classes')->onDelete('cascade');
            $table->enum('mode', [1, 2, 3])->comment('1 - cash, 2 - cheque, 3 - online');
            $table->string('cheque_no')->nullable(true);
            $table->double('amount', 8, 2);
            $table->foreignId('fees_paid_id')->nullable(true)->references('id')->on('fees_paids')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->enum('status', [1, 2])->comment('1 - succeed 2 - pending');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subscriptions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->string('name');
            $table->double('student_charge', 8, 4);
            $table->double('staff_charge', 8, 4);
            $table->date('start_date');
            $table->date('end_date');
            $table->unique(['school_id', 'start_date'], 'subscription');
            $table->timestamps();
        });

        Schema::create('addons', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('price', 8, 4)->comment('Daily price');
            $table->foreignId('feature_id')->unique()->references('id')->on('features')->onDelete('cascade');
            $table->tinyInteger('status')->comment('0 => Inactive, 1 => Active')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('addon_subscriptions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->double('price', 8, 4);
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('status')->default(1)->comment('0 => Discontinue next billing, 1 => Continue');
            $table->unique(['school_id', 'feature_id', 'end_date'], 'addon_subscription');
            $table->timestamps();
        });

        Schema::create('subscription_bills', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->string('description')->nullable(true);
            $table->double('amount', 8, 4);
            $table->bigInteger('total_student');
            $table->bigInteger('total_staff');
            $table->foreignId('payment_transaction_id')->nullable(true)->references('id')->on('payment_transactions')->onDelete('cascade');
            $table->date('due_date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['subscription_id', 'school_id'], 'subscription_bill');
            $table->timestamps();
        });

        Schema::create('expense_categories', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable(true);
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expenses', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable(true)->references('id')->on('expense_categories')->onDelete('cascade');
            $table->string('ref_no')->nullable(true);
            $table->foreignId('staff_id')->nullable(true)->references('id')->on('staffs')->onDelete('cascade');
            $table->bigInteger('month')->nullable(true);
            $table->integer('year')->nullable(true);
            $table->string('title', 512);
            $table->string('description')->nullable(true);
            $table->double('amount', 8, 2);
            $table->date('date');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['staff_id','month','year'],'salary_unique_records');
        });

        Schema::create('payment_configurations', static function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->string('api_key');
            $table->string('secret_key');
            $table->string('webhook_secret_key');
            $table->enum('status', [0, 1])->comment('0 - Off, 1 - On');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('leaves', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('reason');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('status')->default(0)->comment('0 => Pending, 1 => Approved, 2 => Rejected');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreignId('session_year_id')->references('id')->on('session_years')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('staff_support_schools', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id','school_id'],'user_school');
        });

        Schema::create('faqs', static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();

        });

        Schema::create('subscription_features', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreignId('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['subscription_id','feature_id'],'unique');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('schools');
        Schema::table('roles', static function (Blueprint $table) {
//            $table->dropUnique(['name', 'guard_name', 'school_id']);

//            $table->dropConstrainedForeignId('school_id');
            $table->dropColumn('editable');
            $table->dropColumn('custom_role');
        });
        Schema::table('users', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
        });
        Schema::dropIfExists('students');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('session_years');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('class_sections');
        Schema::dropIfExists('mediums');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('elective_subject_groups');
        Schema::dropIfExists('subject_teachers');
        Schema::dropIfExists('student_subjects');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('lesson_topics');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('exam_timetables');
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('academic_calendars');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('staffs');
        Schema::dropIfExists('files');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('promote_students');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('online_exam_student_answers');
        Schema::dropIfExists('student_online_exam_statuses');
        Schema::dropIfExists('online_exam_question_choices');
        Schema::dropIfExists('online_exam_question_options');
        Schema::dropIfExists('online_exam_questions');
        Schema::dropIfExists('online_exams');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('extra_student_datas');
        Schema::dropIfExists('school_settings');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('class_teachers');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('announcement_classes');
        Schema::dropIfExists('features');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('fees_types');
        Schema::dropIfExists('fees_structures');
        Schema::dropIfExists('fees_classes');
        Schema::dropIfExists('compulsory_fees');
        Schema::dropIfExists('optional_fees');
        Schema::dropIfExists('fees_paids');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('installment_fees');
        Schema::dropIfExists('fees');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('addon_subscriptions');
        Schema::dropIfExists('subscription_bills');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('streams');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('payment_configurations');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('staff_support_schools');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('subscription_features');

    }
};
