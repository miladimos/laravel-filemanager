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
            $table->id();
            $table->uuid('uuid')->uniuqe();
            $table->foreignId('group_id')->nullable();
            $table->unsignedBigInteger('directory_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('file_original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_description')->nullable();
            $table->string('file_extension')->nullable();
            $table->string('file_size')->nullable();
            $table->char('file_type')->nullable();
            $table->string('mime_type')->nullable();
            $table->char('status')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('disk')->nullable();
            $table->boolean('is_private')->default(false);
            $table->unsignedInteger('priority_column')->nullable();
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
