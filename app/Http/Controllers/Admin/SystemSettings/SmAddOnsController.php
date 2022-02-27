<?php

namespace App\Http\Controllers\Admin\SystemSettings;

use Exception;
use Throwable;
use App\Envato\Envato;
use GuzzleHttp\Client;
use App\SmGeneralSettings;
use App\InfixModuleManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SmAddOnsController extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
        // User::checkAuth();
    }

    protected $systemConfigModule = "FeesCollection";
    public function setActive($active)
    {
        return $this->json()->set('active', $active)->save();
    }



    public function ModuleRefresh()
    {
        try {
            exec('php composer.phar dump-autoload');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Toastr::success('Refresh successful', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Your server doesn\'t allow this refresh.' . $e->getMessage(), 'Failed');
            return redirect()->back();
        }
    }

    public function ManageAddOns()
    {
        try {
            $module_list = [];
            $is_module_available = Module::all();
            return view('backEnd.systemSettings.ManageAddOns', compact('is_module_available', 'module_list'));
        } catch (\Throwable $th) {
            Toastr::error($th->getMessage(), 'Failed');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Failed');
            return redirect()->back();
        }
    }



    public function moduleAddOnsEnable($name)
    {
        $module_tables = [];
        $module_tables_names = [];
        $dataPath = 'Modules/' . $name . '/' . $name . '.json';        // // Get the contents of the JSON file
        $strJsonFileContents = file_get_contents($dataPath);
        $array = json_decode($strJsonFileContents, true);
        $migrations = $array[$name]['migration'];
        $names = $array[$name]['names'];
        $version = $array[$name]['versions'][0];
        $url = $array[$name]['url'][0];
        $notes = $array[$name]['notes'][0];

        try {

            DB::beginTransaction();
            $check_enable_status= Module::find($name)->isDisabled();
            $s = InfixModuleManager::where('name', $name)->first();
            if (empty($s)) {
                $s = new InfixModuleManager();
            }
            $s->name = $name;
            $s->notes = $notes;
            $s->version = $version;
            $s->update_url = $url;
            $s->installed_domain = url('/');
            $s->activated_date = date('Y-m-d');
            $s->save();
            DB::commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $modulestatus =  Module::find($name)->disable();
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }



        $is_module_available = 'Modules/' . $name . '/Providers/' . $name . 'ServiceProvider.php';

        if (file_exists($is_module_available)) {

            $modulestatus =  Module::find($name)->isDisabled();

            $is_migrate = false;
            //if module status is disable
            if ($modulestatus) {
                foreach ($migrations as $table=> $path) {
                    if($table != "no_table"){
                        if( ! Schema::hasTable($table) && file_exists($path)){

                            try {
                                Artisan::call('migrate:refresh', [
                                    '--force' => true,
                                    '--path'=>$path
                                ]);
                                $is_migrate = true;

                            }
                            catch (\Exception $e) {
                                $is_migrate = false;
                                Log::info($e->getMessage());
                                $modulestatus =  Module::find($name)->disable();
                                $data['error'] = 'Migration failed !';
                                return response()->json($data, 200);

                            }
                        }elseif(file_exists($path)){
                            $is_migrate = true;
                        }

                    }
                    else{

                        Artisan::call('migrate:refresh', [
                            '--force' => true,
                            '--path'=>$path
                        ]);
                        $is_migrate = true;

                    }

                }

                if($is_migrate){
                    $ModuleManage = Module::find($name)->enable();
                    $data['data'] = 'enable';
                    $data['success'] = 'Operation success! Thanks you.';
                    session()->forget('all_module');

                    $all_module = [];
                    $modules = InfixModuleManager::select('name')->get();
                    foreach ($modules as $module) {
                        $all_modules[] = $module->name;
                    }
                    session()->put('all_module', $all_modules);
                    return response()->json($data, 200);
                }


            }
            //if module status is enable
            else {
                $ModuleManage = Module::find($name)->disable();
                $data['data'] = 'disable';
                $data['Module'] = $ModuleManage;
                session()->forget('all_module');
                $all_module = [];
                $modules = InfixModuleManager::select('name')->get();
                foreach ($modules as $module) {
                    $all_modules[] = $module->name;
                }
                session()->put('all_module', $all_modules);
                $data['success'] = 'Operation success! Thanks you.';
                return response()->json($data, 200);
            }


        }
    }



    public function ManageAddOnsValidation(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'purchase_code' => 'required',
            'name' => 'required'
        ]);

        $code = $request->purchase_code;
        $email = $request->email;
        $name = $request->name;
        if ($request->purchase_code == "") {
            Toastr::error('Purchase code is required', 'Failed');
            return redirect()->back();
        }
        if (Config::get('app.app_pro')) {
            if ($request->email == "") {
                Toastr::error('Email is required', 'Failed');
                return redirect()->back();

            }
        }


        if (Config::get('app.app_pro')) {
            try {

                $client = new Client();
                $product_info = $client->request('GET', 'https://sp.uxseven.com/api/module/' . $code . '/' . $email);
                $product_info = $product_info->getBody()->getContents();
                $product_info = json_decode($product_info);

                if (!empty($product_info->products[0])) {
                    // added a new column in sm general settings
                    if (!Schema::hasColumn('sm_general_settings', $name)) {
                        Schema::table('sm_general_settings', function ($table) use ($name) {
                            $table->integer($name)->default(1)->nullable();
                        });
                    }

                    try {
                        $dataPath = 'Modules/' . $name . '/' . $name . '.json';        // // Get the contents of the JSON file
                        $strJsonFileContents = file_get_contents($dataPath);
                        $array = json_decode($strJsonFileContents, true);
                        // $migrations = $array[$name]['migration'];
                        // $names = $array[$name]['names'];


                        $version = $array[$name]['versions'][0];
                        $url = $array[$name]['url'][0];
                        $notes = $array[$name]['notes'][0];

                        DB::beginTransaction();
                        $s = InfixModuleManager::where('name', $name)->first();
                        if (empty($s)) {
                            $s = new InfixModuleManager();
                        }
                        $s->name = $name;
                        $s->email = $email;
                        $s->notes = $notes;
                        $s->version = $version;
                        $s->update_url = $url;
                        $s->installed_domain = url('/');
                        $s->activated_date = date('Y-m-d');
                        $s->purchase_code = $request->purchase_code;
                        $r = $s->save();

                        $config = SmGeneralSettings::first();
                        $config->$name = 1;
                        $r = $config->save();

                        DB::commit();
                        Toastr::success('Verification successful', 'Success');
                        return redirect()->back();
                    } catch (\Exception $e) {
                        DB::rollback();
                        $config = SmGeneralSettings::first();
                        $config->$name = 0;
                        $config->save();
                        $ModuleManage = Module::find($name)->disable();
                        Toastr::error($e->getMessage(), 'Failed');
                        return redirect()->back();
                    }

                    Toastr::success('Verification successful', 'Success');
                    return redirect()->back();
                } else {
                    $config = SmGeneralSettings::first();
                    $config->$name = 0;
                    $r = $config->save();
                    $ModuleManage = Module::find($name)->disable();
                    Toastr::error('Ops! Purchase code is not valid.', 'Failed');
                    return redirect()->back();
                }
            } catch (\Exception $e) {
                return redirect()->back()->with("message-danger", $e->getMessage());
            }
        } else {
            $email = $request->envatouser;
            $UserData = Envato::verifyPurchase($request->purchase_code);


            if (!empty($UserData['verify-purchase']['item_id'])) {

                // added a new column in sm general settings
                if (!Schema::hasColumn('sm_general_settings', $name)) {
                    Schema::table('sm_general_settings', function ($table) use ($name) {
                        $table->integer($name)->default(1)->nullable();
                    });
                }

                try {
                    $dataPath = 'Modules/' . $name . '/' . $name . '.json';        // // Get the contents of the JSON file
                    $strJsonFileContents = file_get_contents($dataPath);
                    $array = json_decode($strJsonFileContents, true);


                    $version = $array[$name]['versions'][0];
                    $url = $array[$name]['url'][0];
                    $notes = $array[$name]['notes'][0];

                    DB::beginTransaction();
                    $s = InfixModuleManager::where('name', $name)->first();
                    if (empty($s)) {
                        $s = new InfixModuleManager();
                    }
                    $s->name = $name;
                    $s->email = $email;
                    $s->notes = $notes;
                    $s->version = $version;
                    $s->update_url = $url;
                    $s->installed_domain = url('/');
                    $s->activated_date = date('Y-m-d');
                    $s->purchase_code = $request->purchase_code;
                    $r = $s->save();

                    $config = SmGeneralSettings::first();
                    $config->$name = 1;
                    $r = $config->save();

                    // session()->forget('all_module');
                    // $all_module = [];
                    // $modules = InfixModuleManager::select('name')->get();
                    //  foreach ($modules as $module) {
                    // $all_modules[] = $module->name;
                    // }
                    // session()->put('all_module', $all_modules);

                    DB::commit();
                    Toastr::success('Verification successful', 'Success');
                    return redirect()->back();
                } catch (\Exception $e) {
                    DB::rollback();
                    $config = SmGeneralSettings::first();
                    $config->$name = 0;
                    $config->save();
                    $ModuleManage = Module::find($name)->disable();
                    Toastr::error($e->getMessage(), 'Failed');
                    return redirect()->back();
                }
            } else {
                $config = SmGeneralSettings::first();
                $config->$name = 0;
                $r = $config->save();
                $ModuleManage = Module::find($name)->disable();
                Toastr::error('Ops! Purchase code is not valid.', 'Failed');
                return redirect()->back();
            }
        }



        $config = SmGeneralSettings::first();
        $config->$name = 0;
        $r = $config->save();
        $ModuleManage = Module::find($name)->disable();
        Toastr::error('Ops! Something went wrong !.', 'Failed');
        return redirect()->back();
    }


    public function FreemoduleAddOnsEnable($name)
    {


        try {

            $module_tables = [];
            $module_tables_names = [];
            $dataPath = 'Modules/' . $name . '/' . $name . '.json';        // // Get the contents of the JSON file
            $strJsonFileContents = file_get_contents($dataPath);
            $array = json_decode($strJsonFileContents, true);
            $migrations = $array[$name]['migration'] ?? '';
            $names = $array[$name]['names'];


            $version = $array[$name]['versions'][0] ?? '';
            $url = $array[$name]['url'][0] ?? '';
            $notes = $array[$name]['notes'][0] ?? '';


            DB::beginTransaction();
            $s = InfixModuleManager::where('name', $name)->first();
            if (empty($s)) {
                $s = new InfixModuleManager();
            }
            $s->name = $name;
            $s->notes = $notes;
            $s->version = $version;
            $s->update_url = $url;
            $s->installed_domain = url('/');
            $s->activated_date = date('Y-m-d');
            $s->save();
            DB::commit();


            if (!empty($migrations) && count($migrations) != 0) {
                foreach ($migrations as $value) {
                    $module_tables[] = 'Modules/' . $name . '/Database/Migrations/' . $value;
                }
            }

            foreach ($names as $value) {
                $module_tables_names[] = $value;
            }

            $is_module_available = 'Modules/' . $name . '/Providers/' . $name . 'ServiceProvider.php';

            if (file_exists($is_module_available)) {
                try {

                    if (!empty($module_tables)) {
                        foreach ($module_tables as $table) {
                            $path = $table;
                            if (file_exists($path)) {
                                try {
                                    Artisan::call('migrate',
                                        array(
                                            '--path' => $path,
                                            '--force' => true));


                                } catch (\Exception $e) {
                                    Log::info($e->getMessage());

                                }
                            }
                        }
                    }


                    $moduleCheck = \Nwidart\Modules\Facades\Module::find($name);
                    $moduleCheck->enable();


                    $ModuleManage = Module::where('name', $name)->first();


                } catch (\Exception $e) {
                    Log::info($e->getMessage());

                }
            } else {
                Log::info('module not found');
                DB::rollback();
            }


        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollback();
        }

    }

}