<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionDebitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_debits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('transaction_amounts_id')->references('id')->on('transaction_types')->onDelete('CASCADE');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('user_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_debits');
    }
}
