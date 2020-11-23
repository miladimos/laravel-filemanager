<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->foreignId('group_id');
            $table->id();
            $table->string('file_path');
            $table->string('file_title');
            $table->string('file_description');
            $table->string('file_ext');
            $table->string('file_size');
            $table->string('file_path');
            $table->timestamp('upload_time');
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
        Schema::dropIfExists('files');
    }
}
