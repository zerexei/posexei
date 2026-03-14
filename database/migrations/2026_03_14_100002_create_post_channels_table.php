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
        Schema::create('post_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('social_channel_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, publishing, success, failed
            $table->string('external_post_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['post_id', 'social_channel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_channels');
    }
};
