<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feedback_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who gives feedback
            $table->foreignId('tutor_id')->constrained()->onDelete('cascade'); // Tutor receiving feedback
            $table->unsignedTinyInteger('rating'); // Rating as an integer (e.g., 1-5)
            $table->string('feedback')->nullable(); // Feedback text
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_ratings');
    }
};
