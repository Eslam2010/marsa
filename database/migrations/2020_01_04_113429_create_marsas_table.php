<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarsasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marsas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('numberof');
            $table->string('numbervat');
            $table->string('location');
            $table->string('space');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marsas');
    }
}
