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
        Schema::create('channel_has_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_channel_id');
            $table->foreignId('telegram_message_id');
            $table->foreign('telegram_channel_id')->references('id')->on('telegram_channels')->onDelete('cascade');
            $table->foreign('telegram_message_id')->references('id')->on('telegram_messages')->onDelete('cascade');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_has_messages');
    }
};
