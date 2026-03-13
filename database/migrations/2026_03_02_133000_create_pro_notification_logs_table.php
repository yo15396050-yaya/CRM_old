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
        if (!Schema::hasTable('pro_notification_logs')) {
            Schema::create('pro_notification_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('company_id')->nullable()->index();
                $table->unsignedInteger('task_id')->nullable()->index();
                $table->unsignedInteger('user_id')->nullable()->index(); // Destinataire
                $table->string('type'); // type_1 (init) or type_2 (process)
                $table->string('channel'); // email, whatsapp, sms
                $table->string('to'); // email or phone number
                $table->enum('status', ['sent', 'delivered', 'failed', 'fallback_triggered'])->default('sent');
                $table->text('content_summary')->nullable();
                $table->text('error_details')->nullable();
                $table->timestamp('sent_at')->useCurrent();
                $table->timestamps();

                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null')->onUpdate('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_notification_logs');
    }
};
