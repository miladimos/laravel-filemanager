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
            $table->enum('file_type', \Miladimos\FileManager\Enums\FileTypeEnum::getConstants())->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->morphs('model');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
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
