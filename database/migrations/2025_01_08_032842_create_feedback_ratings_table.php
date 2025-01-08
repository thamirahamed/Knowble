<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feedback_ratings', function (Blueprint $table) {
            $table->id();
            $table->decimal('rating');
            $table->string('feedback')->nullable();
            $table->foreignId('tutor_session_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_ratings');
    }
};
