<?php

namespace App\Http\Controllers;

use App\Models\FeesAdvance;
use App\Repositories\ClassSchool\ClassSchoolInterface;
use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\CompulsoryFee\CompulsoryFeeInterface;
use App\Repositories\Fees\FeesInterface;
use App\Repositories\FeesClassType\FeesClassTypeInterface;
use App\Repositories\FeesInstallment\FeesInstallmentInterface;
use App\Repositories\FeesPaid\FeesPaidInterface;
use App\Repositories\FeesType\FeesTypeInterface;
use App\Repositories\Medium\MediumInterface;
use App\Repositories\OptionalFee\OptionalFeeInterface;
use App\Repositories\PaymentConfiguration\PaymentConfigurationInterface;
use App\Repositories\PaymentTransaction\PaymentTransactionInterface;
use App\Repositories\SchoolSetting\SchoolSettingInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Repositories\Student\StudentInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Repositories\User\UserInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class FeesController extends Controller
{
    private FeesInterface $fees;
    private SessionYearInterface $sessionYear;
    private FeesInstallmentInterface $feesInstallment;
    private SchoolSettingInterface $schoolSettings;
    private MediumInterface $medium;
    private FeesTypeInterface $feesType;
    private ClassSchoolInterface $classes;
    private FeesClassTypeInterface $feesClassType;
    private UserInterface $user;
    private FeesPaidInterface $feesPaid;
    private CompulsoryFeeInterface $compulsoryFee;
    private OptionalFeeInterface $optionalFee;
    private CachingService $cache;
    private PaymentConfigurationInterface $paymentConfigurations;
    private ClassSchoolInterface $class;
    private StudentInterface $student;
    private PaymentTransactionInterface $paymentTransaction;
    private SystemSettingInterface $systemSetting;
    private ClassSectionInterface $classSection;

    public function __construct(FeesInterface $fees, SessionYearInterface $sessionYear, FeesInstallmentInterface $feesInstallment, SchoolSettingInterface $schoolSettings, MediumInterface $medium, FeesTypeInterface $feesType, ClassSchoolInterface $classes, FeesClassTypeInterface $feesClassType, UserInterface $user, FeesPaidInterface $feesPaid, CompulsoryFeeInterface $compulsoryFee, OptionalFeeInterface $optionalFee, CachingService $cache, PaymentConfigurationInterface $paymentConfigurations, ClassSchoolInterface $classSchool, StudentInterface $student, PaymentTransactionInterface $paymentTransaction, SystemSettingInterface $systemSetting, ClassSectionInterface $classSection)
    {
        $this->fees = $fees;
        $this->sessionYear = $sessionYear;
        $this->feesInstallment = $feesInstallment;
        $this->schoolSettings = $schoolSettings;
        $this->medium = $medium;
        $this->feesType = $feesType;
        $this->classes = $classes;
        $this->feesClassType = $feesClassType;
        $this->user = $user;
        $this->feesPaid = $feesPaid;
        $this->compulsoryFee = $compulsoryFee;
        $this->optionalFee = $optionalFee;
        $this->cache = $cache;
        $this->paymentConfigurations = $paymentConfigurations;
        $this->class = $classSchool;
        $this->student = $student;
        $this->paymentTransaction = $paymentTransaction;
        $this->systemSetting = $systemSetting;
        $this->classSection = $classSection;
    }

    /* START : Fees Module */
    public function index()
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-list');
        $classes = $this->class->all(['*'], ['stream', 'medium', 'stream']);
        $feesTypeData = $this->feesType->all();
        $sessionYear = $this->sessionYear->builder()->pluck('name', 'id');
        $defaultSessionYear = $this->cache->getDefaultSessionYear();
        $mediums = $this->medium->builder()->pluck('name', 'id');
        return view('fees.index', compact('classes', 'feesTypeData', 'sessionYear', 'defaultSessionYear', 'mediums'));
    }

    public function store(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-create');
        $request->validate([
            'include_fee_installments'            => 'required|boolean',
            'due_date'                            => 'required|date',
            'due_charges_percentage'              => 'required|numeric',
            'due_charges_amount'                  => 'required|numeric',
            'class_id'                            => 'required|array',
            'class_id.*'                          => 'required|numeric',
            'compulsory_fees_type'                => 'required|array',
            'compulsory_fees_type.*'              => 'required|array',
            'compulsory_fees_type.*.fees_type_id' => 'required|numeric',
            'compulsory_fees_type.*.amount'       => 'required|numeric',
            'optional_fees_type.*'                => 'required|array',
            'optional_fees_type.*.fees_type_id'   => 'required|numeric',
            'optional_fees_type.*.amount'         => 'required|numeric',
            'fees_installments'                   => 'required_if:include_fee_installments,1|array',
            'fees_installments.*.name'            => 'required',
            'fees_installments.*.due_date'        => 'required|date',
            'fees_installments.*.due_charges'     => 'required|numeric'
        ]);
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();
            $classes = $this->class->builder()->whereIn("id", $request->class_id)->with('stream', 'medium')->get();

            $notifyUser = $this->student->builder()->whereHas('class_section', function ($q) use ($request) {
                $q->whereIn('class_id', $request->class_id);
            })->pluck('guardian_id');

            $title = 'Fees';
            $body = $request->name;
            $type = 'Fees';
            // send_notification($notifyUser, $title, $body, $type); // Send Notification

            foreach ($request->class_id as $class_id) {
                $class = $classes->first(function ($data) use ($class_id) {
                    return $data->id == $class_id;
                });
                $name = (!empty($request->name)) ? $request->name . " - " : "";
                $fees = $this->fees->create([
                    'name'               => $name . $class->full_name,
                    'due_date'           => $request->due_date,
                    'due_charges'        => $request->due_charges_percentage,
                    'due_charges_amount' => $request->due_charges_amount,
                    'class_id'           => $class_id,
                    'session_year_id'    => $sessionYear->id,
                ]);
                $feeClassType = [];
                foreach ($request->compulsory_fees_type as $data) {
                    $feeClassType[] = array(
                        "fees_id"      => $fees->id,
                        "class_id"     => $class_id,
                        "fees_type_id" => $data['fees_type_id'],
                        "amount"       => $data['amount'],
                        "optional"     => 0,
                    );
                }

                if (!empty($request->optional_fees_type)) {
                    foreach ($request->optional_fees_type as $data) {
                        $feeClassType[] = array(
                            "fees_id"      => $fees->id,
                            "class_id"     => $class_id,
                            "fees_type_id" => $data['fees_type_id'],
                            "amount"       => $data['amount'],
                            "optional"     => 1,
                        );
                    }
                }

                if (count($feeClassType) > 0) {
                    $this->feesClassType->upsert($feeClassType, ['class_id', 'fees_type_id'], ['amount', 'optional']);
                }

                if ($request->include_fee_installments && count($request->fees_installments)) {
                    $installmentData = array();
                    foreach ($request->fees_installments as $data) {
                        $data = (object)$data;
                        $installmentData[] = array(
                            'name'             => $data->name,
                            'due_date'         => date('Y-m-d', strtotime($data->due_date)),
                            'due_charges_type' => $data->due_charges_type,
                            'due_charges'      => $data->due_charges,
                            'fees_id'          => $fees->id,
                            'session_year_id'  => $sessionYear->id,
                        );
                    }
                    $this->feesInstallment->createBulk($installmentData);
                }
            }

            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), [
                'does not exist','file_get_contents'
            ])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.");
            } else {
                DB::rollback();
                ResponseService::logErrorResponse($e, "FeesController -> Store Method");
                ResponseService::errorResponse();
            }
        }
    }

    public function show()
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');
        $showDeleted = request('show_deleted');
        $session_year_id = request('session_year_id');
        $medium_id = request('medium_id');

        $sql = $this->fees->builder()->with('installments', 'class:id,name,stream_id,medium_id', 'class.medium:id,name', 'class.stream:id,name', 'fees_class_type.fees_type:id,name')
            ->where(function ($q) use ($search) {
                $q->when($search, function ($query) use ($search) {
                    $query->where('id', 'LIKE', "%$search%")
                        ->orwhere('name', 'LIKE', "%$search%")
                        ->orwhere('due_date', 'LIKE', "%$search%")
                        ->orwhere('due_charges', 'LIKE', "%$search%");
                });
            })
            ->when(!empty($showDeleted), function ($query) {
                $query->onlyTrashed();
            })->when($session_year_id, function ($query) use ($session_year_id) {
                $query->where('session_year_id', $session_year_id);
            })->when($medium_id, function ($query) use ($medium_id) {
                $query->whereHas('class', function ($q) use ($medium_id) {
                    $q->where('medium_id', $medium_id);
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
            $operate = '';
            if ($showDeleted) {
                $operate .= BootstrapTableService::restoreButton(route('fees.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('fees.trash', $row->id));
            } else {
                $operate .= BootstrapTableService::editButton(route('fees.edit', $row->id), false);
                $operate .= BootstrapTableService::deleteButton(route('fees.destroy', $row->id));
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['compulsory_fees'] = number_format($row->fees_class_type->filter(function ($data) {
                return $data->optional == 0;
            })->sum('amount'), 2);
            $tempRow['total_fees'] = number_format($row->fees_class_type->sum('amount'), 2);
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function edit($id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-edit');
        $classes = $this->class->all(['*'], ['stream', 'medium', 'stream']);
        $feesTypeData = $this->feesType->all();

        $fees = $this->fees->builder()->with(['fees_class_type', 'installments', 'class.medium'])->withCount('fees_paid')->findOrFail($id);
        return view('fees.edit', compact('classes', 'feesTypeData', 'fees'));
    }

    public function update(Request $request, $id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-edit');

        $request->validate([
            'include_fee_installments'            => 'required|boolean',
            'due_date'                            => 'required|date',
            'due_charges_percentage'              => 'required|numeric',
            'due_charges_amount'                  => 'required|numeric',
            'compulsory_fees_type'                => 'required|array',
            'compulsory_fees_type.*'              => 'required|array',
            'compulsory_fees_type.*.fees_type_id' => 'required|numeric',
            'compulsory_fees_type.*.amount'       => 'required|numeric',
            'optional_fees_type.*'                => 'required|array',
            'optional_fees_type.*.fees_type_id'   => 'required|numeric',
            'optional_fees_type.*.amount'         => 'required|numeric',
            'fees_installments'                   => 'nullable|array',
            'fees_installments.*.name'            => 'required',
            'fees_installments.*.due_date'        => 'required|date',
            'fees_installments.*.due_charges'     => 'required|numeric'
        ]);
        try {
            DB::beginTransaction();
            $sessionYear = $this->cache->getDefaultSessionYear();

            // Fees Data Store
            $feesData = array(
                'name'               => $request->name,
                'due_date'           => $request->due_date,
                'due_charges'        => $request->due_charges_percentage,
                'due_charges_amount' => $request->due_charges_amount
            );
            $fees = $this->fees->update($id, $feesData);

            foreach ($request->compulsory_fees_type as $data) {
                $feeClassType[] = array(
                    "id"           => $data['id'],
                    "fees_id"      => $fees->id,
                    "class_id"     => $fees->class_id,
                    "fees_type_id" => $data['fees_type_id'],
                    "amount"       => $data['amount'],
                    "optional"     => 0,
                );
            }

            if (!empty($request->optional_fees_type)) {
                foreach ($request->optional_fees_type as $data) {
                    $feeClassType[] = array(
                        "id"           => $data['id'],
                        "fees_id"      => $fees->id,
                        "class_id"     => $fees->class_id,
                        "fees_type_id" => $data['fees_type_id'],
                        "amount"       => $data['amount'],
                        "optional"     => 1,
                    );
                }
            }

            if (isset($feeClassType)) {
                $this->feesClassType->upsert($feeClassType, ['id'], ['fees_type_id', 'amount', 'optional']);
            }

            if (!empty($request->fees_installments)) {
                $installmentData = array();
                foreach ($request->fees_installments as $data) {
                    $data = (object)$data;
                    $installmentData[] = array(
                        'id'               => $data->id,
                        'name'             => $data->name,
                        'due_date'         => date('Y-m-d', strtotime($data->due_date)),
                        'due_charges_type' => $data->due_charges_type,
                        'due_charges'      => $data->due_charges,
                        'fees_id'          => $fees->id,
                        'session_year_id'  => $sessionYear->id
                    );
                }

                $this->feesInstallment->upsert($installmentData, ['id'], ['name', 'due_date', 'due_charges', 'due_charges_type', 'fees_id', 'session_year_id']);
            }

            DB::commit();
            ResponseService::successRedirectResponse(route('fees.index'), 'Data Update Successfully');
        } catch (Throwable) {
            DB::rollback();
            ResponseService::errorRedirectResponse();
        }
    }

    public function destroy($id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenSendJson('fees-delete');
        try {
            DB::beginTransaction();
            $this->fees->deleteById($id);
            DB::commit();
            ResponseService::successResponse("Data Deleted Successfully");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "FeesController -> Store Method");
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-delete');
        try {
            $this->fees->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function search(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        try {
            $data = $this->fees->builder()->where('session_year_id', $request->session_year_id)->get();
            ResponseService::successResponse("Data Restored Successfully", $data);
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-delete');
        try {
            $this->fees->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    /* END : Fees Module */

    public function deleteInstallment($id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        try {
            DB::beginTransaction();
            $this->feesInstallment->DeleteById($id);
            DB::commit();
            ResponseService::successResponse("Data Deleted Successfully");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function deleteClassType($id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        try {
            DB::beginTransaction();
            $this->feesClassType->DeleteById($id);
            DB::commit();
            ResponseService::successResponse("Data Deleted Successfully");
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function removeOptionalFees($id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');
        try {
            DB::beginTransaction();

            // Get Fees Paid ID and Amount of Fees Transaction Table
            $optionalFeeData = $this->optionalFee->findById($id);
            $feesPaidId = $optionalFeeData->fees_paid_id;
            $optionalFeeAmount = $optionalFeeData->amount;

            $this->optionalFee->permanentlyDeleteById($id); // Permanently Delete Optional Fees Data

            // Check Fees Transactions Entry
            $feesPaidDataQuery = $this->feesPaid->builder()->where('id', $feesPaidId);
            if ($feesPaidDataQuery->count()) {
                // Get Fees Paid Data
                $feesPaidAmount = $feesPaidDataQuery->first()->amount; // Get Fees Paid Amount
                $finalAmount = $feesPaidAmount - $optionalFeeAmount; // Calculate Final Amount
                if ($finalAmount > 0) {
                    $this->feesPaid->update($feesPaidId, ['amount' => $finalAmount]); // Update Fees Paid Data with Final Amount
                } else {
                    $this->feesPaid->permanentlyDeleteById($feesPaidId);
                }
            } else {
                $this->feesPaid->permanentlyDeleteById($feesPaidId);
            }

            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function removeInstallmentFees($compulsoryFeesPaidID)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');
        try {
            DB::beginTransaction();

            // Get Fees Paid ID and Amount of Fees Transaction Table
            $installmentFeeTransaction = $this->compulsoryFee->findById($compulsoryFeesPaidID);
            $feesPaidId = $installmentFeeTransaction->fees_paid_id;
            $feesTransactionAmount = $installmentFeeTransaction->amount;

            $this->compulsoryFee->permanentlyDeleteById($compulsoryFeesPaidID); // Permanently Delete Fees Transaction Data

            // Check Fees Transactions Entry
            $feesPaidDataQuery = $this->feesPaid->builder()->where('id', $feesPaidId);
            if ($feesPaidDataQuery->count()) {
                // Get Fees Paid Data
                $feesPaidAmount = $feesPaidDataQuery->first()->amount; // Get Fees Paid Amount
                $finalAmount = $feesPaidAmount - $feesTransactionAmount; // Calculate Final Amount
                if ($finalAmount > 0) {
                    $this->feesPaid->update($feesPaidId, ['amount' => $finalAmount, 'is_fully_paid' => 0]); // Update Fees Paid Data with Final Amount
                } else {
                    $this->feesPaid->permanentlyDeleteById($feesPaidId);
                }
            } else {
                $this->feesPaid->permanentlyDeleteById($feesPaidId);
            }

            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function feesConfigIndex()
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-config');

        // List of the names to be fetched
        $names = array('currency_code', 'currency_symbol',);

        $settings = $this->schoolSettings->getBulkData($names); // Passing the array of names and gets the array of data
        $domain = request()->getSchemeAndHttpHost(); // Get Current Web Domain

        $stripeData = $this->paymentConfigurations->all()->where('payment_method', 'stripe')->first();
        return view('fees.fees_config', compact('settings', 'domain', 'stripeData'));
    }

    public function feesConfigUpdate(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-config');
        $request->validate(['stripe_status' => 'required', 'stripe_publishable_key' => 'required_if:stripe_status,1|nullable', 'stripe_secret_key' => 'required_if:stripe_status,1|nullable', 'stripe_webhook_secret' => 'required_if:stripe_status,1|nullable', 'stripe_webhook_url' => 'required_if:stripe_status,1|nullable', 'currency_code' => 'required|max:10', 'currency_symbol' => 'required|max:5',]);
        try {
            $this->paymentConfigurations->updateOrCreate(['payment_method' => strtolower('stripe')], ['api_key' => $request->stripe_publishable_key, 'secret_key' => $request->stripe_secret_key, 'webhook_secret_key' => $request->stripe_webhook_secret, 'status' => $request->stripe_status]);


            // Store Currency Code and Currency Symbol in School Settings
            $settings = array('currency_code', 'currency_symbol');

            $data = array();
            foreach ($settings as $row) {
                $data[] = [
                    "name" => $row,
                    "data" => $row == 'school_name' ? str_replace('"', '', $request->$row) : $request->$row, "type" => "string"
                ];
            }

            $this->schoolSettings->upsert($data, ["name"], ["data"]);
            Cache::flush();

            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function feesTransactionsLogsIndex()
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');
        $session_year_all = $this->sessionYear->all(['id', 'name', 'default']);
        $classes = $this->classes->builder()->orderByRaw('CONVERT(name, SIGNED) asc')->with('medium', 'stream', 'sections')->get();
        $mediums = $this->medium->builder()->orderBy('id', 'ASC')->get();

        $months = sessionYearWiseMonth();

        return response(view('fees.fees_transaction_logs', compact('classes', 'mediums', 'session_year_all', 'months')));
    }

    public function feesTransactionsLogsList(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        //Fetching Students Data on Basis of Class Section ID with Relation fees paid
        $sql = $this->paymentTransaction->builder()->with('user:id,first_name,last_name');

        if (!empty($request->search)) {
            $search = $request->search;
            $sql->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orwhere('order_id', 'LIKE', "%$search%")->orwhere('payment_id', 'LIKE', "%$search%")
                    ->orwhere('payment_gateway', 'LIKE', "%$search%")->orwhere('amount', 'LIKE', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")->orwhere('last_name', 'LIKE', "%$search%");
                    });
            });
        }

        if (!empty($request->payment_status)) {
            $sql->where('payment_status', $request->payment_status);
        }

        if ($request->month) {
            $sql->whereMonth('created_at', $request->month);
        }

        $total = $sql->count();
        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /* START : Fees Paid Module */
    public function feesPaidListIndex()
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');

        // Fees Data With Few Selected Data
        $fees = $this->fees->builder()->select(['id', 'name','class_id'])->get();
        $classes = $this->classes->all(['*'], ['medium', 'sections']);
        //        $session_year_all = $this->sessionYear->builder()->where('default', 1)->get();
        $session_year_all = $this->sessionYear->all(['id', 'name', 'default']);
        $class_section = $this->classSection->builder()->with('class', 'class.stream', 'section', 'medium')->get();
        $months = sessionYearWiseMonth();
        return response(view('fees.fees_paid', compact('fees', 'classes', 'session_year_all', 'months', 'class_section')));
    }

    public function feesPaidList(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $feesId = (int)request('fees_id');
        $requestSessionYearId = (int)request('session_year_id');
        $class_section_id = request('class_section_id');
        $class_id = request('class_id');
        $settings = $this->cache->getSchoolSettings();

        $sessionYearId = $requestSessionYearId ?? $this->cache->getDefaultSessionYear()->id;
        $fees = null;
        if ($feesId) {
            $fees = $this->fees->findById($feesId, ['*'], ['fees_class_type.fees_type:id,name', 'installments:id,name,due_date,due_charges,fees_id', 'fees_paid' => function ($q) {
                $q->withSum('compulsory_fee', 'amount')
                    ->withSum('optional_fee', 'amount');
            }]);

            $sql = $this->user->builder()->role('Student')->select('id', 'first_name', 'last_name')->with([
                'student'          => function ($query) {
                    $query->select('id', 'class_section_id', 'user_id')->with(['class_section' => function ($query) {
                        $query->select('id', 'class_id', 'section_id', 'medium_id')->with('class:id,name', 'section:id,name', 'medium:id,name');
                    }]);
                }, 'optional_fees' => function ($query) {
                    $query->with('fees_class_type');
                }, 'fees_paid'     => function ($q) use ($fees) {
                    $q->where('fees_id', $fees->id);
                },
                'compulsory_fees'
            ])
                ->withSum(['compulsory_fees' => function ($q) use ($fees) {
                    $q->whereHas('fees_paid', function ($q) use ($fees) {
                        $q->where('fees_id', $fees->id);
                    });
                }], 'amount')
                ->withSum(['compulsory_fees' => function($q) use($fees) {
                    $q->whereHas('fees_paid', function ($q) use ($fees) {
                        $q->where('fees_id', $fees->id);
                    });
                }], 'due_charges')
                ->whereHas('student.class_section', function ($q) use ($fees) {
                    $q->where('class_id', $fees->class_id);
                })->whereHas('student.class_section', function ($q) use ($class_section_id, $class_id) {
                    if($class_id) {
                        $q->where('class_id', $class_id);
                    } 
                });
            if (!empty($_GET['search'])) {
                $search = $_GET['search'];
                $sql->where(function ($q) use ($search) {
                    $q->where('id', 'LIKE', "%$search%")->orWhere('first_name', 'LIKE', "%$search%")->orWhere('last_name', 'LIKE', "%$search%");
                });
            }

            $currencySymbol = $settings['currency_symbol'] ?? '';

            $total_compulsory_fees = ($fees->total_compulsory_fees * $sql->count());
            $total_optional_fees = ($fees->total_optional_fees * $sql->count());
            $total_fees = $total_compulsory_fees + $total_optional_fees;
            $fees_data = [
                'total_fees' => $total_fees,
                'total_compulsory_fees' => $total_compulsory_fees,
                'total_optional_fees' => $total_optional_fees,
            ];
            $fees_data['currency_symbol'] = $currencySymbol;

            // Total Collected Fees
            if (count($fees->fees_paid)) {
                $total_compulsory_fees_collected = $fees->fees_paid->sum('compulsory_fee_sum_amount');
                $total_optional_fees_collected = $fees->fees_paid->sum('optional_fee_sum_amount');
                $total_fees_collected = $total_compulsory_fees_collected + $total_optional_fees_collected;
                $fees_data['total_fees_collected'] = $total_fees_collected;
                $fees_data['total_compulsory_fees_collected'] = $total_compulsory_fees_collected;
                $fees_data['total_optional_fees_collected'] = $total_optional_fees_collected;
            }



            if ($request->paid_status == 0) {
                $sql->whereDoesntHave('fees_paid', function ($q) use ($fees) {
                    $q->where('fees_id', $fees->id);
                })->orWhereHas('fees_paid', function ($q) use ($fees) {
                    $q->where(['fees_id' => $fees->id, 'is_fully_paid' => 0]);
                });
            } else {

                if ($request->paid_status == 1) {
                    $sql->whereHas('fees_paid', function ($q) use ($fees) {
                        $q->where(['fees_id' => $fees->id, 'is_fully_paid' => 1]);
                    });
                } else {
                    $sql->whereHas('fees_paid', function ($q) use ($fees) {
                        $q->where(['fees_id' => $fees->id, 'is_fully_paid' => 0]);
                    });
                }
                
                if ($request->month) {
                    $sql->whereHas('fees_paid', function ($q) use ($request, $fees) {
                        $q->whereMonth('date', $request->month)
                        ->where('fees_id',$fees->id);
                    });
                }
    
                if ($request->payment_gateway == 'cash_cheque') {
                    $sql->whereHas('fees_paid.compulsory_fee', function ($q) use ($request) {
                        $q->whereIn('mode', ['Cash','Cheque']);
                    });
                }
    
                if ($request->payment_gateway == 'stripe_razorpay') {
                    $sql->whereHas('fees_paid.compulsory_fee.payment_transaction', function ($q) use ($request) {
                        $q->whereIn('payment_gateway', ['Stripe','Razorpay']);
                    });
                }

                if($request->online_offline_payment) {
                    $sql->whereHas('fees_paid.compulsory_fee', function ($q) use ($request) {
                        if($request->online_offline_payment == 2) {
                            // Offline
                            $q->whereIn('mode', ['Cash','Cheque']);
                        } else if ($request->online_offline_payment == 1) {
                            // Online
                            $q->whereIn('mode', ['Stripe','Razorpay']);
                        }
                    });
                }
                
            }

            


            $total = $sql->count();
            $sql->orderBy($sort, $order)->skip($offset)->take($limit);
            $res = $sql->get();

            $bulkData = array();
            $bulkData['total'] = $total;
            $rows = array();
            $no = 1;

            foreach ($res as $row) {
                $tempRow = $row->toArray();
                $fees_data['no'] = $no++;
                $tempRow['no'] = $fees_data;


                // Calculate Minimum amount for installment
                if (count($fees->installments) > 0) {
                    collect($fees->installments)->map(function ($data) use ($fees) {
                        $data['minimum_amount'] = $fees->total_compulsory_fees / count($fees->installments);
                        $data['total_amount'] = $data['minimum_amount'] + 0; //Due charges
                        return $data;
                    });
                }
                $tempRow['fees'] = $fees->toArray();
                // $tempRow['fees_status'] = null;
                $due_date = Carbon::parse($fees->due_date);
                $today_date = Carbon::now()->format('Y-m-d');

                if ($due_date->gt($today_date)) {
                    $tempRow['fees_status'] = null;
                } else {
                    $tempRow['fees_status'] = 2;
                }

                $operate = '<div class="dropdown"><button class="btn btn-xs btn-gradient-success btn-rounded btn-icon dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-dollar"></i></button><div class="dropdown-menu">';
                $operate .= '<a href="' . route('fees.compulsory.index', [$fees->id, $row->id]) . '" class="compulsory-data dropdown-item" title="' . trans('Compulsory Fees') . '"><i class="fa fa-dollar text-success mr-2"></i>' . trans('Compulsory Fees') . '</a>';

                if (count($fees->optional_fees) > 0) {
                    $operate .= '<div class="dropdown-divider"></div><a href="' . route('fees.optional.index', [$fees->id, $row->id]) . '" class="optional-data dropdown-item" title="' . trans('Optional Fees') . '"><i class="fa fa-dollar text-success mr-2"></i>' . trans('Optional Fees') . '</a>';
                }
                $operate .= '</div></div>&nbsp;&nbsp;';

                if (!empty($row->fees_paid)) {
                    $operate .= ($fees->session_year_id == $sessionYearId) ? $operate : "";
                    $operate .= BootstrapTableService::button('fa fa-file-pdf-o', route('fees.paid.receipt.pdf', $row->fees_paid->id), ['btn', 'btn-xs', 'btn-gradient-info', 'btn-rounded', 'btn-icon', 'generate-paid-fees-pdf'], ['target' => "_blank", 'data-id' => $row->fees_paid->id, 'title' => trans('generate_pdf') . ' ' . trans('fees')]);
                    $tempRow['fees_status'] = $row->fees_paid->is_fully_paid;
                }

                // if (!empty($row->fees_paid->is_fully_paid)) {
                //     $operate .= ($fees->session_year_id == $sessionYearId) ? $operate : "";
                //     $operate .= BootstrapTableService::button('fa fa-file-pdf-o', route('fees.paid.receipt.pdf', $row->fees_paid->id), ['btn', 'btn-xs', 'btn-gradient-info', 'btn-rounded', 'btn-icon', 'generate-paid-fees-pdf'], ['target' => "_blank", 'data-id' => $row->fees_paid->id, 'title' => trans('generate_pdf') . ' ' . trans('fees')]);
                //     $tempRow['fees_status'] = $row->fees_paid->is_fully_paid;
                // }

                if ($row->fees_paid) {
                    // $tempRow['paid_amount'] = $row->compulsory_fees_sum_amount + $row->compulsory_fees_sum_due_charges;
                    $tempRow['paid_amount'] = $row->compulsory_fees_sum_amount;    
                } else {
                    $tempRow['paid_amount'] = 0;
                }
                if ($row->fees_paid && isset($row->fees_paid->compulsory_fee[0]->mode)) {
                    $tempRow['payment_method'] = $row->fees_paid->compulsory_fee[0]->mode;
                }

                $tempRow['operate'] = $operate;
                $rows[] = $tempRow;
            }
            $bulkData['rows'] = $rows;
            return response()->json($bulkData);
        }


        $bulkData['total'] = 0;
        $bulkData['rows'] = $tempRow = [];
        return response()->json($bulkData);
    }

    public function feesPaidReceiptPDF($feesPaidId)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');
        try {
            $feesPaid = $this->feesPaid->builder()->where('id', $feesPaidId)->with([
                'fees.fees_class_type.fees_type',
                'compulsory_fee.installment_fee:id,name',
                'optional_fee' => function ($q) {
                    $q->with([
                        'fees_class_type' => function ($q) {
                            $q->select('id', 'fees_type_id')->with('fees_type:id,name');
                        }
                    ]);
                }
            ])->firstOrFail();

            $student = $this->student->builder()->with('user:id,first_name,last_name', 'class_section.class.stream', 'class_section.section', 'class_section.medium')->whereHas('user', function ($q) use ($feesPaid) {
                $q->where('id', $feesPaid->student_id);
            })->firstOrFail();

            $school = $this->cache->getSchoolSettings();

            $data = explode("storage/", $school['horizontal_logo'] ?? '');
            $school['horizontal_logo'] = end($data);

            if ($school['horizontal_logo'] == null) {
                $systemSettings = $this->cache->getSystemSettings();
                $data = explode("storage/", $systemSettings['horizontal_logo'] ?? '');
                $school['horizontal_logo'] = end($data);
            }

            $pdf = Pdf::loadView('fees.fees_receipt', compact('school', 'feesPaid', 'student'));
            return $pdf->stream('fees-receipt.pdf');
        } catch (Throwable $e) {
            return $e;
            ResponseService::errorRedirectResponse();
            return false;
        }
    }

    public function payCompulsoryFeesIndex($feesID, $studentID)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        //        ResponseService::noPermissionThenRedirect('fees-edit');
        $fees = $this->fees->findById($feesID, ['*'], ['fees_class_type.fees_type:id,name', 'installments:id,name,due_date,due_charges,due_charges_type,fees_id']);
        $oneInstallmentPaid = false;

        $student = $this->user->builder()->role('Student')->select('id', 'first_name', 'last_name')
            ->with(['student' => function ($query) {
                $query->select('id', 'class_section_id', 'user_id', 'guardian_id')->with(['class_section' => function ($query) {
                    $query->select('id', 'class_id', 'section_id', 'medium_id')->with('class:id,name', 'section:id,name', 'medium:id,name');
                }]);
            }, 'fees_paid'    => function ($q) use ($feesID) {
                $q->where('fees_id', $feesID)->withSum('compulsory_fee','amount')->with('compulsory_fee');
            }, 'compulsory_fees.advance_fees'])->findOrFail($studentID);

        $isFullyPaid = false;
        if (!empty($student->fees_paid) && $student->fees_paid->is_fully_paid) {
            // ResponseService::successRedirectResponse(route('fees.paid.index'), 'Compulsory Fees Already Paid');
            $isFullyPaid = true;
        }
        $installment_status = 0;
        if (count($fees->installments) > 0) {
            $installment_status = 1;
            $totalFeesAmount = $fees->total_compulsory_fees;
            $totalInstallments = count($fees->installments);

            collect($fees->installments)->map(function ($installment) use ($student, &$totalFeesAmount, &$totalInstallments, $fees, &$oneInstallmentPaid) {

                $installmentPaid = $student->compulsory_fees->first(function ($compulsoryFees) use ($installment) {
                    return $compulsoryFees->installment_id == $installment->id;
                });

                if (!empty($installmentPaid)) {
                    // Removing the Paid installments from total installments so that minimum amount can be calculated for the remaining installments.
                    --$totalInstallments;
                    $oneInstallmentPaid = true;
                    $totalFeesAmount -= $installmentPaid->amount;
                    $installment['is_paid'] = (object)$installmentPaid->toArray();
                    if ($totalInstallments) {
                        $installment['minimum_amount'] = $totalFeesAmount / $totalInstallments;    
                    }
                    
                    $installment['maximum_amount'] = $totalFeesAmount;
                } else {
                    $installment['is_paid'] = [];
                    $installment['minimum_amount'] = $totalFeesAmount / $totalInstallments;
                    $installment['maximum_amount'] = $totalFeesAmount;
                }
                if (new DateTime(date('Y-m-d')) > new DateTime($installment['due_date'])) {
                    if ($installment->due_charges_type == "percentage") {
                        $installment['due_charges_amount'] = ($installment['minimum_amount'] * $installment['due_charges']) / 100;
                    } else if ($installment->due_charges_type == "fixed") {
                        $installment['due_charges_amount'] = $installment->due_charges;
                    }
                } else {
                    $installment['due_charges_amount'] = 0;
                }

                $installment['total_amount'] = $installment['minimum_amount'] + $installment['due_charges_amount'];
                $fees->remaining_amount = $totalFeesAmount;
                return $installment;
            });
        }

        $due_charges = 0;
        $due_date = Carbon::createFromFormat('Y-m-d',$fees->getRawOriginal('due_date'));
        if ($due_date->isPast() && !$due_date->isToday()) {
            $due_charges = $fees->due_charges_amount;
        }

        $currencySymbol = $this->cache->getSchoolSettings('currency_symbol');
        return view('fees.pay-compulsory', compact('fees', 'student', 'oneInstallmentPaid', 'currencySymbol','isFullyPaid','due_charges','installment_status'));
    }

    public function payCompulsoryFeesStore(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');

        $request->validate([
            'fees_id'            => 'required|numeric',
            'student_id'         => 'required|numeric',
            'installment_mode'   => 'required|boolean',
            'installment_fees'   => 'array',
            'installment_fees' => 'required_if:installment_mode,1',

        ], [
            'installment_fees.required_if' => 'Please select at least one installment'
        ]);

        
        try {
            DB::beginTransaction();
            $fees = $this->fees->findById($request->fees_id, ['*'], ['fees_class_type.fees_type:id,name', 'installments:id,name,due_date,due_charges,fees_id']);
            //            if (count($fees->installments) > 0) {
            //                collect($fees->installments)->map(function ($data) use ($fees) {
            //                    $data['minimum_amount'] = $fees->total_compulsory_fees / count($fees->installments);
            //                    $data['total_amount'] = $data['minimum_amount']; //Due charges
            //                    return $data;
            //                });
            //            }

            $feesPaid = $this->feesPaid->builder()->where([
                'fees_id'    => $request->fees_id,
                'student_id' => $request->student_id
            ])->first();

            if (!empty($feesPaid) && $feesPaid->is_fully_paid) {
                ResponseService::errorResponse("Compulsory Fees already Paid");
            }

            $amount = 0;
            // If Fees Paid Doesn't Exists
            if ($request->installment_mode) {
                if (!empty($request->installment_fees)) {
                    $amount = array_sum(array_column($request->installment_fees, 'amount'));
                }
                $amount += $request->advance;
            } else {
                if ($request->enter_amount) {
                    $amount = $request->enter_amount;
                } else {
                    $amount = $request->total_amount;
                }
            }

            if (empty($feesPaid)) {
                $feesPaidResult = $this->feesPaid->create([
                    'date'                => date('Y-m-d', strtotime($request->date)),
                    'is_fully_paid'       => $amount >= $fees->total_compulsory_fees,
                    'is_used_installment' => $request->installment_mode,
                    'fees_id'             => $request->fees_id,
                    'student_id'          => $request->student_id,
                    'amount'              => $amount,
                ]);
            } else {
                $feesPaidResult = $this->feesPaid->update($feesPaid->id, [
                    'amount'        => $amount + $feesPaid->amount,
                    'is_fully_paid' => ($amount + $feesPaid->amount) >= $fees->total_compulsory_fees
                ]);
            }            
            if ($request->installment_mode == 1) {
                if (!empty($request->installment_fees)) {
                    foreach ($request->installment_fees as $installment_fee) {
                        $compulsoryFeeData = array(
                            'student_id'     => $request->student_id,
                            'type'           => 'Installment Payment',
                            'installment_id' => $installment_fee['id'],
                            'mode'           => $request->mode,
                            'cheque_no'      => $request->mode == 2 ? $request->cheque_no : null,
                            'amount'         => $installment_fee['amount'],
                            'due_charges'    => $installment_fee['due_charges'] ?? null,
                            'fees_paid_id'   => $feesPaidResult->id,
                            'date'           => date('Y-m-d', strtotime($request->date))
                        );
                        $this->compulsoryFee->create($compulsoryFeeData);
                    }
                } else {

                }
            } else {
                $compulsoryFeeData = array(
                    'type'         => 'Full Payment',
                    'student_id'   => $request->student_id,
                    'mode'         => $request->mode,
                    'cheque_no'    => $request->mode == 2 ? $request->cheque_no : null,
                    'amount'       => $amount,
                    'due_charges'  => $request->due_charges_amount ?? null,
                    'fees_paid_id' => $feesPaidResult->id,
                    'date'         => date('Y-m-d', strtotime($request->date))
                );
                $this->compulsoryFee->create($compulsoryFeeData);
            }


            // Add advance amount in installment
            if ($request->advance > 0) {
                $updateCompulsoryFees = $this->compulsoryFee->builder()->where('student_id', $request->student_id)->with('fees_paid')->whereHas('fees_paid', function ($q) use ($request) {
                    $q->where('fees_id', $request->fees_id);
                })->orderBy('id', 'DESC')->first();

                $updateCompulsoryFees->amount += $request->advance;
                $updateCompulsoryFees->save();

                FeesAdvance::create([
                    'compulsory_fee_id' => $updateCompulsoryFees->id,
                    'student_id'        => $request->student_id,
                    'parent_id'         => $request->parent_id,
                    'amount'            => $request->advance
                ]);
            }
            DB::commit();
            ResponseService::successResponse("Data Updated SuccessFully");
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, 'FeesController -> compulsoryFeesPaidStore method ');
            ResponseService::errorResponse();
        }
    }

    public function payOptionalFeesIndex($feesID, $studentID)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        //        ResponseService::noPermissionThenRedirect('fees-edit');
        // $fees = $this->fees->findById($feesID, ['*'], ['fees_class_type.fees_type:id,name', 'installments:id,name,due_date,due_charges,fees_id']);

        $fees = $this->fees->findById($feesID, ['*'], ['fees_class_type.fees_type:id,name', 'installments:id,name,due_date,due_charges,fees_id']);

        $student = $this->user->builder()->role('Student')->select('id', 'first_name', 'last_name')
            ->with(['student' => function ($query) {
                $query->select('id', 'class_section_id', 'user_id', 'session_year_id')->with(['class_section' => function ($query) {
                    $query->select('id', 'class_id', 'section_id', 'medium_id')->with('class:id,name', 'section:id,name', 'medium:id,name');
                }]);
            }, 'fees_paid'    => function ($q) use ($feesID) {
                $q->where('fees_id', $feesID)->first();
            }])->findOrFail($studentID);


        $optionalFeesData = $this->feesClassType->builder()
        ->where('fees_id',$feesID)
            ->where(['class_id' => $student->student->class_section->class_id, 'optional' => 1])
            ->with([
                'fees_type',
                'optional_fees_paid' => function ($query) use ($student) {
                    $query->where('student_id', $student->id)->whereHas('fees_paid', function ($subQuery1) use ($student) {
                        $subQuery1->whereHas('fees', function ($subQuery2) use ($student) {
                            $subQuery2->where('session_year_id', $student->student->session_year_id);
                        });
                    });
                }
            ])
            ->get();

        return view('fees.pay-optional', compact('fees', 'student', 'optionalFeesData'));
    }

    public function payOptionalFeesStore(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');
        $request->validate([
            'fees_id'    => 'required|numeric',
            'student_id' => 'required|numeric',
        ]);
        try {
            DB::beginTransaction();

            // First Store in Fees Paid table to get Fees Paid ID
            $feesPaid = $this->feesPaid->builder()->where([
                'fees_id'    => $request->fees_id,
                'student_id' => $request->student_id
            ])->first();

            // If Fees Paid Doesn't Exists
            if (empty($feesPaid)) {
                $feesPaidResult = $this->feesPaid->create([
                    'date'                => date('Y-m-d', strtotime($request->date)),
                    'is_fully_paid'       => 0,
                    'is_used_installment' => 0,
                    'fees_id'             => $request->fees_id,
                    'student_id'          => $request->student_id,
                    'amount'              => $request->total_amount,
                ]);
            } else {
                $feesPaidResult = $this->feesPaid->update($feesPaid->id, [
                    'amount' => $request->total_amount + $feesPaid->amount
                ]);
            }


            $optionalFeesPaymentData = array();

            // Loop to the Optional Fees
            if (!empty($request->fees_class_type)) {
                foreach ($request->fees_class_type as $key => $feesClassType) {
                    if (isset($feesClassType['id'])) {
                        $optionalFeesPaymentData[] = array(
                            'student_id'    => $request->student_id,
                            'class_id'      => $request->class_id,
                            'fees_class_id' => $feesClassType['id'],
                            'mode'          => $request->mode,
                            'cheque_no'     => $request->mode == 2 ? $request->cheque_no : null,
                            'amount'        => $feesClassType['amount'],
                            'fees_paid_id'  => $feesPaidResult->id,
                            'date'          => date('Y-m-d', strtotime($request->date)),
                            'status'        => "Success",
                            'created_at'    => now(),
                            'updated_at'    => now()
                        );
                    }
                }
            }

            $this->optionalFee->createBulk($optionalFeesPaymentData);

            DB::commit();
            ResponseService::successResponse("Data Updated SuccessFully");
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, 'FeesController -> compulsoryFeesPaidStore method ');
            ResponseService::errorResponse();
        }
    }
    /* END : Fees Paid Module */

    public function feesOverDue($class_section_id)
    {
        ResponseService::noFeatureThenRedirect('Fees Management');
        ResponseService::noPermissionThenRedirect('fees-paid');

        try {
            // $sessionYear = $this->cache->getDefaultSessionYear();
            $class_id = $this->classSection->builder()->where('id', $class_section_id)->pluck('class_id')->toArray();

            // Ensure $class_id is a single value rather than an array if you expect a single class_id
            $class_id = reset($class_id);

            $today = Carbon::now()->format('Y-m-d');
            $student_ids = [];

           
            $fees = $this->fees->builder()->whereDate('due_date', '<', $today)->with('installments:id,name,due_date,due_charges,fees_id')->where('class_id', $class_id)->get();
           
            foreach ($fees as $fee) {
                $sql = $this->user->builder()
                    ->role('Student')
                    ->select('id', 'first_name', 'last_name')->where('status', 1)
                    ->with(['fees_paids' => function ($query) use ($fee) {
                            $query->where('fees_id', $fee->id);
                        },
                    ])->whereDoesntHave('fees_paids', function ($q) use ($fee) {
                        $q->where('fees_id', $fee->id);
                    })->orwhereHas('fees_paids', function ($query) use ($fee, $today) {
                        $query->where('fees_id', $fee->id)->where('is_fully_paid', 0)
                            ->where(function ($q) use ($fee, $today) {
                                $q->where('is_used_installment', true)
                                    ->whereHas('fees', function ($q) use ($today) {
                                        $q->whereHas('installments', function ($q) use ($today) {
                                            $q->whereDate('due_date', '<', $today);
                                        });
                                    });
                            });
                    });
                $student_ids = array_merge($student_ids, $sql->get()->pluck('id')->toArray());    
                  
            }
            $student_ids = array_unique($student_ids);

            $students = $this->student->builder()->with('guardian')->whereIn('user_id', $student_ids)->where('class_section_id', $class_section_id)
                ->whereHas('user', function ($query) {
                    $query->where('status', 1);
                })->with(['user', 'user.fees_paids', 'class_section' => function ($query) {
                    $query->select('id', 'class_id', 'section_id', 'medium_id')->with('class:id,name', 'section:id,name', 'medium:id,name');
                }])->get();

            // $guardian_ids = $students->pluck('guardian_id')->toArray();
    
            // // send notification to guardians
            // $title = "Overdue Fees";
            // $body = "Dear Guardian, the fees for your ward are overdue. Please make the necessary payment at the earliest.";
            // $type = 'Notification';
            
            // // Send the notification to the guardians
            // send_notification($guardian_ids, $title, $body, $type);

            ResponseService::successResponse("Data Fetched SuccessFully", $students);
        } catch (Throwable $e) {
            if (Str::contains($e->getMessage(), [
                'does not exist','file_get_contents'
            ])) {
                DB::commit();
                ResponseService::warningResponse("Data Stored successfully. But App push notification not send.");
            } else {
                DB::rollback();
                ResponseService::logErrorResponse($e, 'FeesController -> feesOverDue method ');
                ResponseService::errorResponse();
            }
        }
    }

    public function studentAccountDeactivate(Request $request)
    {
        try {
            // Retrieve the IDs from the request
            $checkedIds = explode(',', $request->checked_ids);
            $users = $this->user->builder()->whereIn('id', $checkedIds)->get();
            // dd($users);
            foreach ($users as $user) {
                $user->status = 0;
                $user->update();
            }

            ResponseService::successResponse("Students Deactived Account Successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, 'FeesController -> studentAccountDeactivate method ');
            ResponseService::errorResponse();
        }
    }
}
