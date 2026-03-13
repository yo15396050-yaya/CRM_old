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
            if (!Schema::hasColumn('task_notes', 'status')) {
                $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('approved')->after('recipient_name');
            }
            if (!Schema::hasColumn('task_notes', 'notification_channel')) {
                // To store selected channel by user: 'all', 'email', 'whatsapp', 'sms'
                $table->string('notification_channel')->nullable()->after('status');
            }
            if (!Schema::hasColumn('task_notes', 'feedback_status')) {
                $table->string('feedback_status')->nullable()->after('notification_channel');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_notes', function (Blueprint $table) {
            $table->dropColumn(['status', 'notification_channel', 'feedback_status']);
        });
    }
};
