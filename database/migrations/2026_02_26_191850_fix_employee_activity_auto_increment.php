<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixEmployeeActivityAutoIncrement extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('employee_activity')) {
            // Supprimer le doublon potentiel ID 0 pour permettre l'auto-incrément
            DB::table('employee_activity')->where('id', 0)->delete();

            Schema::table('employee_activity', function (Blueprint $table) {
                $table->bigIncrements('id')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    //
    }
}
;
