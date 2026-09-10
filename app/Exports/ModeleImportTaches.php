<?php

namespace App\Exports;

use App\Imports\TaskImport;
use App\Models\TaskboardColumn;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Modele Excel d'import des taches.
 *
 * Genere a la demande plutot que stocke comme fichier : les statuts,
 * les categories et les projets proposes en exemple suivent ainsi la
 * configuration reelle du cabinet, sans modele a maintenir a la main.
 */
class ModeleImportTaches implements FromArray, WithHeadings, WithStyles
{

    public function headings(): array
    {
        return TaskImport::entetes();
    }

    public function array(): array
    {
        $societe = company()->id;

        /* Un exemple bati sur des valeurs qui existent vraiment : une
           ligne d'exemple inventee serait refusee a l'import et
           donnerait le sentiment d'un outil defaillant. */
        $projet = DB::table('projects')->whereNull('deleted_at')
            ->where('company_id', $societe)->value('project_name');

        $categorie = DB::table('task_category')
            ->where('company_id', $societe)->value('category_name');

        $collaborateur = DB::table('users')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('roles.name', 'employee')->where('users.status', 'active')
            ->where('users.company_id', $societe)
            ->value('users.name');

        $format = company()->date_format;

        return [
            [
                'Exemple : préparer la déclaration TVA',
                $projet ?: '',
                now()->format($format),
                now()->addDays(7)->format($format),
                TaskboardColumn::where('slug', 'to_do')->value('column_name') ?: 'À faire',
                $collaborateur ?: '',
                $categorie ?: '',
                'Ligne d’exemple : supprimez-la avant d’importer.',
            ],
        ];
    }

    public function styles(Worksheet $feuille)
    {
        /* La ligne d'exemple est grisee, pour qu'on comprenne qu'elle
           n'a pas vocation a rester. */
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '9CA3AF']]],
        ];
    }

}
