<?php

use App\SmGeneralSettings;
use App\SmSchool;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Fees\Entities\FmFeesInvoiceSettings;

class CreateFmFeesInvoiceSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fm_fees_invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->text('invoice_positions')->nullable();
            $table->string('uniq_id_start')->nullable();
            $table->string('prefix')->nullable();
            $table->integer('class_limit')->nullable();
            $table->integer('section_limit')->nullable();
            $table->integer('admission_limit')->nullable();
            $table->string('weaver')->nullable();
            $table->integer('school_id')->nullable();
            $table->timestamps();
        });


        $schools = SmSchool::get();
        foreach ($schools as $school) {
            $store = new FmFeesInvoiceSettings();
            $store->invoice_positions = '[{"id":"prefix","text":"prefix"},{"id":"admission_no","text":"Admission No"},{"id":"class","text":"Class"},{"id":"section","text":"Section"}]';
            $store->uniq_id_start = "0011";
            $store->prefix = 'infixEdu';
            $store->class_limit = 3;
            $store->section_limit = 1;
            $store->admission_limit = 3;
            $store->weaver = 'amount';
            $store->school_id = $school->id;
            $store->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fm_fees_invoice_settings');
    }
}
