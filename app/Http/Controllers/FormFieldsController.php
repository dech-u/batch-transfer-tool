<?php

namespace App\Http\Controllers;

use App\Repositories\FormField\FormFieldsInterface;
use App\Rules\uniqueForSchool;
use App\Services\BootstrapTableService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Validator;

class FormFieldsController extends Controller {

    // Initializing the schools Repository
    private FormFieldsInterface $formFields;

    public function __construct(FormFieldsInterface $formFields) {
        $this->formFields = $formFields;
    }

    public function index() {
        ResponseService::noPermissionThenRedirect('form-fields-list');
        $formFields = $this->formFields->defaultModel()->orderBy('rank')->get();
        return view('form-fields.index', compact('formFields'));
    }

    public function store(Request $request) {

        // Check if the user has permission to create form fields
        ResponseService::noAnyPermissionThenRedirect(['form-fields-create']);
    
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'user_type' => 'required',
            
        ]);
    
        // If validation fails, return the first error
        if ($validator->fails()) {
            ResponseService::validationError($validator->errors()->first());
        }
    
        // Check if the combination of name, user_type, and school_id already exists
       
        try {
            // Check the Type and populate the default data based on it
            if (in_array($request->type, ['dropdown', 'radio', 'checkbox'])) {
                // Create an array of options from the default data
                $defaultData = array_map(function ($data) {
                    return $data['option'];
                }, $request->default_data);
    
                // Encode the default data into a JSON string
                $defaultData = json_encode($defaultData, JSON_THROW_ON_ERROR);
            } else {
                $defaultData = null;
            }
            
            // Get the latest rank and increment it (for ordering)
            $getRank = $this->formFields->builder()->latest()->pluck('rank')->first();
    
            $checkName = $this->formFields->builder()
            ->where('name', $request->name)
            ->where('user_type', $request->user_type)
            ->where('school_id', Auth::user()->school_id)
            ->first();
            
            // If a duplicate exists, return an error message
            if ($checkName) {
                ResponseService::errorResponse('The name already exists for this user type and school');
            }
    
            // Prepare the data array to insert into the database
            $data = [
                'name' => $request->name,
                'type' => $request->type,
                'is_required' => $request->required == 'on' ? 1 : 0,  // Convert 'on' to 1, otherwise 0
                'default_values' => $defaultData,
                'rank' => isset($getRank) ? ++$getRank : 1, // Increment the rank or set to 1 if first record
                'user_type' => $request->user_type,
                'school_id' => Auth::user()->school_id,
            ];
            // Insert the data into the form_fields table
            $this->formFields->create($data);
    
            // Return success response
            ResponseService::successResponse('Data Stored Successfully');
    
        } catch (Throwable $e) {
            // Log the error and return a generic error message
            ResponseService::logErrorResponse($e, "Form Fields Controller -> Store method");
            ResponseService::errorResponse('An error occurred while storing the data.');
        }
    }

    public function show() {
        ResponseService::noAnyPermissionThenRedirect(['form-fields-list']);
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'rank');
        $order = request('order', 'ASC');
        $search = request('search');
        $showDeleted = request('show_deleted');

        $sql = $this->formFields->builder()
            //search query
            ->where(function ($query) use ($search) {

                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%");
                    });
                });
            })
            ->when(!empty($showDeleted), function ($query) {
                $query->onlyTrashed();
            })->when(!empty(request('filter_all_user_type')), function ($query) {
                $query->where('user_type', request('filter_all_user_type'));
            });

        // if (!empty(request('filter_all_user_type'))) {
        //     dd( $sql->toSql());
        //     $sql->whereHas('child.class_section', function ($q) {
        //         $q->where('user_type', request('filter_all_user_type'));
        //     });
        // }
        
        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            if ($showDeleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::restoreButton(route('form-fields.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('form-fields.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate = BootstrapTableService::editButton(route('form-fields.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('form-fields.destroy', $row->id));
            }
            $tempRow = $row->toArray();
            $tempRow['user_type'] = $row->user_type == 1 ? 'Student' : ($row->user_type == 2 ? 'Teacher/Staff' : '');
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function update(Request $request, $id) {
        ResponseService::noPermissionThenSendJson('form-fields-edit');
        $request->validate([
            'name' => 'required|unique:form_fields,name,' . $id,
        ]);
        try {
            $defaultData = null;
            // Check the Type and then populate the default data according to it
            if ($request->type == 'dropdown' || $request->type == 'radio' || $request->type == 'checkbox') {

                // Make Array for Values Only
                $defaultData = array();
                foreach ($request->edit_default_data as $data) {
                    $defaultData[] = $data["option"];
                }
                $defaultData = json_encode($defaultData, JSON_THROW_ON_ERROR);
            }

            $data = array(
                'name'           => $request->name,
                'is_required'    => $request->edit_required == 'on' ? 1 : 0,
                'default_values' => $defaultData
            );
            // Pass the Data Array to Repository to Update Data in Database
            $this->formFields->update($id, $data);

            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Form Fields Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    public function destroy($id) {
        ResponseService::noPermissionThenSendJson('form-fields-delete');
        try {
            $this->formFields->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "FormFields Controller -> Delete method");
            ResponseService::errorResponse();
        }
    }

    public function updateRankOfFields(Request $request) {
        ResponseService::noAnyPermissionThenRedirect(['form-fields-edit']);

        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
            ]);
            if ($validator->fails()) {
                ResponseService::errorResponse($validator->errors()->first());
            }
            $data = array();
            foreach ($request->ids as $key => $value) {
                $data[] = array(
                    'id'   => $value,
                    'rank' => $key + 1
                );
            }
            $this->formFields->upsert($data, ['id'], ['rank']);
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Form Fields Controller -> Update Rank method");
            ResponseService::errorResponse();
        }
    }

    public function restore(int $id) {
        ResponseService::noPermissionThenSendJson('form-fields-delete');
        try {
            $this->formFields->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function trash($id) {
        ResponseService::noPermissionThenSendJson('form-fields-delete');
        try {
            $this->formFields->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "FormFields Controller -> Trash Method");
            ResponseService::errorResponse();
        }
    }

    public function schoolIndex() {
        ResponseService::noPermissionThenRedirect('school-custom-field-list');
        $formFields = $this->formFields->defaultModel()->orderBy('rank')->get();
        return view('form-fields.school-field', compact('formFields'));
    }

    public function schoolStore(Request $request) {
        ResponseService::noAnyPermissionThenRedirect(['school-custom-field-create']);
        $request->validate([
            'name' => 'required|unique:form_fields,name',
            'type' => 'required',
        ]);
        try {
            // Check the Type and then populate the default data according to it
            if ($request->type == 'dropdown' || $request->type == 'radio' || $request->type == 'checkbox') {

                // Make Array for Values Only
                $defaultData = array();
                foreach ($request->default_data as $data) {
                    $defaultData[] = $data["option"];
                }
                $defaultData = json_encode($defaultData, JSON_THROW_ON_ERROR);
            } else {
                $defaultData = null;
            }

            $getRank = $this->formFields->builder()->latest()->pluck('rank')->first();

            $data = array(
                'name'           => $request->name,
                'type'           => $request->type,
                'is_required'    => $request->required == 'on' ? 1 : 0,
                'default_values' => $defaultData,
                'rank'           => isset($getRank) ? ++$getRank : 1,
            );
            // Pass the Data Array to Repository to Add Data in Database
            $this->formFields->create($data);

            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Form Fields Controller -> School Store method");
            ResponseService::errorResponse();
        }
    }

    public function schoolShow() {
        ResponseService::noAnyPermissionThenRedirect(['school-custom-field-list']);
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'rank');
        $order = request('order', 'ASC');
        $search = request('search');
        $showDeleted = request('show_deleted');

        $sql = $this->formFields->builder()
            //search query
            ->where(function ($query) use ($search) {

                $query->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                });
                });
            })
            ->when(!empty($showDeleted), function ($query) {
                $query->onlyTrashed();
            });

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;

        foreach ($res as $row) {
            if ($showDeleted) {
                //Show Restore and Hard Delete Buttons
                $operate = BootstrapTableService::restoreButton(route('school-custom-field.restore', $row->id));
                $operate .= BootstrapTableService::trashButton(route('school-custom-field.trash', $row->id));
            } else {
                //Show Edit and Soft Delete Buttons
                $operate = BootstrapTableService::editButton(route('school-custom-field.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('school-custom-field.destroy', $row->id));
            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }


    public function schoolUpdate(Request $request, $id) {
        ResponseService::noPermissionThenSendJson('school-custom-field-edit');
        $request->validate([
            'name' => 'required|unique:form_fields,name,' . $id,
        ]);
        try {
            $defaultData = null;
            // Check the Type and then populate the default data according to it
            if ($request->type == 'dropdown' || $request->type == 'radio' || $request->type == 'checkbox') {

                // Make Array for Values Only
                $defaultData = array();
                foreach ($request->edit_default_data as $data) {
                    $defaultData[] = $data["option"];
                }
                $defaultData = json_encode($defaultData, JSON_THROW_ON_ERROR);
            }

            $data = array(
                'name'           => $request->name,
                'is_required'    => $request->edit_required == 'on' ? 1 : 0,
                'default_values' => $defaultData
            );
            // Pass the Data Array to Repository to Update Data in Database
            $this->formFields->update($id, $data);

            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Form Fields Controller -> School Update method");
            ResponseService::errorResponse();
        }
    }

    public function schoolDestroy($id) {
        ResponseService::noPermissionThenSendJson('school-custom-field-delete');
        try {
            $this->formFields->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "FormFields Controller -> School Delete method");
            ResponseService::errorResponse();
        }
    }

    public function schoolUpdateRankOfFields(Request $request) {
        ResponseService::noAnyPermissionThenRedirect(['school-custom-field-edit']);

        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
            ]);
            if ($validator->fails()) {
                ResponseService::errorResponse($validator->errors()->first());
            }
            $data = array();
            foreach ($request->ids as $key => $value) {
                $data[] = array(
                    'id'   => $value,
                    'rank' => $key + 1
                );
            }
            $this->formFields->upsert($data, ['id'], ['rank']);
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Form Fields Controller -> School Update Rank method");
            ResponseService::errorResponse();
        }
    }

    public function schoolRestore(int $id) {
        ResponseService::noPermissionThenSendJson('school-custom-field-delete');
        try {
            $this->formFields->findOnlyTrashedById($id)->restore();
            ResponseService::successResponse("Data Restored Successfully");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e);
            ResponseService::errorResponse();
        }
    }

    public function schoolTrash($id) {
        ResponseService::noPermissionThenSendJson('school-custom-field-delete');
        try {
            $this->formFields->findOnlyTrashedById($id)->forceDelete();
            ResponseService::successResponse("Data Deleted Permanently");
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "FormFields Controller -> School Trash Method");
            ResponseService::errorResponse();
        }
    }
}
