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
        Schema::table('tutor_sessions', function (Blueprint $table) {
            $table->date('date')->nullable();
            $table->time('startTime')->nullable();
            $table->time('endTime')->nullable();
            $table->string('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutor_session', function (Blueprint $table) {
            //
        });
    }
};
