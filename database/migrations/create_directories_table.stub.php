<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDirectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->uniuqe();
            $table->foreignID('parent_id')->nullable();
//            $table->foreignID('group_id')->nullable();
            $table->foreignID('user_id')->nullable();
            $table->string('title')->unique();
            $table->string('color_hex')->nullable();
            $table->string('path')->nullable();
            $table->string('description')->nullable();
            $table->char('status')->default('a');
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
        Schema::dropIfExists('directories');
    }
}

