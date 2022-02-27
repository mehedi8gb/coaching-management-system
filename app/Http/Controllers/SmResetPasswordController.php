<?php

namespace App\Http\Controllers;
use App\User;
use App\ApiBaseMethod;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;

class SmResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function resetStudentPassword(Request $request)
    {

        try {
            if ($request->new_password == "") {
                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    return ApiBaseMethod::sendError('New Password and id field are required');
                }
                return redirect('student-login-report')->with('message-dander', 'New Password field is required');
            } else {
                $password = Hash::make($request->new_password);
                $user = User::find($request->id);
                $user->password = $password;
                $result = $user->save();

                if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                    if ($result) {
                        return ApiBaseMethod::sendResponse(null, 'Password reset has been successfully');
                    } else {
                        return ApiBaseMethod::sendError('Something went wrong, please try again');
                    }
                } else {
                    if ($result) {
                        Toastr::success('Password reset has been successfully', 'Success');
                        return redirect('student-login-report');
                        // return redirect('student-login-report')->with('message-success', 'Password reset has been successfully');
                    } else {
                        Toastr::error('Operation Failed', 'Failed');
                        return redirect('student-login-report');
                        // return redirect('student-login-report')->with('message-danger', 'Something went wrong, please try again');
                    }
                }
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
