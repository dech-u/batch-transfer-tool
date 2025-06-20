<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\Languages\LanguageInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Repositories\User\UserInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Stmt\TryCatch;
use Throwable;

class LanguageController extends Controller
{
    private LanguageInterface $language;
    private CachingService $cache;

    public function __construct(LanguageInterface $language, CachingService $cachingService)
    {
        $this->language = $language;
        $this->cache = $cachingService;
    }

    public function index()
    {
        ResponseService::noPermissionThenRedirect('language-create');
        return view('settings.language_setting');
    }

    public function language_sample()
    {
        ResponseService::noPermissionThenRedirect('language-create');
        $filePath = base_path("resources/lang/en.json");
        $headers = ['Content-Type: application/json'];
        $fileName = 'language.json';
        if (File::exists(base_path("resources/lang/en.json"))) {
            return response()->download($filePath, $fileName, $headers);
        }

        ResponseService::errorResponse();
    }

    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('language-create');
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:languages,code',
            'file' => 'required|mimes:json',
        ]);

        try {

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = $request->code . '.' . $file->getClientOriginalExtension();
                $file->move(base_path('resources/lang/'), $filename);
            }

            $languageData = array(
                'name'   => $request->name,
                'code'   => $request->code,
                'status' => 0,
                'is_rtl' => $request->rtl ?? 0,
                'file'   => $filename ?? NULL
            );

            $this->language->create($languageData);
            $this->cache->removeSystemCache(config('constants.CACHE.SYSTEM.LANGUAGE'));
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Language Controller -> Store method");
            ResponseService::errorResponse();
        }
    }

    public function show()
    {
        ResponseService::noPermissionThenRedirect('language-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');

        $sql = $this->language->builder()
            ->where(function ($query) use ($search) {
                $query->when('search', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")
                            ->orwhere('name', 'LIKE', "%$search%")
                            ->orwhere('code', 'LIKE', "%$search%")
                            ->orwhere('status', 'LIKE', "%$search%");
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
            $operate = "";
            $operate .= BootstrapTableService::editButton(route('language.update', $row->id));
            $operate .= BootstrapTableService::button('fa fa-file', route('language.json.file',$row->code), [ 'btn-gradient-info'], ['title' => trans("file")]);
            if ($row->code != "en") {
                $operate .= BootstrapTableService::deleteButton(route('language.destroy', $row->id));

            }
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }

        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    public function update($id, Request $request)
    {
        ResponseService::noPermissionThenRedirect('language-edit');
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:languages,code,' . $id,
            'file' => 'required|mimes:json',
        ]);

        try {
            $languageData = array(
                'name'   => $request->name,
                'code'   => $request->code,
                'is_rtl' => $request->rtl ?? 0,
            );

            $languageDB = $this->language->findById($id);
            if ($request->hasFile('file')) {
                $request->validate(['file' => 'required|mimes:json',]);
                if (File::exists(base_path("resources/lang/") . $languageDB->file)) {
                    File::delete(base_path("resources/lang/") . $languageDB->file);
                }
                $file = $request->file('file');
                $filename = $request->code . '.' . $file->getClientOriginalExtension();
                $file->move(base_path('resources/lang/'), $filename);
                //                $language['file'] = $filename;
            }

            $this->language->update($id, $languageData);
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Language Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    public function destroy($id)
    {
        ResponseService::noPermissionThenSendJson('language-delete');
        try {
            $this->language->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Language Controller -> Delete method");
            ResponseService::errorResponse();
        }
    }

    public function set_language(Request $request)
    {
        Session::put('locale', $request->lang);
        Session::save();
        $language = $this->language->builder()->where('code', $request->lang)->first();
        Session::put('language', $language);
        app()->setLocale(Session::get('locale'));
        

        Session::put('landing_locale', $request->lang);
        Session::save();
        Session::put('language', $language);
        app()->setLocale(Session::get('landing_locale'));

        Cache::flush();
        if (Auth::user()) {
            User::where('id',Auth::user()->id)->update(['language' => $language->code]);
        }
        return redirect()->back();
    }

    public function language_file($code = null)
    {
        try {
            $filePath = base_path("resources/lang");
            $filePath .= '/'.$code.'.json';
            $headers = ['Content-Type: application/json'];
            $fileName = $code.'.json';
            if (File::exists($filePath)) {
                return response()->download($filePath, $fileName, $headers);
            }
            return redirect()->back()->with('error','No file found.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','No file found.');
        }        
    }
}
