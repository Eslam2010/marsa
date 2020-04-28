<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNecklasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('necklases', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->integer('marsaid')->unsigned();
            $table->integer('userid')->unsigned();
            $table->integer('customerid')->unsigned();
            $table->string('many');
            $table->string('add');
            $table->string('total');
            $table->date('from');
            $table->date('to');
            $table->string('batch');
            $table->integer('totalbatches');
            $table->integer('remainingamount')->default(0);
            $table->integer('remainingbatches')->default(0);


            $table->string('wastaname');
            $table->string('codewasta');
            $table->string('hieght');
            $table->string('width');
            $table->timestamps();
            $table->foreign('marsaid')->references('id')->on('marsas')->onDelete('cascade');
            $table->foreign('userid')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customerid')->references('id')->on('customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('necklases');
    }
}
