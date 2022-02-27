<?php

use App\InfixModuleManager;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeadToInfixModuleManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $name ="Lead";
        if (!Schema::hasColumn('sm_general_settings', $name)) {
            Schema::table('sm_general_settings', function ($table) use ($name) {
                $table->integer($name)->default(0)->nullable();
            });
        }


        $exist = InfixModuleManager::where('name',$name)->first();

        if(! $exist){
            $s = new InfixModuleManager();
            $s->name = $name;
            $s->email = 'support@spondonit.com';
            $s->notes = "Lead module is your lead management for infix edu. It will help to covert your lead to student.";
            $s->version = 1.0;
            $s->update_url = "support@spondonit.com";
            $s->is_default = 0;
            $s->purchase_code = null;
            $s->installed_domain = null;
            $s->activated_date = null;
            $s->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $name ="Lead";
        if (Schema::hasColumn('sm_general_settings', $name)) {
            Schema::table('sm_general_settings', function ($table) use ($name) {
                $table->dropColumn($name);
            });
        }

        $exist = InfixModuleManager::where('name',$name)->first();
        if ($exist){
            $exist->delete();
        }
    }
}
