<?php

namespace App\Http\Controllers;

use App\YearCheck;
use App\SmClassTime;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SmClassTimeController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $class_times = SmClassTime::latest()->where('school_id',Auth::user()->school_id)->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($class_times, null);
            }
            return view('backEnd.academics.class_time', compact('class_times'));
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'time_type' => 'required',
            'period' => 'required|max:200',
            // 'period' => 'required|max:200|unique:sm_class_times,period',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $class_time = new SmClassTime();
            $class_time->type = $request->time_type;
            $class_time->period = $request->period;
            $class_time->start_time = date('H:i:s', strtotime($request->start_time));
            $class_time->end_time = date('H:i:s', strtotime($request->end_time));
            $class_time->school_id = Auth::user()->school_id;
            $result = $class_time->save();

            $type = $request->time_type;

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse($type, 'time has been created successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {

                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect()->back();
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function show($id = null)
    {
        Toastr::error('Operation Failed', 'Failed');
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        try {
            $class_time = SmClassTime::find($id);
            $class_times = SmClassTime::where('school_id',Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['class_time'] = $class_time->toArray();
                $data['class_times'] = $class_times->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.academics.class_time', compact('class_time', 'class_times'));
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'time_type' => 'required',
            'period' => 'required|max:200',
            // 'period' => 'required|max:200|unique:sm_class_times,period,' . $request->id,
            'start_time' => 'required|before:end_time',
            'end_time' => 'required'
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $class_time = SmClassTime::find($request->id);
            $class_time->type = $request->time_type;
            $class_time->period = $request->period;
            $class_time->start_time = date('H:i:s', strtotime($request->start_time));
            $class_time->end_time = date('H:i:s', strtotime($request->end_time));
            $result = $class_time->save();

            $type = $request->time_type;

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse($type, 'Class Room has been updated successfully');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('class-time');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        try {
            $class_id_key = 'class_period_id';
            $exam_id_key = 'exam_period_id';

            $class = \App\tableList::getTableList($class_id_key, $id);
            $exam = \App\tableList::getTableList($exam_id_key, $id);
            $tables = $class . '' . $exam;
            //return $tables;
            try {
                if ($tables == null) {
                    $delete_query = SmClassTime::destroy($id);
                    if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                        if ($delete_query) {
                            return ApiBaseMethod::sendResponse(null, 'Class has been deleted successfully');
                        } else {
                            return ApiBaseMethod::sendError('Something went wrong, please try again.');
                        }
                    } else {
                        if ($delete_query) {
                            Toastr::success('Operation successful', 'Success');
                            return redirect()->back();
                        } else {
                            Toastr::error('Operation Failed', 'Failed');
                            return redirect()->back();
                        }
                    }
                } else {
                    $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                    Toastr::error($msg, 'Failed');
                    return redirect()->back();
                }
            } catch (\Illuminate\Database\QueryException $e) {
                $msg = 'This data already used in  : ' . $tables . ' Please remove those data first';
                Toastr::error($msg, 'Failed');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
