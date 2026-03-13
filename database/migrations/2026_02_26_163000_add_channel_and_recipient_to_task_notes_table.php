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
        Schema::table('task_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('task_notes', 'channel')) {
                $table->string('channel')->nullable()->after('is_client_visible');
            }
            if (!Schema::hasColumn('task_notes', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('channel');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_notes', function (Blueprint $table) {
            $table->dropColumn(['channel', 'recipient_name']);
        });
    }
};
