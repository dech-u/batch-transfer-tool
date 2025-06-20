<?php

namespace App\Http\Controllers;

use App\Models\CertificateTemplate;
use App\Models\ClassGroup;
use App\Models\ExpenseCategory;
use App\Models\Faq;
use App\Models\FeesType;
use App\Models\File as ModelsFile;
use App\Models\FormField;
use App\Models\Grade;
use App\Models\Holiday;
use App\Models\Mediums;
use App\Models\PaymentTransaction;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Semester;
use App\Models\SessionYear;
use App\Models\Shift;
use App\Models\Slider;
use App\Models\Staff;
use App\Models\Stream;
use App\Models\Students;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Repositories\DatabaseBackup\DatabaseBackupInterface;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use App\Services\SubscriptionService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class DatabaseBackupController extends Controller
{
    //

    private DatabaseBackupInterface $databaseBackup;
    private SubscriptionService $subscriptionService;

    public function __construct(DatabaseBackupInterface $databaseBackup, SubscriptionService $subscriptionService)
    {
        $this->databaseBackup = $databaseBackup;
        $this->subscriptionService = $subscriptionService;
    }

    public function index()
    {
        $schoolId = Auth::user()->hasRole('School Admin') ? Auth::user()->school_id : Auth::user()->id;
        return view('database-backup.index', compact('schoolId'));
    }

    public function store()
    {

        ResponseService::noPermissionThenSendJson('database-backup');
        $user_Id = Auth::user()->hasRole('School Admin') ? Auth::user()->school_id : Auth::user()->id;
        $current_version = SystemSetting::where('name', 'system_version')->first()['data'];

        if (Auth::user()->hasRole('School Admin')) {

            // Database backup
            $allTables = DB::select('SHOW TABLES');
            $tableNames = array_map('current', $allTables);

            $expectedTables = ['addons', 'attachments', 'categories', 'chats', 'failed_jobs', 'features', 'feature_sections', 'feature_section_lists', 'guidances', 'languages', 'messages', 'migrations', 'packages', 'package_features', 'password_resets', 'personal_access_tokens', 'staff_support_schools', 'system_settings', 'user_status_for_next_cycles', 'database_backups'];

            $subscription = $this->subscriptionService->active_subscription(Auth::user()->school_id);

            $allTables = array_diff($tableNames, $expectedTables);
            $tableNames = array_values($allTables);

            $staff_ids = Staff::whereHas('user', function ($q) use ($user_Id) {
                $q->where('school_id', $user_Id);
            })->pluck('id')->toArray();

            $students = Students::where('school_id', $user_Id)->withTrashed()->get();
            $student_ids = $students->pluck('user_id')->toArray();
            $guardian_ids = $students->pluck('guardian_id')->toArray();

            $roles_id = Role::where('school_id', Auth::user()->school_id)->pluck('id')->toArray();

            $guardian_ids = array_values(array_unique($guardian_ids));
            // Tables requiring additional conditions
            // dd($subscription->school->admin_id);
            $tablesWithAdditionalConditions = [
                'users' => function ($query) use ($user_Id, $guardian_ids, $subscription) {
                    $query->where(function ($q) use ($user_Id, $guardian_ids) {
                        $q->where('school_id', $user_Id)
                            ->orWhereIn('id', $guardian_ids);
                    });
                    // ->where(function ($q) use ($subscription) {
                    //     $q->WhereNot('id', $subscription->school->admin_id)
                    //         ->where('id', Auth::user()->id);
                    // });
                },
                'fees_advance' => function ($query) use ($guardian_ids) {
                    $query->whereIn('parent_id', $guardian_ids);
                },
                'staffs' => function ($query) use ($staff_ids) {
                    $query->whereIn('id', $staff_ids);
                },
                'staff_salaries' => function ($query) use ($staff_ids) {
                    $query->whereIn('staff_id', $staff_ids);
                },
                'model_has_roles' => function ($query) use ($roles_id) {
                    $query->whereIn('role_id', $roles_id);
                },

                'role_has_permissions' => function ($query) use ($roles_id) {
                    $query->whereIn('role_id', $roles_id);
                },
                'subscription_features' => function ($query) use ($subscription) {
                    $query->where('subscription_id', $subscription->id);
                },
                // Add more specific tables here
            ];

            $backupData = '';
            // dd($tableNames);
            foreach ($tableNames as $table) {
                // Get table creation SQL
                // $createTableSQL = DB::select("SHOW CREATE TABLE `$table`");
                // $backupData .= $createTableSQL[0]->{'Create Table'} . ";\n\n";

                // ==========================================================
                $createTableSQL = DB::select("SHOW CREATE TABLE `$table`");
                $createTable = $createTableSQL[0]->{'Create Table'};

                // Replace 'CREATE TABLE' with 'CREATE TABLE IF NOT EXISTS'
                $createTableWithIfNotExists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $createTable);

                // Add the modified SQL to the backup
                $backupData .= $createTableWithIfNotExists . ";\n\n";
                // ==========================================================

                // Create query builder

                $query = DB::table($table);
                // Check if 'school_id' column exists
                $hasSchoolIdColumn = Schema::hasColumn($table, 'school_id');
                // Apply conditions
                if ($hasSchoolIdColumn) {
                    if (!array_key_exists($table, $tablesWithAdditionalConditions)) {
                        $query->where('school_id', $user_Id);
                    } else {
                        // Apply specific conditions
                        $tablesWithAdditionalConditions[$table]($query);
                    }
                } else {
                    // Apply specific conditions if necessary
                    if (array_key_exists($table, $tablesWithAdditionalConditions)) {
                        $tablesWithAdditionalConditions[$table]($query);
                    }
                }

                // Fetch rows and build SQL for inserts
                $rows = $query->get();

                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, (array) $row);

                    $backupData .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                }
                $backupData .= "\n";
            }

            // Define the path
            $path = 'public/database-backup/schools/' . $user_Id; // Change to store in the 'sql' directory
            // $path = storage_path('public/database-backup/schools/' . $user_Id);
            $timestamp = Carbon::now()->format('Y-m-d');
            $file_name = "database_backup_{$user_Id}_{$timestamp}";
            $filePath = $path . "/database_backup_{$user_Id}_{$timestamp}.sql";
            
            // check storage public in id folder exist
            if (!Storage::exists('public/' . $user_Id)) {
                Storage::makeDirectory('public/' . $user_Id);
            }
            
            // Create the directory if it doesn't exist
            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            // Save the backup to the file
            Storage::put($filePath, $backupData);
            // End database backup
            // ==========================================================

            $zip = new ZipArchive;
            $backup_type = 'schools';
            $public_folder_path = storage_path('app/public/' . $user_Id);
            $backup_folder_path = storage_path('app/public/database-backup/' . $backup_type . '/' . $user_Id);

            $zip_file_path = $backup_folder_path . '/' . $file_name . '-(V-' . $current_version . ').zip';

            // dd(File::isDirectory($backup_folder_path));
            // $mainFolder = Auth::user()->id; // Main folder name
            // $mainFolderPath = storage_path('app/public/database-backup/super-admin/' . $mainFolder); // Path to the main folder

            if ($zip->open($zip_file_path, ZipArchive::CREATE) === TRUE) {

                if (File::isDirectory($backup_folder_path)) {

                    // Add main folder to the zip
                    $zip_media_folder = 'media';

                    // create folder for media files
                    $zip->addEmptyDir($zip_media_folder);

                    // create database-backup folder and add .sql file
                    $relativeSqlFile = 'database-backup/database_backup_' . $user_Id . '_' . $timestamp . '.sql';
                    $zip->addFile(storage_path('app/' . $filePath), $relativeSqlFile);
                    // dd($relativeSqlFile);

                    // // Get all subdirectories inside the main folder
                    $subfolders = File::directories($public_folder_path);

                    foreach ($subfolders as $subfolderPath) {
                        // Get the relative path of the subfolder
                        $relativeSubfolder = $zip_media_folder . '/' . str_replace(storage_path('app/public/'), '', $subfolderPath);


                        // Add the subfolder to the zip
                        $zip->addEmptyDir($relativeSubfolder);

                        // Get all files inside the current subfolder
                        $files = File::files($subfolderPath);

                        foreach ($files as $file) {
                            // Get the relative path of the file
                            $relativeFile = $zip_media_folder . '/' . str_replace(storage_path('app/public/'), '', $file);

                            // Add the file to the zip with its relative path
                            $zip->addFile($file, $relativeFile);
                        }
                    }

                    // // Add files in the main folder
                    // $mainFolderFiles = File::files($mainFolderPath);
                    // foreach ($mainFolderFiles as $file) {
                    //     // Get the relative path of the file
                    //     $relativeFile = str_replace(storage_path('app/public/'), '', $file);

                    //     // Add the file to the zip
                    //     $zip->addFile($file, $relativeFile);
                    // }


                    // Close the archive
                    $zip->close();

                    // Create the directory if it doesn't exist
                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }
                }

                // Delete the .sql file after zipping
                Storage::delete($filePath);

                $data = [
                    'name' => $file_name
                ];

                // $this->databaseBackup->create($data);

                $file_name = "database_backup_{$user_Id}_" . Carbon::now()->format('Y-m-d') . '-(V-' . $current_version . ').zip';
                // dd($file_name);
 
                $zipFileDownload = 'database-backup/schools/' . $user_Id . '/' . $file_name;

                $download_url = url(Storage::url($zipFileDownload));

                ResponseService::successResponse('Backup completed successfully', $download_url);
            } else {
                ResponseService::logErrorResponse("DatabaseBackup Controller -> Store Method");
                ResponseService::errorResponse();
            }
        } else if (Auth::user()->hasRole('Super Admin')) {

            // Set default connection for Super Admin
            DB::setDefaultConnection('mysql');

            // Get all tables
            $allTables = DB::select('SHOW TABLES');
            $tableNames = array_map('current', $allTables);

            $backupData = '';

            $expectedTables = ['addons', 'failed_jobs', 'features', 'feature_sections', 'feature_section_lists', 'guidances', 'languages', 'migrations', 'packages', 'package_features', 'password_resets', 'personal_access_tokens', 'staff_support_schools', 'system_settings'];

            $allTables = array_diff($tableNames, $expectedTables);
            $tableNames = array_values($allTables);

            // Loop through each table and generate backup data
            foreach ($tableNames as $table) {
                $createTableSQL = DB::select("SHOW CREATE TABLE `$table`");
                $createTable = $createTableSQL[0]->{'Create Table'};
                $createTableWithIfNotExists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $createTable);
                $backupData .= $createTableWithIfNotExists . ";\n\n";

                $query = DB::table($table);
                $rows = $query->get();

                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, (array) $row);

                    $backupData .= "INSERT INTO `$table` VALUES (" . implode(', ', $values) . ");\n";
                }
                $backupData .= "\n";
            }

            // Define the path
            $path = 'public/database-backup/super-admin/' . $user_Id; // Change to store in the 'sql' directory

            $timestamp = Carbon::now()->format('Y-m-d');
            $file_name = "database_backup_{$user_Id}_{$timestamp}";
            $filePath = $path . "/database_backup_{$user_Id}_{$timestamp}.sql";

            // check storage public in id folder exist
            if (!Storage::exists('public/' . $user_Id)) {
                Storage::makeDirectory('public/' . $user_Id);
            }

            // Create the directory if it doesn't exist
            if (!Storage::exists($path)) {
                Storage::makeDirectory($path);
            }

            // Save the backup to the file
            Storage::put($filePath, $backupData);
            // End database backup
            // ==========================================================

            $zip = new ZipArchive;
            $backup_type = 'super-admin';
            $public_folder_path = storage_path('app/public/' . $user_Id);
            $backup_folder_path = storage_path('app/public/database-backup/' . $backup_type . '/' . $user_Id);

            $zip_file_path = $backup_folder_path . '/' . $file_name . '-(V-' . $current_version . ').zip';

            // dd(File::isDirectory($backup_folder_path));
            // $mainFolder = Auth::user()->id; // Main folder name
            // $mainFolderPath = storage_path('app/public/database-backup/super-admin/' . $mainFolder); // Path to the main folder

            if ($zip->open($zip_file_path, ZipArchive::CREATE) === TRUE) {

                if (File::isDirectory($backup_folder_path)) {

                    // Add main folder to the zip
                    $zip_media_folder = 'media';

                    // create folder for media files
                    $zip->addEmptyDir($zip_media_folder);

                    // create database-backup folder and add .sql file
                    $relativeSqlFile = 'database-backup/database_backup_' . $user_Id . '_' . $timestamp . '.sql';
                    $zip->addFile(storage_path('app/' . $filePath), $relativeSqlFile);
                    // dd($relativeSqlFile);

                    // // Get all subdirectories inside the main folder
                    $subfolders = File::directories($public_folder_path);

                    foreach ($subfolders as $subfolderPath) {
                        // Get the relative path of the subfolder
                        $relativeSubfolder = $zip_media_folder . '/' . str_replace(storage_path('app/public/'), '', $subfolderPath);


                        // Add the subfolder to the zip
                        $zip->addEmptyDir($relativeSubfolder);

                        // Get all files inside the current subfolder
                        $files = File::files($subfolderPath);

                        foreach ($files as $file) {
                            // Get the relative path of the file
                            $relativeFile = $zip_media_folder . '/' . str_replace(storage_path('app/public/'), '', $file);

                            // Add the file to the zip with its relative path
                            $zip->addFile($file, $relativeFile);
                        }
                    }

                    // // Add files in the main folder
                    // $mainFolderFiles = File::files($mainFolderPath);
                    // foreach ($mainFolderFiles as $file) {
                    //     // Get the relative path of the file
                    //     $relativeFile = str_replace(storage_path('app/public/'), '', $file);

                    //     // Add the file to the zip
                    //     $zip->addFile($file, $relativeFile);
                    // }


                    // Close the archive
                    $zip->close();

                    // Create the directory if it doesn't exist
                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }
                }

                // Delete the .sql file after zipping
                Storage::delete($filePath);

                $data = [
                    'name' => $file_name
                ];

                // $this->databaseBackup->create($data);

                $file_name = "database_backup_{$user_Id}_" . Carbon::now()->format('Y-m-d') . '-(V-' . $current_version . ').zip';
               
                $zipFileDownload = 'database-backup/super-admin/' . $user_Id . '/' . $file_name;

                $download_url = url(Storage::url($zipFileDownload));

                ResponseService::successResponse('Backup completed successfully', $download_url);
            } else {
                ResponseService::logErrorResponse("DatabaseBackup Controller -> Store Method");
                ResponseService::errorResponse();
            }
        }
    }

    public function show()
    {
        ResponseService::noPermissionThenRedirect('database-backup');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');

        $sql = $this->databaseBackup->builder()
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")->orwhere('title', 'LIKE', "%$search%")->orwhere('description', 'LIKE', "%$search%")->orwhere('date', 'LIKE', "%$search%");
                    });
                });
            });

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = BootstrapTableService::button('fa fa-refresh', '#', ['restore-database', 'btn-gradient-info'], ['title' => trans("restore"), 'data-id' => $row->id]);
            $operate .= BootstrapTableService::deleteButton(url('database-backup', $row->id));

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function destroy($id)
    {
        ResponseService::noPermissionThenSendJson('database-backup');
        try {
            $databaseBackup = $this->databaseBackup->findById($id);
            $sql_file = 'database-backup/' . Auth::user()->school_id . '/' . $databaseBackup->name . '.sql';
            $zip_file = 'database-backup/' . Auth::user()->school_id . '/' . $databaseBackup->name . '.zip';


            if (Storage::disk('public')->exists($sql_file)) {
                Storage::disk('public')->delete($sql_file);
            }
            if (Storage::disk('public')->exists($zip_file)) {
                Storage::disk('public')->delete($zip_file);
            }


            $this->databaseBackup->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "DatabaseBackup Controller -> Destroy Method");
            ResponseService::errorResponse();
        }
    }

    public function restore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zip_file' => 'required|mimes:zip',
        ]);

        if ($validator->fails()) {
            ResponseService::errorResponse($validator->errors()->first());
        }

        try {

            $authId = Auth::user()->hasRole('School Admin') ? Auth::user()->school_id : Auth::user()->id;

            if (Auth::user()->hasRole('School Admin')) {

                // File UnZip
                $zip = new ZipArchive;
                $backup_type = 'schools';
                $public_folder_path = storage_path('app/public/' . $authId);
                $backup_folder_path = storage_path('app/public/database-backup/' . $backup_type . '/' . $authId);



                // Ensure the school ID folder exists
                if (!file_exists($public_folder_path)) {
                    File::makeDirectory($public_folder_path, 0755, true);
                }

                // Old Data Delete from public folder
                $mainOldfolders = File::directories($public_folder_path);
                // dd("School Admin",$mainOldfolders);

                foreach ($mainOldfolders as $subfolder) {
                    foreach (File::files($subfolder) as $file) {
                        File::delete($file);
                    }
                }

                // Un-Zip and move data
                if ($zip->open($request->zip_file) === TRUE) {

                    $zip->extractTo($backup_folder_path);
                    $mainfolders = File::directories($backup_folder_path);

                    foreach ($mainfolders as $subfolder) {
                        
                        // Check if the folder doesn't contain the specific 'database-backup' path
                        if (!Str::contains($subfolder, $authId . "/database-backup")) {
                            
                           if (File::isDirectory($subfolder. '/' . $authId)) {
                                // Get the list of subfolders
                                $mainSubfolders = File::directories($subfolder . '/' . $authId);
                              
                                // Iterate through each subfolder
                                foreach ($mainSubfolders as $folder) {
    
                                    $folderName = basename($folder);
        
                                    // Build the destination path for the folder
                                    $destinationPath = $public_folder_path . "/" . $folderName;
    
                                    File::move($folder, $destinationPath);
                                }
                           }
                        }
                    }

                    $zip->close();
                }



                // Read Backup .sql File
                $sql_file_path = "";
                foreach (File::directories($backup_folder_path) as $subfolders) {
                    foreach (File::files($subfolders) as $subfolders) {
                        $sql_file_path = pathinfo($subfolders)['dirname'] . "/" . pathinfo($subfolders)['basename'];
                    }
                }
               

                try {
                    DB::beginTransaction();
                    DB::statement("SET FOREIGN_KEY_CHECKS = 0");
                    $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

                    $expectedTables = ['addons', 'attachments', 'categories', 'chats', 'failed_jobs', 'features', 'feature_sections', 'feature_section_lists', 'guidances', 'languages', 'messages', 'migrations', 'packages', 'package_features', 'password_resets', 'personal_access_tokens', 'staff_support_schools', 'system_settings', 'user_status_for_next_cycles', 'database_backups'];

                    $allTables = array_diff($tables, $expectedTables);
                    $tableNames = array_values($allTables);
                    // dd($tableNames);
                    foreach ($tableNames as $table) {
                        DB::table($table)->truncate();
                        \Log::info("Table {$table} truncated.");
                    }
                    DB::statement("SET FOREIGN_KEY_CHECKS = 1");
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Truncation error: ' . $e->getMessage());
                }

                // Check if the SQL file exists and proceed with execution
                if (File::exists($sql_file_path)) {

                    // Read the contents of the SQL file
                    $sql = File::get($sql_file_path);

                    $queries = array_filter(
                        explode(";\n", $sql),
                        function ($query) {
                            return !empty(trim($query));
                        }
                    );
                    try {
                        // Begin transaction
                        DB::beginTransaction();
                        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
                        foreach ($queries as $query) {
                            if (!empty(trim($query))) {
                                DB::unprepared(trim($query));
                                \Log::info("Executed query: {$query}");
                            }
                        }
                        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Log::error("Transaction failed: " . $e->getMessage());
                    }
                } else {
                    \Log::warning('SQL file not found: ' . $sql_file_path);
                }

                // Delete backup folders and .sql files after backup
                $deleteBackupFolders = File::directories($backup_folder_path);

                foreach ($deleteBackupFolders as $subfolder) {
                    if (Str::contains($subfolder, $authId . "/database-backup")) {

                        // Delete all .sql files in the subfolder
                        foreach (File::files($subfolder) as $subpath) {
                            if (pathinfo($subpath)['extension'] === 'sql') {
                                File::delete($subpath);
                            }
                        }

                        // Delete the subfolder after its contents have been deleted
                        File::deleteDirectory($subfolder);
                    }
                }


                // add school current version migration
                $schools = School::withTrashed()->where('id', Auth::user()->school_id)->get();

                foreach ($schools as $key => $school) {
                    Config::set('database.connections.school.database', $school->database_name);
                    DB::purge('school');
                    DB::connection('school')->reconnect();
                    DB::setDefaultConnection('school');

                    Artisan::call('migrate', [
                        '--database' => 'school',
                        '--path' => 'database/migrations/schools',
                        '--force' => true,
                    ]);
                }
            } else if (Auth::user()->hasRole('Super Admin')) {
                // File UnZip
                $zip = new ZipArchive;
                $backup_type = 'super-admin';
                $public_folder_path = storage_path('app/public/' . $authId);
                $backup_folder_path = storage_path('app/public/database-backup/' . $backup_type . '/' . $authId);

                // Ensure the school ID folder exists
                if (!file_exists($public_folder_path)) {
                    File::makeDirectory($public_folder_path, 0755, true);
                }

                // Old Data Delete from public folder
                $mainOldfolders = File::directories($public_folder_path);
                // dd("School Admin",$mainOldfolders);

                foreach ($mainOldfolders as $subfolder) {
                    foreach (File::files($subfolder) as $file) {
                        File::delete($file);
                    }
                }

                // Un-Zip and move data
                if ($zip->open($request->zip_file) === TRUE) {

                    $zip->extractTo($backup_folder_path);
                    $mainfolders = File::directories($backup_folder_path);

                    foreach ($mainfolders as $subfolder) {
                        // Check if the folder doesn't contain the specific 'database-backup' path
                        if (!Str::contains($subfolder, $authId . "/database-backup")) {
                            
                           if (File::isDirectory($subfolder. '/' . $authId)) {
                                // Get the list of subfolders
                                $mainSubfolders = File::directories($subfolder . '/' . $authId);
                              
                                // Iterate through each subfolder
                                foreach ($mainSubfolders as $folder) {
    
                                    $folderName = basename($folder);
        
                                    // Build the destination path for the folder
                                    $destinationPath = $public_folder_path . "/" . $folderName;
    
                                    File::move($folder, $destinationPath);
                                }
                           }
                        }
                    }

                    $zip->close();
                }


                // Read Backup .sql File
                $sql_file_path = "";
                foreach (File::directories($backup_folder_path) as $subfolders) {
                    foreach (File::files($subfolders) as $subfolders) {

                        $sql_file_path = pathinfo($subfolders)['dirname'] . "/" . pathinfo($subfolders)['basename'];
                    }
                }

                try {
                    // Start the transaction
                    DB::beginTransaction();

                    // Disable foreign key checks
                    DB::statement("SET FOREIGN_KEY_CHECKS = 0");

                    // Get all table names
                    $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

                    $expectedTables = ['addons', 'attachments', 'categories', 'chats', 'failed_jobs', 'features', 'feature_sections', 'feature_section_lists', 'guidances', 'languages', 'messages', 'migrations', 'packages', 'package_features', 'password_resets', 'personal_access_tokens', 'staff_support_schools', 'system_settings', 'user_status_for_next_cycles', 'database_backups'];

                    $allTables = array_diff($tables, $expectedTables);
                    $tableNames = array_values($allTables);
                    
                    // Loop through each table and truncate
                    foreach ($tableNames as $table) {
                        // You can use `DB::table($table)->truncate()` or use raw SQL for truncation
                        DB::table($table)->truncate();
                        \Log::info("Table {$table} truncated.");
                    }

                    // Re-enable foreign key checks
                    DB::statement("SET FOREIGN_KEY_CHECKS = 1");

                    // Commit the transaction
                    DB::commit();
                } catch (\Exception $e) {
                    // Rollback the transaction if an error occurs
                    DB::rollBack();
                    \Log::error('Truncation error: ' . $e->getMessage());
                }

                // Check if the SQL file exists and proceed with execution
                if (File::exists($sql_file_path)) {

                    // Read the contents of the SQL file
                    $sql = File::get($sql_file_path);

                    $queries = array_filter(
                        explode(";\n", $sql),
                        function ($query) {
                            return !empty(trim($query));
                        }
                    );
                    try {
                        // Begin transaction
                        DB::beginTransaction();
                        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
                        foreach ($queries as $query) {
                            if (!empty(trim($query))) {
                                DB::unprepared(trim($query));
                                \Log::info("Executed query: {$query}");
                            }
                        }
                        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Log::error("Transaction failed: " . $e->getMessage());
                    }
                } else {
                    \Log::warning('SQL file not found: ' . $sql_file_path);
                }

                // Delete backup folders and .sql files after backup
                $deleteBackupFolders = File::directories($backup_folder_path);

                foreach ($deleteBackupFolders as $subfolder) {
                    if (Str::contains($subfolder, $authId . "/database-backup")) {

                        // Delete all .sql files in the subfolder
                        foreach (File::files($subfolder) as $subpath) {
                            if (pathinfo($subpath)['extension'] === 'sql') {
                                File::delete($subpath);
                            }
                        }

                        // Delete the subfolder after its contents have been deleted
                        File::deleteDirectory($subfolder);
                    }
                }
            }

            ResponseService::successResponse('Data Restore Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            ResponseService::logErrorResponse($th, "DatabaseBackup Controller -> Restore Method");
            ResponseService::errorResponse();
        }
    }

    public function getTableNameFromQuery($query)
    {
        if (preg_match('/CREATE TABLE `?(\w+)`?/', $query, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function deleteOldRecords($subscription)
    {
        $schoolId = $subscription->school_id;
        if ($subscription) {

            $guardian_ids = Students::where('school_id', $schoolId)->pluck('guardian_id')->toArray();

            Mediums::where('school_id', $schoolId)->delete();
            User::where('school_id', $schoolId)->whereNot('id', Auth::user()->id)->whereNot('id', $subscription->school->admin_id)->delete();
            User::whereIn('id', $guardian_ids)->delete();
            CertificateTemplate::where('school_id', $schoolId)->delete();
            ClassGroup::where('school_id', $schoolId)->delete();
            ExpenseCategory::where('school_id', $schoolId)->delete();
            Faq::where('school_id', $schoolId)->delete();
            FeesType::where('school_id', $schoolId)->delete();
            ModelsFile::where('school_id', $schoolId)->delete();
            FormField::where('school_id', $schoolId)->delete();
            SessionYear::where('school_id', $schoolId)->delete();
            Grade::where('school_id', $schoolId)->delete();
            Holiday::where('school_id', $schoolId)->delete();
            SchoolSetting::where('school_id', $schoolId)->delete();
            Section::where('school_id', $schoolId)->delete();
            Semester::where('school_id', $schoolId)->delete();
            Shift::where('school_id', $schoolId)->delete();
            Slider::where('school_id', $schoolId)->delete();
            Stream::where('school_id', $schoolId)->delete();
            Subscription::where('school_id', $schoolId)->delete();
            PaymentTransaction::where('school_id', $schoolId)->delete();
        }
    }
}