<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('amount');
            $table->unsignedInteger('debit_user_id');
            $table->unsignedInteger('debit_user_balance');
            $table->unsignedInteger('credit_user_id');
            $table->unsignedInteger('credit_user_balance');
            $table->timestamps();

            $table->foreign('debit_user_id')->references('id')->on('users')->onDelete('RESTRICT');
            $table->foreign('credit_user_id')->references('id')->on('users')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
