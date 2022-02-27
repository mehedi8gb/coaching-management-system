<?php

namespace App\Http\Controllers;
use App\Role;
use App\User;
use App\SmClass;
use App\SmStaff;
use App\SmParent;
use App\SmStudent;
use App\YearCheck;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Modules\RolePermission\Entities\InfixRole;

class SmLoginAccessControlController extends Controller
{
    public function loginAccessControl(){
    	
		try{
			$roles = InfixRole::where('id','!=',1)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
			$classes = SmClass::where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
	
			return view('backEnd.systemSettings.login_access_control', compact('roles', 'classes'));
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function searchUser(Request $request){

    	if($request->role == ""){
    		$request->validate([
    			'role' => 'required'
    		]);
    	}elseif($request->role == "2"){
    		$request->validate([
    			'role' => 'required',
    			'class' => 'required',
    		]);
    	}

		try{
			$role = $request->role;
			$roles = InfixRole::where('id','!=',1)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
			$classes = SmClass::where('created_at', 'LIKE', '%' . YearCheck::getYear() . '%')->where('school_id',Auth::user()->school_id)->get();
	
			if($request->role == "2"){
	
	
				$students = SmStudent::query();
				$students->where('active_status', 1);
				if($request->class != ""){
					$students->where('class_id', $request->class);
				}
				 if($request->section != ""){
					$students->where('section_id', $request->section);
				}
				$students = $students->where('school_id',Auth::user()->school_id)->get();
	
				return view('backEnd.systemSettings.login_access_control', compact('students', 'role', 'roles', 'classes'));
	
			}elseif($request->role == "3"){
				$parents = SmParent::where('active_status', 1)->where('school_id',Auth::user()->school_id)->get();
				return view('backEnd.systemSettings.login_access_control', compact('parents', 'role', 'roles', 'classes'));
			}else{
				$staffs = SmStaff::where('active_status', 1)->where('role_id', $request->role)->where('school_id',Auth::user()->school_id)->get();
				return view('backEnd.systemSettings.login_access_control', compact('staffs', 'role', 'roles', 'classes'));
			}	
			return view('backEnd.systemSettings.login_access_control', compact('roles', 'classes'));
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }

    public function loginAccessPermission(Request $request){
    	
		try{
			if($request->status == 'on'){
				$status = 1;
			}else{
				$status = 0;
			}			
			$user = User::find($request->id);
			$user->access_status = $status;
			$user->save();
	
			return response()->json($request->status);
		}catch (\Exception $e) {
		   Toastr::error('Operation Failed', 'Failed');
		   return redirect()->back(); 
		}
    }
}