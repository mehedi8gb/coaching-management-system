<?php

namespace App\Http\Controllers;

use App\SmClass;
use App\SmSection;
use App\tableList;
use App\YearCheck;
use App\ApiBaseMethod;
use App\SmClassSection;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SmClassController extends Controller
{
    public $date;
    public function __construct()
    {
        $this->middleware('PM');
        $this->date = SmGeneralSettings::first()->academic_Year->year;
    }


    public function index(Request $request)
    {


        try {
            $sections = SmSection::where('active_status', '=', 1)->where('created_at', 'LIKE', '%' . $this->date . '%')->where('school_id', Auth::user()->school_id)->get();
            $classes = SmClass::where('created_at', 'LIKE', '%' . $this->date . '%')->where('active_status', '=', 1)->where('school_id', Auth::user()->school_id)->where('created_at', 'LIKE', '%' . $this->date . '%')->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['classes'] = $classes->toArray();
                $data['sections'] = $sections->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.academics.class', compact('classes', 'sections'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make(
            $input,
            [
                // 'name' => "required|max:200|unique:sm_classes,class_name",
                'name' => "required|max:200",
            ]
        );


        $is_duplicate = SmClass::where('school_id', Auth::user()->school_id)->where('class_name', $request->name)->where('created_at', 'LIKE', '%' . $this->date . '%')->first();
        if ($is_duplicate) {
            Toastr::error('Duplicate name found!', 'Failed');
            return redirect()->back()->withErrors($validator)->withInput();
        }


        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $class = new SmClass();
            $class->class_name = $request->name;
            $class->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
            $class->school_id = Auth::user()->school_id;
            $class->save();
            $class->toArray();
            try {
                $sections = $request->section;

                if ($sections != '') {
                    foreach ($sections as $section) {
                        $smClassSection = new SmClassSection();
                        $smClassSection->class_id = $class->id;
                        $smClassSection->section_id = $section;
                        $smClassSection->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                        $smClassSection->school_id = Auth::user()->school_id;
                        $smClassSection->save();
                    }
                }
                DB::commit();

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendResponse(null, 'Class has been created successfully');
                }
                Toastr::success('Operation successful', 'Success');
                return redirect()->back();
            } catch (\Exception $e) {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendError('Something went wrong, please try again.');
        }
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {


        try {
            $classById = SmCLass::find($id);

            $sectionByNames = SmClassSection::select('section_id')->where('class_id', '=', $classById->id)->where('created_at', 'LIKE', '%' . $this->date . '%')->get();

            $sectionId = array();
            foreach ($sectionByNames as $sectionByName) {
                $sectionId[] = $sectionByName->section_id;
            }

            $sections = SmSection::where('active_status', '=', 1)->where('created_at', 'LIKE', '%' . $this->date . '%')->where('school_id', Auth::user()->school_id)->get();

            $classes = SmClass::where('active_status', '=', 1)->orderBy('id', 'desc')->where('created_at', 'LIKE', '%' . $this->date . '%')->where('school_id', Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['sections'] = $sections->toArray();
                $data['classes'] = $classes->toArray();
                $data['classById'] = $classById;
                $data['sectionId'] = $sectionId;
                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('backEnd.academics.class', compact('classById', 'classes', 'sections', 'sectionId'));
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make(
            $input,
            [
                // 'name' => "required|unique:sm_classes,class_name," . $request->id,
                'name' => "required|max:200",
                'section' => 'required|array',
            ],
            [
                'section.required' => 'At least one checkbox required!'
            ]
        );

        $is_duplicate = SmClass::where('school_id', Auth::user()->school_id)->where('id', '!=', $request->id)->where('class_name', $request->name)->where('created_at', 'LIKE', '%' . $this->date . '%')->first();
        if ($is_duplicate) {
            Toastr::error('Duplicate name found!', 'Failed');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        SmCLassSection::where('class_id', $request->id)->delete();



        DB::beginTransaction();

        try {
            $class = SmClass::find($request->id);
            $class->class_name = $request->name;
            $class->save();
            $class->toArray();
            try {
                $sections = $request->section;

                foreach ($sections as $section) {
                    $smClassSection = new SmClassSection();
                    $smClassSection->class_id = $class->id;
                    $smClassSection->section_id = $section;
                    $smClassSection->created_at = YearCheck::getYear() . '-' . date('m-d h:i:s');
                    $smClassSection->school_id = Auth::user()->school_id;
                    $smClassSection->save();
                }

                DB::commit();

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendResponse(null, 'Class has been updated successfully');
                }
                Toastr::success('Operation successful', 'Success');
                return redirect('class');
            } catch (\Exception $e) {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendError('Something went wrong, please try again.');
        }
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
    }

    public function delete(Request $request, $id)
    {
        try {
            $tables = tableList::getTableList('class_id', $id);

            try {

                DB::beginTransaction();

                $class_sections = SmClassSection::where('class_id', $id)->get();
                foreach ($class_sections as $key => $class_section) {
                    $class_section_delete_query = SmClassSection::destroy($class_section->id);
                }

                $delete_query = $section = SmClass::destroy($id);
                DB::commit();
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    if ($section) {
                        return ApiBaseMethod::sendResponse(null, 'Class has been deleted successfully');
                    } else {
                        return ApiBaseMethod::sendError('Something went wrong, please try again.');
                    }
                } else {
                    if ($delete_query) {
                        Toastr::success('Operation successful', 'Success');
                        return redirect('class');
                    } else {
                        DB::rollback();
                        Toastr::error('Operation Failed', 'Failed');
                        return redirect()->back();
                    }
                }
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }




            // if(ApiBaseMethod::checkUrl($request->fullUrl())){
            //     if($section){
            //         return ApiBaseMethod::sendResponse(null, 'Section has been deleted successfully');
            //     }else{
            //         return ApiBaseMethod::sendError('Something went wrong, please try again.');
            //     }
            // }else{

            //     if($section){
            //         return redirect()->back()->with('message-success-delete', 'Section has been deleted successfully');
            //     }else{
            //         return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
            //     }

            // }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
