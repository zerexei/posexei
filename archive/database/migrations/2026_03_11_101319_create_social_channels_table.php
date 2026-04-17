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
        Schema::create('social_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channel_type')->comment('profile, page, company, channel, group');
            $table->string('external_id')->comment('platform ID');
            $table->string('name');
            $table->json('metadata_json')->nullable();
            $table->timestamps();

            $table->unique(['external_id', 'channel_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_channels');
    }
};
