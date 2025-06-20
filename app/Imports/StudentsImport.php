<?php

namespace App\Imports;

use App\Repositories\FormField\FormFieldsInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Repositories\User\UserInterface;
use App\Rules\TrimmedEnum;
use App\Services\CachingService;
use App\Services\ResponseService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Str;
use Throwable;
use TypeError;

class StudentsImport implements WithMultipleSheets
{
    private mixed $classSectionID;
    private mixed $sessionYearID;
    private mixed $is_send_notification;

    public function __construct($classSectionID, $sessionYearID, $is_send_notification)
    {
        $this->classSectionID = $classSectionID;
        $this->sessionYearID = $sessionYearID;
        $this->is_send_notification = $is_send_notification;
    }

    /**
     * @throws Throwable
     */
    public function sheets(): array
    {
        return [
            new FirstSheetImport($this->classSectionID, $this->sessionYearID, $this->is_send_notification)
        ];
    }
}

class FirstSheetImport implements ToCollection, WithHeadingRow
{
    private mixed $classSectionID;
    private mixed $sessionYearID;
    private mixed $is_send_notification;

    /**
     * @param $classSectionID
     * @param $sessionYearID
     * @param $is_send_notification
     */

    // Import the Class Section and Repositories
    public function __construct($classSectionID, $sessionYearID, $is_send_notification)
    {
        $this->classSectionID = $classSectionID;
        $this->sessionYearID = $sessionYearID;
        $this->is_send_notification = $is_send_notification;
    }

    /**
     * @throws JsonException
     * @throws Throwable
     */
    public function collection(Collection $collection)
    {
        $student = app(StudentInterface::class);
        $formFields = app(FormFieldsInterface::class);
        $sessionYear = app(SessionYearInterface::class);

        $subscription = app(SubscriptionInterface::class);
        $user = app(UserInterface::class);
        $cache = app(CachingService::class);

        $validator = Validator::make($collection->toArray(), [
            '*.first_name'     => 'required',
            '*.last_name'      => 'required',
            '*.mobile'         => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            '*.gender'         => ['required', new TrimmedEnum(['male', 'female', 'Male', 'Female'])],
            '*.dob'            => 'required|date',
            '*.admission_date' => 'required|date',
            '*.current_address'      => 'required',
            '*.permanent_address'      => 'required',
            '*.guardian_email'      => 'required|email',
            '*.guardian_first_name' => 'required',
            '*.guardian_last_name'  => 'required',
            '*.guardian_mobile'     => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
        ],[
            '*.first_name.required' => 'Please enter the first name.',
            '*.last_name.required' => 'Please enter the last name.',
            '*.mobile.required' => 'Please enter the mobile number.',
            '*.gender.required' => 'Please select the gender.',
            '*.dob.date' => 'Please ensure that the dob date format you use is either DD-MM-YYYY or MM/DD/YYYY.',
            '*.admission_date.date' => 'Please ensure that the admission date format you use is either DD-MM-YYYY or MM/DD/YYYY.',
            '*.guardian_email.required' => 'Please enter the guardian email.',
            '*.guardian_email.email' => 'Please enter a valid email address.',
            '*.guardian_first_name.required' => 'Please enter the guardian first name.',
            '*.guardian_last_name.required' => 'Please enter the guardian last name.',
            '*.guardian_mobile.required' => 'Please enter the guardian mobile number.',
            
        ]);

        //             If Validation fails then this will throw the ValidationFail Exception
        $validator->validate();

        // Check free trial package
        $today_date = Carbon::now()->format('Y-m-d');
        $get_subscription = $subscription->builder()->doesntHave('subscription_bill')->whereDate('start_date','<=',$today_date)->where('end_date','>=',$today_date)->whereHas('package',function($q){
            $q->where('is_trial',1);
        })->first();

        $userService = app(UserService::class);
        $sessionYear = $sessionYear->findById($this->sessionYearID);
        DB::beginTransaction();
        foreach ($collection as $row) {

            // Check free trial package
            
            if ($get_subscription) {
                $systemSettings = $cache->getSystemSettings();
                $count_student = $user->builder()->role('Student')->withTrashed()->count();
                if ($count_student >= $systemSettings['student_limit']) {
                    $message = "The free trial allows only ".$systemSettings['student_limit']." students.";
                    ResponseService::errorResponse($message);
                    break;
                }
            }
            $row = $row->toArray();
            // Find the index of the key after which to split the array
            $splitIndex = array_search('guardian_mobile', array_keys($row)) + 1;

            // Get The Extra Details of it
            $extraDetailsFields = array_slice($row, $splitIndex);
            // Get the Session year ID
            // $sessionYear = $sessionYear->findById($this->sessionYearID);


            $guardian = $userService->createOrUpdateParent($row['guardian_first_name'], $row['guardian_last_name'], $row['guardian_email'], $row['guardian_mobile'], $row['guardian_gender']);
            $get_student = $student->builder()->where('session_year_id', $sessionYear->id)->select('id')->latest('id')->pluck('id')->first();
            $admission_no = $sessionYear->name .'0'.  Auth::user()->school_id .'0'. ($get_student + 1);
            $extraDetails = array();
            // Check that Extra Details Exists
            if (!empty($extraDetailsFields)) {
                $extraFieldName = array_map(static function ($d) {
                    return str_replace("_", " ", $d);
                }, array_keys($extraDetailsFields));
                $formFieldsCollection = $formFields->builder()->whereIn('name', $extraFieldName)->get();
                $extraFieldValidationRules = [];
                foreach ($formFieldsCollection as $field) {
                    if ($field->is_required) {
                        $name = strtolower(str_replace(' ', '_', $field->name));
                        $extraFieldValidationRules[$name] = 'required';
                    }
                }
                $extraFieldValidator = Validator::make($row, $extraFieldValidationRules);
                $extraFieldValidator->validate();


                // Create Extra Details Array for Student's Extra Form Details
                foreach ($extraDetailsFields as $key => $value) {
                    $formField = $formFieldsCollection->first(function ($data) use ($key) {
                        return strtolower($data->name) === str_replace("_", " ", $key);
                    });

                    if (!empty($formField)) {

                        // if Form Field is checkbox then make data in json format
                        $data = $formField->type == 'checkbox' ? explode(',', $value) : $value;
                        $extraDetails[] = array(
                            'input_type'    => $formField->type,
                            'form_field_id' => $formField->id,
                            'data'          => (is_array($data)) ? json_encode($data, JSON_THROW_ON_ERROR) : $data
                        );
                    }
                }
                //                     Make File Input Array to Store the Null Values
                $getFileExtraField = $formFields->builder()->where('type', 'file')->get();
                foreach ($getFileExtraField as $value) {
                    $extraDetails[] = array(
                        'input_type'    => 'file',
                        'form_field_id' => $value->id,
                        'data'          => NULL,
                    );
                }
            }
            //                $userService->createOrUpdateStudentUser($row['first_name'], $row['last_name'], $admission_no, $row['mobile'], $row['dob'], $row['gender'], null, $this->class_section_id, now(), $extraDetails, null, $guardian->id);
            try {
                $userService->createStudentUser($row['first_name'], $row['last_name'], $admission_no, $row['mobile'], $row['dob'], $row['gender'], null, $this->classSectionID, $row['admission_date'],$row['current_address'],$row['permanent_address'], $sessionYear->id, $guardian->id, $extraDetails, 0, $this->is_send_notification);
            } catch (Throwable $e) {
                // IF Exception is TypeError and message contains Mail keywords then email is not sent successfully
                if ($e instanceof TypeError && Str::contains($e->getMessage(), [
                        'Mail',
                        'Mailer',
                        'MailManager'
                    ])) {
                    continue;
                }
                DB::rollBack();
                throw $e;
            }
        }
        DB::commit();
        return true;
    }
}
