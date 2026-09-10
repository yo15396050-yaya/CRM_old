<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Echeancier reglementaire du tableau de bord.
 *
 * La maquette pilotage-dc-knowing affiche une carte « Echeancier
 * reglementaire — 15 prochains jours » listant les obligations fiscales
 * et sociales a venir. Aucune table du projet ne portait cette notion :
 * les diligences ont une echeance propre, mais le calendrier fiscal est
 * un referentiel distinct, identique pour tous les clients d'un cabinet.
 *
 * Les echeances sont stockees sous forme de regles recurrentes plutot
 * que de dates : une ligne « TVA, le 15 de chaque mois » vaut pour
 * toutes les annees, sans reprise annuelle.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('regulatory_deadlines')) {
            return;
        }

        Schema::create('regulatory_deadlines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable()->index();

            $table->string('label');                       // « Declaration TVA — juillet »
            $table->string('detail')->nullable();          // « 9 clients concernes »

            $table->unsignedTinyInteger('day_of_month');   // 1 a 31
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly'])->default('monthly');

            // Mois concernes pour quarterly / yearly : « 1,4,7,10 ». Null = tous.
            $table->string('months')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
        });

        $this->seedCalendrierIvoirien();
    }

    /**
     * Calendrier fiscal et social ivoirien de base, applicable a toutes
     * les entreprises du CRM (company_id null). Les cabinets peuvent
     * ensuite ajouter leurs propres echeances rattachees a une entreprise.
     */
    private function seedCalendrierIvoirien(): void
    {
        $maintenant = now();

        $echeances = [
            ['Déclaration TVA',                 'Régime du réel normal',              15, 'monthly',   null,          10],
            ['ITS et retenues à la source',     'Impôt sur traitements et salaires',  15, 'monthly',   null,          20],
            ['Cotisations CNPS',                'Régime général',                     15, 'monthly',   null,          30],
            ['Acompte impôt sur les bénéfices', 'Versement trimestriel',              20, 'quarterly', '3,6,9,12',    40],
            ['Patente',                         'Première échéance annuelle',         15, 'yearly',    '3',           50],
            ['États financiers annuels',        'Dépôt auprès de la DGI',             30, 'yearly',    '6',           60],
        ];

        $lignes = [];

        foreach ($echeances as [$label, $detail, $jour, $frequence, $mois, $ordre]) {
            $lignes[] = [
                'company_id'   => null,
                'label'        => $label,
                'detail'       => $detail,
                'day_of_month' => $jour,
                'frequency'    => $frequence,
                'months'       => $mois,
                'is_active'    => true,
                'sort_order'   => $ordre,
                'created_at'   => $maintenant,
                'updated_at'   => $maintenant,
            ];
        }

        DB::table('regulatory_deadlines')->insert($lignes);
    }

    public function down(): void
    {
        Schema::dropIfExists('regulatory_deadlines');
    }
};
