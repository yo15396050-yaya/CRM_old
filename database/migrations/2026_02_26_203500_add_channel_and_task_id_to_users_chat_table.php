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
        Schema::table('users_chat', function (Blueprint $table) {
            if (!Schema::hasColumn('users_chat', 'channel')) {
                $table->string('channel')->nullable()->after('message_seen');
            }
            if (!Schema::hasColumn('users_chat', 'task_id')) {
                $table->unsignedBigInteger('task_id')->nullable()->after('channel');
                $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade')->onUpdate('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_chat', function (Blueprint $table) {
            if (Schema::hasColumn('users_chat', 'task_id')) {
                $table->dropForeign(['task_id']);
                $table->dropColumn(['task_id']);
            }
            if (Schema::hasColumn('users_chat', 'channel')) {
                $table->dropColumn(['channel']);
            }
        });
    }
};
