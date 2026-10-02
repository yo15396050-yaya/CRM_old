<?php
    /*
     * Gabarit PDF du rapport hebdomadaire.
     *
     * Vue distincte de l'ecran, et non une simple feuille d'impression :
     * dompdf ne connait ni flexbox ni CSS grid, sur lesquels repose toute
     * la mise en page de l'ecran. Ici tout passe par des tableaux, des
     * largeurs en pourcentage et des couleurs ecrites en clair.
     *
     * La police DejaVu Sans est livree avec dompdf : c'est la seule qui
     * garantisse le rendu des accents et des majuscules accentuees.
     */

    $k = $rapport->synthese;

    $libelles = [
        'completed' => 'Terminée', 'doing' => 'En cours', 'to_do' => 'À faire',
        'incomplete' => 'Incomplète', 'waiting_approval' => 'En attente',
    ];
    $teintes = [
        'completed' => 'ok', 'doing' => 'info', 'to_do' => 'neu',
        'incomplete' => 'warn', 'waiting_approval' => 'warn',
    ];
    $priorites = ['high' => 'Haute', 'medium' => 'Moyenne', 'low' => 'Basse'];
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title><?php echo e(__('Rapport hebdomadaire des tâches')); ?></title>
<style>
@page { margin: 14mm 12mm 16mm 12mm; }

body { font-family: DejaVu Sans, sans-serif; font-size: 8.5pt; color: #111827; margin: 0; }

.entete { border-bottom: 1.5pt solid #111827; padding-bottom: 7pt; margin-bottom: 11pt; }
.entete td { vertical-align: bottom; border: 0; }
.cabinet { font-size: 13pt; font-weight: bold; }
.sous-cabinet { font-size: 7.5pt; color: #6b7280; margin-top: 1pt; }
.titre-doc { font-size: 11pt; font-weight: bold; text-align: right; }
.periode { font-size: 8pt; color: #374151; text-align: right; margin-top: 2pt; }
.edite { font-size: 7pt; color: #9ca3af; text-align: right; margin-top: 1pt; }

.kpi { width: 100%; border-collapse: separate; border-spacing: 4pt 0; margin-bottom: 6pt; }
.kpi td { width: 25%; border: 0.6pt solid #e5e7eb; padding: 6pt 7pt; background: #fff; }
.kpi .lab { font-size: 6.8pt; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.3pt; }
.kpi .val { font-size: 15pt; font-weight: bold; padding-top: 1pt; }
.kpi .sub { font-size: 6.8pt; color: #9ca3af; }
.kpi td.ok { background: #f2f8e9; border-color: #cbe0a8; }
.kpi td.ok .val { color: #3f6212; }
.kpi td.err { background: #fdf1f0; border-color: #f2c9c6; }
.kpi td.err .val { color: #b42318; }

h2 { font-size: 9.5pt; margin: 0 0 5pt; padding-bottom: 3pt; border-bottom: 0.8pt solid #d1d5db; }
.bloc { margin-bottom: 12pt; }

table.data { width: 100%; border-collapse: collapse; }
table.data th {
  background: #f3f4f6; border-bottom: 0.8pt solid #d1d5db; padding: 4pt;
  font-size: 6.8pt; text-transform: uppercase; letter-spacing: 0.3pt;
  color: #4b5563; text-align: left;
}
table.data td { border-bottom: 0.4pt solid #e5e7eb; padding: 4pt; font-size: 7.6pt; vertical-align: top; }
table.data tr.alt td { background: #fafafa; }
.num { text-align: right; white-space: nowrap; }
.gris { color: #9ca3af; }
.tache-titre { font-weight: bold; }
.tache-sous { color: #6b7280; font-size: 6.8pt; }

.p { padding: 1pt 4pt; font-size: 6.6pt; font-weight: bold; border: 0.5pt solid; }
.p-ok { background: #f2f8e9; border-color: #cbe0a8; color: #3f6212; }
.p-info { background: #eff8ff; border-color: #b3ddf7; color: #0369a1; }
.p-warn { background: #fef9e7; border-color: #f3dfa2; color: #a16207; }
.p-err { background: #fdf1f0; border-color: #f2c9c6; color: #b42318; }
.p-neu { background: #f3f4f6; border-color: #e5e7eb; color: #374151; }

.note { background: #fef9e7; border: 0.6pt solid #f3dfa2; padding: 6pt 8pt;
        margin-bottom: 11pt; font-size: 7.4pt; }
.note b { display: block; margin-bottom: 1pt; }

.vide { padding: 26pt 0; text-align: center; color: #6b7280; font-size: 9pt; }

.pied { position: fixed; bottom: -10mm; left: 0; right: 0; font-size: 6.8pt;
        color: #9ca3af; border-top: 0.4pt solid #e5e7eb; padding-top: 3pt; }
.pied td { border: 0; }

/* L'en-tete se repete quand le tableau deborde, et aucune ligne
   n'est coupee entre deux pages. */
thead { display: table-header-group; }
tr { page-break-inside: avoid; }
</style>
</head>
<body>

<table class="entete" width="100%">
    <tr>
        <td width="55%">
            <?php if($logo): ?>
                
                <img src="<?php echo e($logo); ?>" alt="<?php echo e($societe->company_name); ?>"
                     style="max-height:34pt;max-width:170pt;margin-bottom:3pt">
            <?php else: ?>
                <div class="cabinet"><?php echo e($societe->company_name); ?></div>
            <?php endif; ?>
            <div class="sous-cabinet">
                <?php if($logo): ?><?php echo e($societe->company_name); ?> — <?php endif; ?>
                <?php echo e($societe->address ? \Illuminate\Support\Str::limit(strip_tags($societe->address), 58) : ''); ?>

            </div>
        </td>
        <td width="45%">
            <div class="titre-doc">
                <?php echo e($pourQui ? __('Mon rapport hebdomadaire') : __('Rapport hebdomadaire des tâches')); ?>

            </div>
            <div class="periode">
                Du <?php echo e($rapport->debut->locale('fr')->translatedFormat('j F Y')); ?>

                au <?php echo e($rapport->fin->locale('fr')->translatedFormat('j F Y')); ?>

            </div>
            <div class="edite">
                Édité le <?php echo e(now(company()->timezone)->locale('fr')->translatedFormat('j F Y à H\hi')); ?>

                — <?php echo e($pourQui ?: __('système')); ?>

            </div>
        </td>
    </tr>
</table>

<?php if($k->total === 0): ?>

    <div class="vide">
        <?php echo e(__("Aucune tâche n'a été réalisée, échue ou modifiée durant cette semaine.")); ?>

    </div>

<?php else: ?>

    <table class="kpi">
        <tr>
            <td>
                <div class="lab"><?php echo e($pourQui ? __('Mes tâches') : __('Tâches')); ?></div>
                <div class="val"><?php echo e($k->total); ?></div>
                <div class="sub"><?php echo e(__('dans le périmètre')); ?></div>
            </td>
            <td class="ok">
                <div class="lab"><?php echo e(__('Terminées')); ?></div>
                <div class="val"><?php echo e($k->termines); ?></div>
                <div class="sub"><?php echo e($k->total ? round($k->termines / $k->total * 100) : 0); ?> % <?php echo e(__('du total')); ?></div>
            </td>
            <td>
                <div class="lab"><?php echo e(__('À faire')); ?></div>
                <div class="val"><?php echo e($k->aFaire); ?></div>
                <div class="sub"><?php echo e(__('non démarrées')); ?></div>
            </td>
            <td class="<?php echo e($k->retards > 0 ? 'err' : ''); ?>">
                <div class="lab"><?php echo e(__('En retard')); ?></div>
                <div class="val"><?php echo e($k->retards); ?></div>
                <div class="sub"><?php echo e(__('échéance dépassée')); ?></div>
            </td>
        </tr>
    </table>

    <table class="kpi" style="margin-bottom:11pt">
        <tr>
            <td>
                <div class="lab"><?php echo e(__('En cours')); ?></div>
                <div class="val"><?php echo e($k->enCours); ?></div>
                <div class="sub"><?php echo e(__('en traitement')); ?></div>
            </td>
            <td>
                <div class="lab"><?php echo e(__('En attente')); ?></div>
                <div class="val"><?php echo e($k->attente); ?></div>
                <div class="sub"><?php echo e(__("d'approbation")); ?></div>
            </td>
            <td>
                <div class="lab"><?php echo e(__('Délai moyen')); ?></div>
                <div class="val"><?php echo e($k->delaiMoyen ?? '—'); ?></div>
                <div class="sub"><?php echo e(__('jours, sur')); ?> <?php echo e($k->delaisJuges); ?> <?php echo e(trans_choice('tâche|tâches', $k->delaisJuges)); ?></div>
            </td>
            <td>
                <div class="lab"><?php echo e($pourQui ? __('Temps pointé') : __('Collaborateurs')); ?></div>
                <div class="val"><?php echo e($pourQui ? ($k->pointeHeures ?: '—') : $k->gens); ?></div>
                <div class="sub"><?php echo e($pourQui ? __('heures saisies') : __('impliqués')); ?></div>
            </td>
        </tr>
    </table>

    <?php if($k->pointeTaches === 0): ?>
        <div class="note">
            <b><?php echo e(__('Temps pointé indisponible sur cette semaine')); ?></b>
            <?php echo e(__('Aucune des')); ?> <?php echo e($k->total); ?> <?php echo e(__("tâches n'a de saisie de temps. La mesure retenue est le délai de traitement, calculé sur")); ?>

            <?php echo e($k->delaisJuges); ?> <?php echo e(trans_choice('tâche achevée|tâches achevées', $k->delaisJuges)); ?>

            <?php echo e(__('ayant une date de début.')); ?>

        </div>
    <?php endif; ?>

    
    <?php if(!$pourQui && $rapport->collaborateurs->isNotEmpty()): ?>
        <div class="bloc">
            <h2><?php echo e(__('Activité par collaborateur')); ?></h2>
            <table class="data">
                <thead>
                    <tr>
                        <th width="34%"><?php echo e(__('Collaborateur')); ?></th>
                        <th class="num"><?php echo e(__('Tâches')); ?></th>
                        <th class="num"><?php echo e(__('Terminées')); ?></th>
                        <th class="num"><?php echo e(__('En cours')); ?></th>
                        <th class="num"><?php echo e(__('À faire')); ?></th>
                        <th class="num"><?php echo e(__('En retard')); ?></th>
                        <th class="num"><?php echo e(__('Délai moyen')); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $rapport->collaborateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e($i % 2 ? 'alt' : ''); ?>">
                        <td><strong><?php echo e($c->nom); ?></strong></td>
                        <td class="num"><?php echo e($c->total); ?></td>
                        <td class="num"><?php echo e($c->termines); ?></td>
                        <td class="num"><?php echo e($c->enCours); ?></td>
                        <td class="num"><?php echo e($c->aFaire); ?></td>
                        <td class="num">
                            <?php if($c->retards > 0): ?>
                                <span class="p p-err"><?php echo e($c->retards); ?></span>
                            <?php else: ?>
                                0
                            <?php endif; ?>
                        </td>
                        <td class="num">
                            <?php echo e($c->delaiMoyen !== null ? $c->delaiMoyen . ' j' : '—'); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="bloc">
        <h2>
            <?php echo e($pourQui ? __('Mes tâches de la semaine') : __('Détail des tâches')); ?>

            (<?php echo e($rapport->taches->count()); ?>)
        </h2>
        <table class="data">
            <thead>
                <tr>
                    <th width="7%"><?php echo e(__('Réf.')); ?></th>
                    <th width="<?php echo e($pourQui ? '34%' : '27%'); ?>"><?php echo e(__('Tâche')); ?></th>
                    <th width="13%"><?php echo e(__('Statut')); ?></th>
                    <th width="9%"><?php echo e(__('Priorité')); ?></th>
                    <th width="10%"><?php echo e(__('Échéance')); ?></th>
                    <th width="10%"><?php echo e(__('Réalisée')); ?></th>
                    <th width="7%" class="num"><?php echo e(__('Délai')); ?></th>
                    <?php if(!$pourQui): ?><th width="20%"><?php echo e(__('Collaborateurs')); ?></th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $rapport->taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="<?php echo e($i % 2 ? 'alt' : ''); ?>">
                    <td class="gris"><?php echo e($t->reference ?: '—'); ?></td>
                    <td>
                        <div class="tache-titre"><?php echo e(\Illuminate\Support\Str::limit($t->titre, $pourQui ? 66 : 58)); ?></div>
                        <?php if($t->client || $t->projet): ?>
                            <div class="tache-sous"><?php echo e(\Illuminate\Support\Str::limit($t->client ?: $t->projet, 42)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="p p-<?php echo e($teintes[$t->slug] ?? 'neu'); ?>">
                            <?php echo e($libelles[$t->slug] ?? $t->statut); ?>

                        </span>
                        <?php if($t->retard): ?>
                            <br><span class="p p-err"><?php echo e(__('En retard')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($priorites[$t->priorite] ?? $t->priorite); ?></td>
                    <td class="num"><?php echo e($t->echeance?->format('d/m/Y') ?: '—'); ?></td>
                    <td class="num"><?php echo e($t->faitLe?->format('d/m/Y') ?: '—'); ?></td>
                    <td class="num"><?php echo e($t->delai !== null ? $t->delai . ' j' : '—'); ?></td>
                    <?php if(!$pourQui): ?>
                        <td>
                            <?php if($t->gens->isNotEmpty()): ?>
                                <?php echo e($t->gens->pluck('nom')->implode(', ')); ?>

                            <?php else: ?>
                                <span class="gris"><?php echo e(__('non affectée')); ?></span>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>

<table class="pied" width="100%">
    <tr>
        <td width="72%">
            <?php echo e($societe->company_name); ?> — <?php echo e($pourQui ?: __('Rapport hebdomadaire')); ?>,
            <?php echo e(__('semaine du')); ?> <?php echo e($rapport->debut->format('d/m/Y')); ?>

        </td>
        <td width="28%" style="text-align:right">
            <script type="text/php">
                if (isset($pdf)) {
                    $pdf->page_text($pdf->get_width() - 90, $pdf->get_height() - 32,
                        "Page {PAGE_NUM} / {PAGE_COUNT}", null, 7, [0.61, 0.64, 0.68]);
                }
            </script>
        </td>
    </tr>
</table>

</body>
</html>
<?php /**PATH C:\laragon\www\CRM\resources\views/reports/tasks/pdf/hebdomadaire.blade.php ENDPATH**/ ?>