<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilemanagerTables extends Migration
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

        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->uniuqe();
            $table->string("fileable_type")->nullable();
            $table->unsignedBigInteger("fileable_id")->nullable();
            $table->foreignId('group_id')->nullable();
            $table->unsignedBigInteger('directory_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('file_original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_description')->nullable();
            $table->string('file_extension')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
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


        Schema::create('file_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->uniuqe();
            $table->string('title')->unique();
            $table->string('description')->unique()->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });


        Schema::create('file_group_pivot', function (Blueprint $table) {
            $table->id();
            $table->morphs('groupable');
            $table->foreignId('group_id');
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

        Schema::dropIfExists('files');

        Schema::dropIfExists('file_groups');

        Schema::dropIfExists('file_group_pivot');

    }
}
