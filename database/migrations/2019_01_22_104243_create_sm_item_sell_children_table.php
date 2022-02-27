<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmItemSellChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sm_item_sell_children', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sell_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('sub_total')->nullable();
            $table->string('description')->length('500')->nullable();
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();

            $table->integer('item_sell_id')->nullable()->unsigned()->index('sm_item_sell_children_1');
            $table->foreign('item_sell_id', 'sm_item_sell_children_1')->references('id')->on('sm_item_sells')->onDelete('restrict');

            $table->integer('item_id')->nullable()->unsigned()->index('sm_item_sell_children_2');
            $table->foreign('item_id', 'sm_item_sell_children_2')->references('id')->on('sm_items')->onDelete('restrict');

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
        Schema::dropIfExists('sm_item_sell_children');
    }
}
