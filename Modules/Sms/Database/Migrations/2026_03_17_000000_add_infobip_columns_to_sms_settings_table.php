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
        Schema::table('sms_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('sms_settings', 'infobip_api_key')) {
                $table->string('infobip_api_key')->nullable();
            }
            if (!Schema::hasColumn('sms_settings', 'infobip_base_url')) {
                $table->string('infobip_base_url')->nullable();
            }
            if (!Schema::hasColumn('sms_settings', 'infobip_whatsapp_number')) {
                $table->string('infobip_whatsapp_number')->nullable();
            }
            if (!Schema::hasColumn('sms_settings', 'infobip_from_number')) {
                $table->string('infobip_from_number')->nullable();
            }
            if (!Schema::hasColumn('sms_settings', 'infobip_status')) {
                $table->boolean('infobip_status')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->dropColumn(['infobip_api_key', 'infobip_base_url', 'infobip_whatsapp_number', 'infobip_from_number', 'infobip_status']);
        });
    }
};
