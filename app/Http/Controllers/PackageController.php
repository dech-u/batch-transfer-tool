<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\SubscriptionFeature;
use App\Repositories\Feature\FeatureInterface;
use App\Repositories\Package\PackageInterface;
use App\Repositories\PackageFeature\PackageFeatureInterface;
use App\Repositories\Subscription\SubscriptionInterface;
use App\Repositories\SubscriptionFeature\SubscriptionFeatureInterface;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PackageController extends Controller
{

    private PackageInterface $package;
    private FeatureInterface $feature;
    private PackageFeatureInterface $packageFeature;
    private SubscriptionInterface $subscription;
    private SubscriptionFeatureInterface $subscriptionFeature;

    public function __construct(PackageInterface $package, FeatureInterface $feature, PackageFeatureInterface $packageFeature, SubscriptionInterface $subscription, SubscriptionFeatureInterface $subscriptionFeature)
    {
        $this->package = $package;
        $this->feature = $feature;
        $this->packageFeature = $packageFeature;
        $this->subscription = $subscription;
        $this->subscriptionFeature = $subscriptionFeature;
    }


    public function index()
    {
        ResponseService::noPermissionThenRedirect('package-list');
        return view('package.index');
    }


    public function create()
    {
        ResponseService::noPermissionThenRedirect('package-create');
        $features = $this->feature->builder()->activeFeatures()->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->get();
        $vps_features = $this->feature->builder()->where('required_vps', 1)->get();
        return view('package.create', compact('features', 'vps_features'));
    }


    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('package-create');
        $validator = Validator::make($request->all(), [
            'name'           => 'required',
            'student_charge' => 'required_if:type,1|nullable|numeric|decimal:0,2',
            'staff_charge'   => 'required_if:type,1|nullable|numeric|decimal:0,2',
            'feature_id'     => 'required',
            'days'           => 'required|numeric',
            'no_of_students' => 'required_if:type,0|nullable|numeric|decimal:0,2',
            'no_of_staffs'   => 'required_if:type,0|nullable|numeric|decimal:0,2',
            'charges'        => 'required_if:type,0|nullable|numeric|decimal:0,2',

        ], [
            'feature_id.required' => trans('please_select_at_least_one_feature')
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        $request['student_charge'] = $request->student_charge ?? 0;
        $request['staff_charge'] = $request->staff_charge ?? 0;

        $request['no_of_students'] = $request->no_of_students ?? 0;
        $request['no_of_staffs'] = $request->no_of_staffs ?? 0;
        $request['charges'] = $request->charges ?? 0;

        try {
            DB::beginTransaction();
            $packageData = [
                ...$request->all(),
                'highlight'                  => $request->highlight ?? 0,
            ];

            // Create package
            $package = $this->package->create($packageData);
            // Create package features
            $packageFeatures = [];
            foreach ($request->feature_id as $feature) {
                $packageFeatures[] = [
                    'package_id' => $package->id,
                    'feature_id' => $feature
                ];
            }
            $this->packageFeature->upsert($packageFeatures, ['package_id', 'feature_id'], ['package_id', 'feature_id']); // Store package features
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller -> Store method');
            ResponseService::errorResponse();
        }
    }


    public function show()
    {
        ResponseService::noPermissionThenRedirect('package-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'rank');
        $order = request('order', 'ASC');
        $search = request('search');
        $showDeleted = request('show_deleted');
        $type = request('type');
        $today_date = Carbon::now()->format('Y-m-d');

        $sql = $this->package->builder()->with('package_feature.feature')->where('is_trial', 0)
            ->withCount(['subscription' => function ($q) use ($today_date) {
                $q->whereDate('start_date', '<=', $today_date)->whereDate('end_date', '>=', $today_date);
            }])
            ->where(function ($query) use ($search) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%")
                            ->orWhere('description', 'LIKE', "%$search%")
                            ->orWhere('tagline', 'LIKE', "%$search%");
                    });
                });
            })->when(!empty($showDeleted), function ($q) {
                $q->onlyTrashed();
            });

        if (isset($type)) {
            $sql->where('type', $type);
        }


        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            $operate = '';
            if (empty($showDeleted)) {
                if ($row->status == 0) {
                    $operate .= BootstrapTableService::button('fa fa-check', '#', ['change-package-status', 'btn-gradient-success'], ['title' => trans("publish_package"), 'data-id' => $row->id]);
                } else {
                    $operate .= BootstrapTableService::button('fa fa-times', '#', ['change-package-status', 'btn-gradient-warning'], ['title' => trans("unpublished_package"), 'data-id' => $row->id]);
                }
                $operate .= BootstrapTableService::editButton(route('package.edit', $row->id), false);
                $operate .= BootstrapTableService::deleteButton(route('package.destroy', $row->id));
            } else {
                $operate .= BootstrapTableService::restoreButton(route('package.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('package.trash', $row->id));
            }

            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['used_by'] = $row->subscription_count;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function edit($id)
    {
        ResponseService::noPermissionThenRedirect('package-edit');
        $package = $this->package->findById($id);
        $features = $this->feature->builder()->activeFeatures()->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->get();
        $vps_features = $this->feature->builder()->where('required_vps', 1)->get();
        return view('package.edit', compact('package', 'features', 'vps_features'));
    }

    public function update(Request $request, $id)
    {
        ResponseService::noPermissionThenSendJson('package-edit');
        $validator = Validator::make($request->all(), [
            'name'           => 'required',
            'student_charge' => 'required_if:type,1|nullable|numeric|decimal:0,2',
            'staff_charge'   => 'required_if:type,1|nullable|numeric|decimal:0,2',
            'feature_id'     => 'required',
            'days'           => 'required|numeric',
            'no_of_students' => 'required_if:type,0|nullable|numeric|decimal:0,2',
            'no_of_staffs'   => 'required_if:type,0|nullable|numeric|decimal:0,2',
            'charges'        => 'required_if:type,0|nullable|numeric|decimal:0,2',
        ], [
            'feature_id.required'   => trans('please_select_at_least_one_feature'),
        ]);



        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Instant effects features
            if ($request->instant_effects) {
                $today_date = Carbon::now()->format('Y-m-d');
                // $subscriptions = $this->subscription->builder()->where('package_id',$id)->where('start_date','<=',$today_date)->where('end_date','>=',$today_date)->doesntHave('subscription_bill')->get();
                $subscriptions = SubscriptionFeature::groupBy('subscription_id')->pluck('subscription_id');
            }

            // 0 => Prepaid, 1 => Postpaid
            if ($request->type == 1) {
                $request['student_charge'] = $request->student_charge ?? 0;
                $request['staff_charge'] = $request->staff_charge ?? 0;

                $request['no_of_students'] = 0;
                $request['no_of_staffs'] = 0;
                $request['charges'] = 0;
            } else {
                $request['student_charge'] = 0;
                $request['staff_charge'] = 0;

                $request['no_of_students'] = $request->no_of_students ?? 0;
                $request['no_of_staffs'] = $request->no_of_staffs ?? 0;
                $request['charges'] = $request->charges ?? 0;
            }

            $packageData = [
                ...$request->all(),
                'highlight'                  => $request->highlight ?? 0,
            ];

            $package = $this->package->update($id, $packageData);
            $package_features = $package->package_feature->pluck('feature_id')->toArray();
            $packageFeatures = [];
            $subscription_features = [];
            foreach ($request->feature_id as $feature) {
                $packageFeatures[] = [
                    'package_id' => $id,
                    'feature_id' => $feature
                ];

                // Remove package features
                $key = array_search($feature, $package_features);
                if ($key !== false) {
                    unset($package_features[$key]);
                }

                if ($request->instant_effects) {
                    foreach ($subscriptions as $key => $subscription) {
                        $subscription_features[] = [
                            'subscription_id' => $subscription,
                            'feature_id' => $feature
                        ];
                    }
                }
            }
            if ($request->instant_effects) {
                // Update features
                $this->subscriptionFeature->upsert($subscription_features, ['subscription_id', 'feature_id'], ['subscription_id', 'feature_id']);
                // Delete features
                $delete_subscription_features = $subscriptions;
                $this->subscriptionFeature->builder()->whereIn('subscription_id', $delete_subscription_features)->whereIn('feature_id', $package_features)->delete();
            }
            $this->packageFeature->upsert($packageFeatures, ['feature_id', 'package_id'], ['package_id', 'feature_id']);

            // Delete package features
            $this->packageFeature->builder()->whereIn('feature_id', $package_features)->where('package_id', $id)->delete();
            DB::commit();
            // Package update will affect all the schools that is why Cache::flush is used here.
            Cache::flush();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller -> Update method');
            ResponseService::errorResponse();
        }
    }

    public function destroy($id)
    {
        //
        ResponseService::noPermissionThenSendJson('package-delete');
        try {
            DB::beginTransaction();
            $this->package->update($id, ['status' => 0]);
            $this->package->deleteById($id);
            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller -> Destroy method');
            ResponseService::errorResponse();
        }
    }


    public function status($id)
    {
        ResponseService::noAnyPermissionThenSendJson(['package-create', 'package-edit']);
        try {
            DB::beginTransaction();
            $package = $this->package->findById($id);
            $package_status = ['status' => $package->status == 1 ? 0 : 1];
            $this->package->update($id, $package_status);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller -> change status method');
            ResponseService::errorResponse();
        }
    }


    public function restore($id)
    {
        ResponseService::noPermissionThenSendJson('package-edit');

        try {
            DB::beginTransaction();
            $this->package->restoreById($id);
            DB::commit();
            ResponseService::successResponse('Data Restored Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller -> Restore method');
            ResponseService::errorResponse();
        }
    }


    public function trash($id)
    {
        ResponseService::noPermissionThenSendJson('package-delete');
        try {
            DB::beginTransaction();
            // TODO:: Add condition this package cannot be subscribed to any school.
            $package = $this->package->findOnlyTrashedById($id);
            if (count($package->subscription)) {
                ResponseService::errorResponse('cannot_delete_because_data_is_associated_with_other_data');
            } else {
                $this->package->permanentlyDeleteById($id);
            }


            DB::commit();
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller -> Trash method');
            ResponseService::errorResponse();
        }
    }

    public function change_rank(Request $request)
    {
        ResponseService::noPermissionThenRedirect('package-edit');

        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ], [
            'ids' => trans('No Package Data Found'),
        ]);
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
        try {
            DB::beginTransaction();
            $ids = json_decode($request->ids, false, 512, JSON_THROW_ON_ERROR);
            $update = [];
            foreach ($ids as $key => $id) {
                $update[] = [
                    'id' => $id,
                    'rank' => ($key + 1)
                ];
            }
            $this->package->upsert($update, ['id'], ['rank']);
            DB::commit();
            ResponseService::successResponse('Rank Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller -> Change Rank method');
            ResponseService::errorResponse();
        }
    }

    public function features_list()
    {
        if (!Auth::user()->hasRole('School Admin')) {
            ResponseService::noAnyPermissionThenRedirect(['addons-list', 'addons-create', 'addons-edit', 'addons-delete', 'package-list', 'package-create', 'package-edit', 'package-delete']);
        }
        return view('features');
    }

    public function features_show()
    {
        if (!Auth::user()->hasRole('School Admin')) {
            ResponseService::noAnyPermissionThenRedirect(['addons-list', 'addons-create', 'addons-edit', 'addons-delete', 'package-list', 'package-create', 'package-edit', 'package-delete']);
        }

        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');
        $search = request('search');

        $sql = Feature::activeFeatures()->orderBy($sort, $order);

        // Get all features as collection to handle array filtering in PHP
        $features = $sql->get();

        // Step 2: Filter features based on search term
        if ($search) {
            $features = $features->filter(function ($row) use ($search) {
                $matchesPermission = false;

                // Get permissions for each feature
                $permissions = $this->features_permission($row->name);

                // Search in feature name and permissions array
                if (stripos($row->name, $search) !== false) {
                    return true;
                }

                if (is_array($permissions)) {
                    foreach ($permissions as $permission) {
                        if (stripos($permission, $search) !== false) {
                            $matchesPermission = true;
                            break;
                        }
                    }
                }

                return $matchesPermission;
            });
        }

        // Step 3: Get the total number of filtered records
        $total = $features->count();

        // Step 4: Paginate the filtered results
        $features = $features->slice($offset, $limit);

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = $offset + 1;

        // Step 5: Prepare response data
        foreach ($features as $row) {
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['permission'] = $this->features_permission($row->name);
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        // Step 6: Return JSON response
        return response()->json($bulkData);
    }

    public function features_enable(Request $request)
    {
        ResponseService::noAnyPermissionThenRedirect(['package-edit', 'package-create']);
        try {
            DB::beginTransaction();
            $update = [];
            foreach ($request->feature_id as $key => $value) {

                $update[] = [
                    'id' => $key,
                    'status' => $value
                ];
            }

            $this->feature->upsert($update, ['id'], ['status']);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, 'Package Controller ->Enable Features method');
            ResponseService::errorResponse();
        }
    }

    public function features_permission($feature = null)
    {
        // TODO : Understand this code
        $permissions = array(
            "Student Management" => array(
                "Manage Student",
                "Manage Guardian",
                "Reset Password",
                "Manage Student Admission Custom Fields"
            ),
            "Academics Management" => array(
                "Manage Medium",
                "Manage Section",
                "Manage Subject",
                "Manage Semester",
                "Manage Stream",
                "Manage Shift",
                "Manage Class",
                "Manage Class Section",
                "Manage Class Teacher",
                "Promote Student"
            ),
            "Slider Management" => array(
                "Manage Slider for App & Web"
            ),
            "Teacher Management" => array(
                "Manage Teacher",
                "Bulk Upload"
            ),
            "Session Year Management" => array(
                "Manage Session Year"
            ),
            "Holiday Management" => array(
                "Manage Holiday"
            ),
            "Timetable Management" => array(
                "Manage Timetable",
                "Create Timetable Using Drag & Drop"
            ),
            "Attendance Management" => array(
                "Manage Attendance"
            ),
            "Exam Management" => array(
                "Manage Exam",
                "Manage Exam Timetable",
                "Manage Grade",
                "Manage Student Result",
                "Manage Online Exam",
                "Manage Online Exam Question",
                "Manage Online Result"
            ),
            "Lesson Management" => array(
                "Manage Lesson",
                "Manage Lesson Topic"
            ),
            "Announcement Management" => array(
                "Manage Announcement"
            ),
            "Staff Management" => array(
                "Manage Role",
                "Manage Staff",
                "Bulk Upload"
            ),
            "Assignment Management" => array(
                "Manage Assignment",
                "Manage Assignment Submission with Scores"
            ),
            "Expense Management" => array(
                "Manage Category",
                "Manage Expense",
                "Manage Staff Payroll",
                "Staff Allowances & Deductions",
            ),
            "Staff Leave Management" => array(
                "Manage Staff Leaves",
                "Manage Leave Allowances",
                "Manage LWP (Leave Without Pay)"
            ),
            "Fees Management" => array(
                "Manage Class Wise Fees",
                "Manage Payment [ Cash/Cheque, Online ]",
                "Manage Fees Receipt",
                "Partial Pay Fees with Receipt"
            ),
            "School Gallery Management" => array(
                "Manage School Gallery",
                "Upload Multiple Images & Youtube Links"
            ),
            "ID Card - Certificate Generation" => array(
                "Generate students - staff ID cards & certificates",
                "Drag-and-Drop Certificate Builder"
            ),
            "Website Management" => array(
                "Custom Domain",
                "Content Management System (CMS)",
                "User-Friendly Interface",
                "Dynamic Content Editing",
                "Online Student Admission",
                "Third-Patry API (Google Captcha)"
            ),
            "Chat Module" => array(
                "Parent - Teacher",
                "Student - Teacher",
                "Teacher - Staff",
            )
        );

        if ($feature) {
            return $permissions[$feature] ?? null;
        }
        return $permissions;
    }
}
