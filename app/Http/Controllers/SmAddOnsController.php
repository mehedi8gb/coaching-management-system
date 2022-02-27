<?php

namespace App\Http\Controllers;

use Throwable;
use App\Envato\Envato;
use App\SmGeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class SmAddOnsController extends Controller
{
    protected $systemConfigModule = "FeesCollection";
    public function setActive($active)
    {
        return $this->json()->set('active', $active)->save();
    }

    // ManageAddOns by rashed


    public function ModuleRefresh()
    {
        try {
            exec('php composer.phar dump-autoload');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Toastr::success('Refresh successful', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::error($th->getMessage(), 'Failed');
            return redirect('manage-adons');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Failed');
            return redirect('manage-adons');
        }
    }

    public function ManageAddOns()
    {




        // $module_tables['migration'] = [];
        // $module_tables['names'] = [];
        // $p = json_encode($module_tables);
        // return $p;

        try {
            $is_module_available = Module::all();
            if (empty(@$is_module_available)) {
                $module_list = [
                    [
                        'name' => 'FeesCollection',
                        'url' => 'https://infixedu.com/package'
                    ],
                    [
                        'name' => 'InfixBiometrics',
                        'url' => 'https://infixedu.com/package'
                    ],
                ];
            } else {
                $module_list = [];
            }
            return view('backEnd.systemSettings.ManageAddOns', compact('is_module_available', 'module_list'));
        } catch (\Throwable $th) {
            Toastr::error($th->getMessage(), 'Failed');
            return redirect('manage-adons');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), 'Failed');
            return redirect('manage-adons');
        }
    }



    public function moduleAddOnsEnable($name)
    {

        $dataPath = 'Modules/' . $name . '/' . $name . '.json';        // // Get the contents of the JSON file 
        $strJsonFileContents = file_get_contents($dataPath);
        $array = json_decode($strJsonFileContents, true);
        $migrations = $array[$name]['migration'];
        $names = $array[$name]['names'];
        foreach ($migrations  as $key => $value) {
            $module_tables[] = 'Modules/' . $name . '/Database/Migrations/' . $value;
        }
        foreach ($names  as $key => $value) {
            $module_tables_names[] = $value;
        }

        $is_module_available = 'Modules/' . $name . '/Providers/' . $name . 'ServiceProvider.php';
        if (file_exists($is_module_available)) {
            try {
                $modulestatus =  Module::find($name)->isDisabled();
                if ($modulestatus) {
                    $ModuleManage = Module::find($name)->enable();
                    if (!empty($module_tables)) {
                        foreach ($module_tables as $table) {
                            $path = $table;
                            if (file_exists($path)) {
                                try {
                                    $command = 'migrate:refresh --path=' . $path;
                                    Log::info($command);
                                    Artisan::call($command);
                                } catch (Throwable $th) {
                                    Toastr::error($th->getMessage(), 'Failed');
                                    return response()->json(['error' => 'Operation Failed! Module file missing']);
                                    return redirect()->back()->with(['error' => $th->getMessage()]);
                                } catch (\Exception $e) {
                                    return $e;
                                    Log::info($e->getMessage());
                                    return response()->json(['error' => 'Operation Failed! Module file missing']);
                                    return redirect()->back()->with(['error' => $e->getMessage()]);
                                }
                            } else {
                                $error = "Module File is missing, Please contact with administrator";
                                Log::info($error);
                                return response()->json(['error' => $error]);
                                return redirect()->back()->with(['error' => $error]);
                            }
                        }
                    }


                    $data['data'] = 'enable';
                } else {
                    $ModuleManage = Module::find($name)->disable();
                    if (!empty($module_tables_names)) {
                        foreach ($module_tables_names as $table) {
                            if (Schema::hasTable($table)) {
                                //remove module tables from database 
                                try {
                                    DB::beginTransaction();
                                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                    Schema::dropIfExists($table);
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollback();
                                    $data['error'] = $e->getMessage();
                                    return response()->json($data, 200);
                                }

                                //remove migration name from migrations database 
                                try {
                                    DB::beginTransaction();
                                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                    DB::table('migrations')->where('migration', 'LIKE', '%' . $table . '%')->delete();
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollback();
                                    Log::info($e->getMessage());
                                    $data['error'] = $e->getMessage();
                                    return response()->json($data, 200);
                                }
                            }
                        }
                    }
                    foreach ($module_tables_names as $table) {
                        if (Schema::hasTable($table)) {
                            //remove module tables from database 
                            try {
                                DB::beginTransaction();
                                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                Schema::dropIfExists($table);
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollback();
                                $data['error'] = $e->getMessage();
                                return response()->json($data, 200);
                            }

                            //remove migration name from migrations database 
                            try {
                                DB::beginTransaction();
                                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                                DB::table('migrations')->where('migration', 'LIKE', '%' . $table . '%')->delete();
                                DB::commit();
                            } catch (\Exception $e) {
                                DB::rollback();
                                Log::info($e->getMessage());
                                $data['error'] = $e->getMessage();
                                return response()->json($data, 200);
                            }
                        }
                    }
                    $data['data'] = 'disable';
                    $data['Module'] = $ModuleManage;
                }
                $data['success'] = 'Operation success!';
                return response()->json($data, 200);
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                $data['error'] = $e->getMessage();
                return response()->json($data, 200);
            }
        } else {
            $data['error'] = 'Operation Failed! Module file missing !';
            return response()->json($data, 200);
        }
    }


    public function ManageAddOnsValidation(Request $request)
    {
        if ($request->purchase_code == "") {
            Toastr::error('Purchase code is required', 'Failed');
            return redirect()->back();
        } else {
            try {
                $UserData = Envato::verifyPurchase($request->purchase_code);
                $name = $request->name;
                if (!empty($UserData['verify-purchase']['item_id'])) {
                    $config = SmGeneralSettings::find(1);
                    $config->$name = 1;
                    $r = $config->save();
                    $ModuleManage = Module::find($name)->disable();
                    if ($r) {
                        Toastr::success('Validation successful', 'Success');
                        return redirect()->back();
                    } else {
                        Toastr::error('Operation Failed', 'Failed');
                        return redirect()->back();
                    }
                } else {
                    Toastr::error('Validation Failed', 'Failed');
                    return redirect()->back();
                }
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                return response()->json(['error' => 'Operation Failed!']);
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        }
        Toastr::error('Validation Failed', 'Failed');
        return redirect()->back();
    }
}
