<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmPaymentGatewaySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_payment_gateway_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gateway_name')->nullable();
            $table->string('gateway_username')->nullable();
            $table->string('gateway_password')->nullable();
            $table->string('gateway_signature')->nullable();
            $table->string('gateway_client_id')->nullable();
            $table->string('gateway_mode')->nullable();
            $table->string('gateway_secret_key')->nullable();
            $table->string('gateway_secret_word')->nullable();
            $table->string('gateway_publisher_key')->nullable();
            $table->string('gateway_private_key')->nullable();
            $table->tinyInteger('active_status')->default(0);
            $table->timestamps();

            $table->integer('created_by')->nullable()->default(1)->unsigned();
            $table->integer('updated_by')->nullable()->default(1)->unsigned();

            $table->integer('school_id')->nullable()->default(1)->unsigned();
            $table->foreign('school_id')->references('id')->on('sm_schools')->onDelete('restrict');
        });




        DB::table('sm_payment_gateway_settings')->insert([
            [
                'gateway_name'          => 'PayPal',
                'gateway_username'      => 'demo@paypal.com',
                'gateway_password'      => '12334589',
                'gateway_client_id'     => 'AVZdghanegaOjiL6DPXd0XwjMGEQ2aXc58z1-qNwv_Wz9mI_6MKSW5dS9uPAha3rd7eB82ToOCQLp31c',
                'gateway_secret_key'    => 'EMgxBzeJ9By7D0xvkSUblDd_GW99WvK0DDNyvkGn7rBikvjPw46xz9Plozp4jl7AOsx-isWmBFnw1h2j',
                'created_at' => date('Y-m-d h:i:s'),

            ]
        ]);

        DB::table('sm_payment_gateway_settings')->insert([
            [
                'gateway_name'          => 'Stripe',
                'gateway_username'      => 'demo@strip.com',
                'gateway_password'      => '12334589',
                'gateway_client_id'     => '',
                'gateway_secret_key'    => 'AVZdghanegaOjiL6DPXd0XwjMGEQ2aXc58z1-isWmBFnw1h2j',
                'gateway_secret_word'   => 'AVZdghanegaOjiL6DPXd0XwjMGEQ2aXc58z1',
                'created_at' => date('Y-m-d h:i:s'),

            ]

        ]);


        DB::table('sm_payment_gateway_settings')->insert([
            [
                'gateway_name'          => 'Paystack',
                'gateway_username'      => 'demo@gmail.com',
                'gateway_password'      => '12334589',
                'gateway_client_id'     => '',
                'gateway_secret_key'    => 'sk_live_2679322872013c265e161bc8ea11efc1e822bce1',
                'gateway_publisher_key' => 'pk_live_e5738ce9aade963387204f1f19bee599176e7a71',
                'created_at' => date('Y-m-d h:i:s'),

            ], 
        ]);
 
        DB::table('sm_payment_gateway_settings')->insert([
            [
                'gateway_name'              => 'Razorpay',
                'gateway_publisher_key'     => 'rzp_test_qhPXN4zgflSLgA',
                'gateway_secret_key'        => 'nUhyAjRJU5SAoNiZPLfosA9Q',
                'created_at' => date('Y-m-d h:i:s'),

            ], 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_payment_gateway_settings');
    }
}
