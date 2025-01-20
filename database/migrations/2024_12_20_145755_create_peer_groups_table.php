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
        Schema::create('peer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('leader'); // Foreign key to the users table
            $table->unsignedBigInteger('module_id'); // Foreign key to a modules table
            $table->enum('status', ['opened', 'closed'])->default('opened');
            $table->integer('total_members')->default(2); 
            $table->timestamps();
        
            $table->foreign('leader')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peer_groups');
    }
};
