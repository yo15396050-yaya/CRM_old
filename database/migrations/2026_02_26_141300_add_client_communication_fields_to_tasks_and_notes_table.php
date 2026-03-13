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
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'responsible_id')) {
                $table->unsignedInteger('responsible_id')->nullable()->after('added_by')->index();
            // $table->foreign('responsible_id')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
            }
            if (!Schema::hasColumn('tasks', 'source')) {
                $table->enum('source', ['internal', 'client_submitted'])->default('internal')->after('responsible_id');
            }
        });

        Schema::table('task_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('task_notes', 'is_client_visible')) {
                $table->boolean('is_client_visible')->default(false)->after('note');
            }
            if (!Schema::hasColumn('task_notes', 'deliverables')) {
                $table->text('deliverables')->nullable()->after('is_client_visible');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            try {
                $table->dropForeign(['responsible_id']);
            }
            catch (\Exception $e) {
            // Already dropped or never created
            }
            $table->dropColumn(['responsible_id', 'source']);
        });

        Schema::table('task_notes', function (Blueprint $table) {
            $table->dropColumn(['is_client_visible', 'deliverables']);
        });
    }
};
