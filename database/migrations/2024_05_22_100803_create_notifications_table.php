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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained(
                table: 'users', indexName: 'notifications_owner_id'
            )->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->foreignId('emitter_id')->constrained(
                table: 'users', indexName: 'notifications_emitter_id'
            )->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->enum('type', ['post_comment', 'post_like', 'post_creation', 'your_post_shared', 'friend_request', 'friend_request_accept']);
            $table->string('content', 300);
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
