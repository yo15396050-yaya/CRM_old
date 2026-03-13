<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $dbName = DB::getDatabaseName();
        $tablesList = DB::select('SHOW TABLES');
        $prop = "Tables_in_" . $dbName;

        // Désactiver les contraintes pour permettre la modification des IDs référencés
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tablesList as $tableObj) {
            $table = $tableObj->$prop;

            if (Schema::hasColumn($table, 'id')) {

                // 1. Nettoyage des IDs 0
                DB::table($table)->where('id', 0)->delete();

                // 2. Fix Auto-Increment
                $columns = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = 'id' AND Extra LIKE '%auto_increment%'");

                if (empty($columns)) {
                    try {
                        // On tente d'abord avec le type standard INT 
                        DB::statement("ALTER TABLE `$table` MODIFY `id` INT UNSIGNED AUTO_INCREMENT");
                    }
                    catch (\Exception $e) {
                        try {
                            // Si échec, on tente BIGINT
                            DB::statement("ALTER TABLE `$table` MODIFY `id` BIGINT UNSIGNED AUTO_INCREMENT");
                        }
                        catch (\Exception $ex) {
                        // On log mais on ne bloque pas
                        // Log::error("Impossible de corriger l'auto-increment pour la table $table : " . $ex->getMessage());
                        }
                    }
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
