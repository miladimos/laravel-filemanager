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
            $table->string('uuid')->uniuqe();
            $table->foreignId('group_id');
            $table->integer('directory_id');
            $table->integer('user_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_description');
            $table->string('file_extension');
            $table->string('file_size');
            $table->timestamp('upload_time');
            $table->string('file_hash');
            $table->enum('file_type', ['image', 'document','video', 'audio'])->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('is_private')->default(false);
            $table->boolean('has_reference')->default(false);
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
