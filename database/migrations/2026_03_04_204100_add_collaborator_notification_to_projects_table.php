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
        Schema::table('projects', function (Blueprint $blueprint) {
            if (!Schema::hasColumn('projects', 'allow_collaborator_notification')) {
                $blueprint->enum('allow_collaborator_notification', ['enable', 'disable'])->default('disable')->after('allow_client_notification');
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
        Schema::table('projects', function (Blueprint $blueprint) {
            $blueprint->dropColumn('allow_collaborator_notification');
        });
    }
};
