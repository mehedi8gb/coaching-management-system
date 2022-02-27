<?php

namespace App\Http\Controllers;
use App\SmFeesType;
use App\SmFeesGroup;
use App\SmFeesMaster;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SmFeesGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        
        try{
            $fees_groups = SmFeesGroup::where('school_id',Auth::user()->school_id)->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($fees_groups, null);
            }
    
            return view('backEnd.feesCollection.fees_group', compact('fees_groups'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => "required"
        ]);

        $is_duplicate = SmFeesGroup::where('school_id', Auth::user()->school_id)->where('name', $request->name)->first();
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

        try{
            $visitor = new SmFeesGroup();
            $visitor->name = $request->name;
            $visitor->description = $request->description;
            $visitor->school_id = Auth::user()->school_id;;
            $result = $visitor->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Fees Group has been created successfully.');
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
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function edit(Request $request, $id)
    {
       
        try{
            $fees_group = SmFeesGroup::find($id);
            $fees_groups = SmFeesGroup::where('school_id',Auth::user()->school_id)->get();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['fees_group'] = $fees_group->toArray();
                $data['fees_groups'] = $fees_groups->toArray();
                return ApiBaseMethod::sendResponse($data, null);
            }
            return view('backEnd.feesCollection.fees_group', compact('fees_group', 'fees_groups'));
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => "required|max:200",

        ]);

        $is_duplicate = SmFeesGroup::where('school_id', Auth::user()->school_id)->where('id','!=', $request->id)->where('name', $request->name)->first();
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


        try{
            $visitor = SmFeesGroup::find($request->id);
            $visitor->name = $request->name;
            $visitor->description = $request->description;
            $result = $visitor->save();
    
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($result) {
                    return ApiBaseMethod::sendResponse(null, 'Fees Group has been updated successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($result) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('fees-group');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

    public function deleteGroup1(Request $request)
    {
        
        try{

            $check_fees_group_in_master=SmFeesMaster::where('fees_group_id',$request->id)->first();
            $check_fees_group_in_type=SmFeesType::where('fees_group_id',$request->id)->first();

            if ($check_fees_group_in_type!=null && $check_fees_group_in_master!=null) {
                Toastr::warning('Operation Failed', 'Used Data');
                return redirect('fees-group');
            }

            $fees_group = SmFeesGroup::destroy($request->id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($fees_group) {
                    return ApiBaseMethod::sendResponse(null, 'Fees Group has been deleted successfully.');
                } else {
                    return ApiBaseMethod::sendError('Something went wrong, please try again.');
                }
            } else {
                if ($fees_group) {
                    Toastr::success('Operation successful', 'Success');
                    return redirect('fees-group');
                } else {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect('fees-group');
                }
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }
    public function deleteGroup(Request $request)
    {
        
        try{
            $tables = \App\tableList::getTableList('fees_group_id', $request->id);
            try {
                if ($tables==null) {
                    $fees_group = SmFeesGroup::destroy($id);
                    if ($fees_group) {
                        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                            if ($fees_group) {
                                return ApiBaseMethod::sendResponse(null, 'Fees Group has been deleted successfully');
                            } else {
                                return ApiBaseMethod::sendError('Something went wrong, please try again');
                            }
                        } else {
                            if ($fees_group) {
                                Toastr::success('Operation successful', 'Success');
                                return redirect()->back();
                            } else {
                                Toastr::error('Operation Failed', 'Failed');
                                return redirect()->back();
                            }
                        }
                    } else {
                        Toastr::error('Operation Failed', 'Failed');
                        return redirect()->back();
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
            } catch (\Exception $e) {
                //dd($e->getMessage(), $e->errorInfo);
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }catch (\Exception $e) {
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

}