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
        Schema::create('tutor_sessions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID column
            $table->foreignId('tutor_id')->constrained()->onDelete('cascade'); // Foreign key to the tutors table
            $table->date('session_date'); // The date of the session
            $table->time('start_time'); // The start time of the session
            $table->time('end_time'); // The end time of the session
            $table->enum('status', ['pending', 'booked', 'completed', 'cancelled', 'unbooked', 'cancelRequest', 'alt'])->default('pending'); // Status of the session
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Foreign key to the user table, nullable if not booked
            $table->foreignId('peer_group_id')->nullable()->constrained()->onDelete('set null'); // Foreign key to the peer group table
            $table->foreignId('module_id')->nullable()->constrained()->onDelete('cascade'); // Foreign key to the modules table
            $table->text('notes')->nullable(); // Notes related to the session
            $table->string('meeting_url')->nullable(); // URL for the meeting (e.g., Zoom link)
            $table->string('alt_session_id')->nullable(); // alt session in case of cancellation
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutor_sessions');
    }
};
