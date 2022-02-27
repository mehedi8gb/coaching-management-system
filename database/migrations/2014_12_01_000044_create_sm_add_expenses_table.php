<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmAddExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_add_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable();
            $table->date('date')->nullable();
            $table->float('amount', 10, 2)->nullable();
            $table->string('file')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();


            $table->integer('expense_head_id')->nullable()->unsigned();
            $table->foreign('expense_head_id')->references('id')->on('sm_expense_heads')->onDelete('restrict');

            $table->integer('account_id')->nullable()->unsigned();
            $table->foreign('account_id')->references('id')->on('sm_bank_accounts')->onDelete('restrict');

            $table->integer('payment_method_id')->nullable()->unsigned();
            $table->foreign('payment_method_id')->references('id')->on('sm_payment_methhods')->onDelete('restrict');

            $table->integer('created_by')->nullable()->default(1)->unsigned();

            $table->integer('updated_by')->nullable()->default(1)->unsigned();

            $table->integer('school_id')->nullable()->default(1)->unsigned();
            $table->foreign('school_id')->references('id')->on('sm_schools')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sm_add_expenses');
    }
}
