<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id(); // Primary key and foreign key from users table
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('level_id');
            $table->string('cb_number'); // Extracted from email
            $table->text('profile_pic')->default('default.svg'); // user uploaded profile pic or default
            $table->timestamps(); // Created at and Updated at

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('cources')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('cource_levels')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}

