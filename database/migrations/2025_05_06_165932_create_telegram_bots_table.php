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
        Schema::create('telegram_bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('bot_username');
            $table->text('token');
            $table->string('bot_id')->unique(); // id bot telegram
            $table->boolean('can_join_groups')->default(false);
            $table->boolean('can_read_all_group_messages')->default(false);
            $table->boolean('supports_inline_queries')->default(false);
            $table->boolean('can_connect_to_business')->default(false);
            $table->boolean('has_main_web_app')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_bots');
    }
};
