<?php

namespace App\Http\Controllers;

use App\User;
use App\SmStaff;
use App\Envato\Envato;
use GuzzleHttp\Client;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\ConnectException;

class InstallController extends Controller
{

    public function is_valid_domain_name($domain_name)
    {
		return TRUE;
    }


    //##Step01
    public function index()
    {
        if (Schema::hasTable('sm_general_settings') && Schema::hasTable('users')) {
            $users = DB::table('users')->get();
            if ($users->count() > 0) {
                return redirect('login');
            } else {
                Session::put('step1', 1);
                return view('install.welcome_to_infix');
            }
        } else {
            Session::put('step1', 1);
            return view('install.welcome_to_infix');
        }
    }

    //##Step2
    public function CheckPurchaseVerificationPage()
    {
        // if (Schema::hasTable('sm_general_settings') && Schema::hasTable('users')) {
        //     return redirect('login');
        // } else {
        if (Session::get('step1') != 1) {
            return redirect('install');
        } else {
            Session::put('step2', 2);
            return view('install.check_purchase_page');
        }
        // }
    }
    //##Step03
    public function CheckVerifiedInput(Request $request)
    {
        // if (Schema::hasTable('sm_general_settings') && Schema::hasTable('users')) {
        //     return redirect('login');
        // } else {
        if (Session::get('step1') != 1) {
            return redirect('install');
        } elseif (Session::get('step2') != 2) {
            return redirect('check-purchase-verification');
        } else {
                                Session::put('step3', 3);
                                Session::flash("message-success", "Congratulations! Purchase code is verified.");
                                return redirect('check-environment');
        }
        // }
    }

    //##Step04
    public function checkEnvironmentPage()
    {
        // if (Schema::hasTable('sm_general_settings') && Schema::hasTable('users')) {
        //     return redirect('login');
        // } else {


        if (Session::get('step1') != 1) {
            return redirect('install');
        } elseif (Session::get('step2') != 2) {
            return redirect('check-purchase-verification');
        } elseif (Session::get('step3') != 3) {
            return redirect('check-purchase-verification');
        } else {
            try {
                $path = '';
                $folders = array(
                    $path . "/route",
                    $path . "/resources",
                    $path . "/public",
                    $path . "/storage",
                );

                Session::put('step4', 4);
                return view('install.checkEnvironmentPage')->with('folders', $folders);
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
        // }
    }


    //##Step06
    public function confirmation()
    {

        try {
            return view('install.confirmation');
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }








    //##Step05
    public function checkEnvironment(Request $request)
    {
        // if (Schema::hasTable('sm_general_settings') && Schema::hasTable('users')) {
        //     return redirect('login');
        // } else {
        if (Session::get('step1') != 1) {
            return redirect('install');
        } elseif (Session::get('step2') != 2) {
            return redirect('check-purchase-verification');
        } elseif (Session::get('step3') != 3) {
            return redirect('check-purchase-verification');
        } elseif (Session::get('step4') != 4) {
            return redirect('check-environment');
        } else {
            try {
                if (phpversion() >= '7.1' && OPENSSL_VERSION_NUMBER > 0x009080bf && extension_loaded('mbstring') && extension_loaded('tokenizer') && extension_loaded('xml') && extension_loaded('ctype')  && extension_loaded('json')) {
                    Session::put('step5', 5);
                    return redirect('system-setup-page');
                } else {
                    Session::flash("message-danger", "Ops! Extension are disabled.  Please check requirements!");
                    return redirect()->back()->with("message-danger", "Ops! Extension are disabled.  Please check requirements!");
                }
            } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
        }
        // }
    }



    //##Step06
    public function systemSetupPage()
    {

        if (Schema::hasTable('sm_general_settings') && Schema::hasTable('users')) {
            return redirect('login');
        } else {
            if (Session::get('step1') != 1) {
                return redirect('install');
            } elseif (Session::get('step2') != 2) {
                return redirect('check-purchase-verification');
            } elseif (Session::get('step3') != 3) {
                return redirect('check-purchase-verification');
            } elseif (Session::get('step4') != 4) {
                return redirect('check-environment');
            } else {
                try {
                    Session::put('step6', 6);
                    return view('install.systemSetupPage');
                } catch (\Exception $e) {
                    Toastr::error('Operation Failed', 'Failed');
                    return redirect()->back();
                }
            }
        }
    }



    //##Step07
    public function confirmInstalling(Request $request)
    {
        set_time_limit(2700);
        $this->validate($request, [
            'institution_name' => 'required',
            'system_admin_email' => 'required',
            'system_admin_password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);


        try {
            $sql = base_path('database/infixv4_5.sql');
            DB::unprepared(file_get_contents($sql));
        } catch (\Exception $e) {
            Artisan::call('migrate:refresh');
        }

        try {
            Session::put('system_admin_email', $request->system_admin_email);
            Session::put('system_admin_password', $request->system_admin_password);

            // Artisan::call('migrate:refresh');
            // if ($request->install_mode == 1) {
            //     Artisan::call('db:seed');
            // }


            if (Schema::hasTable('migrations')) {
                $migration = DB::table('migrations')->get();
                if (count($migration) > 0) {
                    $id = 1;
                    $setting = SmGeneralSettings::find($id);
                    if ($setting == "") {
                        $setting = new SmGeneralSettings();
                    }
                    $setting->school_name = @$request->input('institution_name');
                    $setting->email = @$request->input('system_admin_email');
                    $setting->system_purchase_code = Session::get('purchasecode');
                    $setting->system_activated_date = date('Y-m-d');
                    $setting->system_domain = Session::get('domain');
                    $setting->save();
                }

                $user = User::find(1);
                if (empty($user)) {
                    $user = new User();
                }

                $user->role_id = 1;
                $user->username = $request->input('system_admin_email');
                $user->full_name = 'System Administrator';
                $user->email = $request->input('system_admin_email');
                $user->password = Hash::make($request->input('system_admin_password'));
                $user->save();

                $staff = SmStaff::find(1);
                if (empty($staff)) {
                    $staff = new SmStaff();
                }
                $staff->user_id  = $user->id;
                $staff->first_name  = 'System';
                $staff->last_name  = 'Administrator';
                $staff->full_name  = 'System Administrator';
                $staff->email  = $request->input('system_admin_email');
                $staff->save();
                return redirect('confirmation');
            }
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect('system-setup-page');
        }
    }


    public function verifiedCode()
    {

        // dd('k');
        try {
            if (Schema::hasTable('sm_general_settings')) {
                $GetData = DB::table('sm_general_settings')->find(1);
                if (!empty($GetData)) {
                    $UserData = Envato::verifyPurchase($GetData->system_purchase_code);
                    if (!empty($UserData['verify-purchase']['item_id']) && (User::$item == $UserData['verify-purchase']['item_id'])) {
                        return redirect('/login');
                    }
                } else {
                    return view('install.verified_code');
                }
            } else {
                return redirect('install');
            }
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function verifiedCodeStore(Request $request)
    {

        try {
            $envatouser = htmlspecialchars($request->input('envatouser'));
            $purchasecode = htmlspecialchars($request->input('purchasecode'));
            $domain = htmlspecialchars($request->input('installationdomain'));

            $obj = Envato::verifyPurchase($purchasecode);


            if (!empty($obj)) {
                foreach ($obj as $data) {
                    if (!empty($data['item_id'])) {

                        $setting = SmGeneralSettings::first();
                        $setting->system_domain = $domain;
                        $setting->envato_user = $envatouser;
                        $setting->system_purchase_code = $purchasecode;
                        $setting->envato_item_id = $data['item_id'];
                        $setting->system_activated_date = date('Y-m-d');
                        $setting->save();

                        $url = Session::get('url');

                        return redirect($url);
                    }
                }
            } else {
                Session::flash("message-danger", "Ops! Purchase Code is not vaild. Please try again.");
                return redirect()->back();
            }
            Session::flash("message-danger", "Ops! Purchase Code is not vaild. Please try again.");
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }
}
