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
        Schema::create('peer_group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peer_group_id');
            $table->unsignedBigInteger('user_id'); // Student who joins the group
            $table->timestamps();
        
            $table->foreign('peer_group_id')->references('id')->on('peer_groups')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['peer_group_id', 'user_id']); // Prevent duplicate memberships
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peer_groups_members');
    }
};
