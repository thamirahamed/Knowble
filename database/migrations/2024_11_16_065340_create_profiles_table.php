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
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade'); // Linking profiles to users table
            $table->string('school_of_study'); // Dropdown options for school of study
            $table->string('year'); // Year (e.g., Year 1–3, Sem 1–3, or on break)
            $table->string('cb_number'); // Extracted from email
            $table->string('profile_pic')->default('default.svg'); // user uploaded profile pic or default
            $table->json('available_times')->nullable(); // Available time and days
            $table->enum('role', ['Tutor', 'Student'])->default('Student'); // Tutor or Student
            $table->timestamps(); // Created at and Updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}

