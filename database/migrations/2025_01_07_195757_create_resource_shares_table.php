<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resource_shares', function (Blueprint $table) {
            $table->id();
            $table->text('fileLocation');
            $table->foreignId('tutor_id');
            $table->string('fileName');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_shares');
    }
};
