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

            $table->integer('user_id');
            $table->integer('folder_id');
            $table->string('file_name');
            $table->string('file_extension');
            $table->bigInteger('file_size');
            $table->string('file_hash');

            $table->string('uuid')->uniuqe();
            $table->string('title')->nullable();
            $table->string('file_field')->nullable();
            $table->string('file_name')->nullable();
            $table->enum('file_type', ['image', 'document','video', 'audio'])->nullable();
            $table->string('mime_type')->nullable();
            $table->double('size', 15, 2)->nullable();
            $table->boolean('private')->default(false);
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
