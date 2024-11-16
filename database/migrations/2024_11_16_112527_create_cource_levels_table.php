<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cource_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cource_id');
            $table->string('level');
            $table->timestamps();

            $table->foreign('cource_id')->references('id')->on('cources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cource_levels');
    }
};
