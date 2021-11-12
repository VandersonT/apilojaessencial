<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloth', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->string('description');
            $table->float('price', 20, 2);
            $table->string('type');
            $table->string('size');
            $table->integer('amount');
            $table->text('info');
            $table->string('cover');
            $table->string('age');
            $table->string('sex');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cloth');
    }
}
