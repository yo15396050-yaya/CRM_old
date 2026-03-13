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

        foreach ($tablesList as $tableObj) {
            $table = $tableObj->$prop;

            // On vérifie si la table a une colonne 'id'
            $hasId = Schema::hasColumn($table, 'id');

            if ($hasId) {
                // Diagnostic: Enregistrer la structure actuelle pour debug si besoin
                try {
                    $res = DB::select("SHOW CREATE TABLE `$table`")[0];
                    $createProp = "Create Table";
                    file_put_contents(base_path('schema_fix_log.txt'), "--- $table ---\n" . $res->$createProp . "\n\n", FILE_APPEND);
                }
                catch (\Exception $e) {
                }

                // 1. Nettoyage des IDs 0 qui bloquent l'auto-increment
                DB::table($table)->where('id', 0)->delete();

                // 2. Vérification s'il manque l'auto_increment
                $columns = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = 'id' AND Extra LIKE '%auto_increment%'");

                if (empty($columns)) {
                    try {
                        // On tente de forcer l'auto-increment. 
                        // On utilise INT ou BIGINT selon ce qui est le plus sûr, mais MODIFY id INT UNSIGNED AUTO_INCREMENT est généralement le défaut dans ce CRM
                        DB::statement("ALTER TABLE `$table` MODIFY `id` INT UNSIGNED AUTO_INCREMENT");
                    }
                    catch (\Exception $e) {
                        try {
                            // Si INT échoue (peut-être déjà BIGINT), on tente BIGINT
                            DB::statement("ALTER TABLE `$table` MODIFY `id` BIGINT UNSIGNED AUTO_INCREMENT");
                        }
                        catch (\Exception $ex) {
                            file_put_contents(base_path('schema_fix_errors.txt'), "Error on $table: " . $ex->getMessage() . "\n", FILE_APPEND);
                        }
                    }
                }
            }
        }

        // Nettoyage final des clés étrangères corrompues pour les clients
        if (Schema::hasTable('client_details')) {
            DB::table('client_details')->where('user_id', 0)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    // No easy way to remove auto-increment safely once data is moved
    }
};
