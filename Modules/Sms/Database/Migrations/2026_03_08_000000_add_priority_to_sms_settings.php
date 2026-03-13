<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('sms_settings')) {
            Schema::table('sms_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('sms_settings', 'notification_priority')) {
                    $table->string('notification_priority')->default('both')->after('whatsapp_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('sms_settings')) {
            Schema::table('sms_settings', function (Blueprint $table) {
                if (Schema::hasColumn('sms_settings', 'notification_priority')) {
                    $table->dropColumn('notification_priority');
                }
            });
        }
    }
};
