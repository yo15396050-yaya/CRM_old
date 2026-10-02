

<?php
    $k = $cabIndicateurs;
    $enRetard = \App\Models\Task::withoutGlobalScopes()
        ->where('company_id', company()->id)->whereNull('deleted_at')
        ->whereNotNull('due_date')->whereDate('due_date', '<', now())
        ->where('board_column_id', '<>', \App\Models\TaskboardColumn::completeColumn()->id)
        ->count();
?>

<section class="dck-pilotage">

    
    <?php if($enRetard > 0): ?>
        <div class="dck-note is-warn">
            <span class="dck-note__i"><i class="bi bi-exclamation-triangle"></i></span>
            <div>
                <b><?php echo e($enRetard); ?> <?php echo e(trans_choice('tâche a dépassé son échéance|tâches ont dépassé leur échéance', $enRetard)); ?></b>
                <p>Ouvrez le tableau des tâches pour les réaffecter.</p>
            </div>
            <a class="dck-note__act" href="<?php echo e(route('taskboards.index')); ?>">Voir les tâches</a>
        </div>
    <?php endif; ?>

    
    <div class="dck-grid dck-g4">
        <a class="dck-kpi2" href="<?php echo e(route('tasks.index')); ?>">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-check2-square"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Volume du mois'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e($k->tachesOuvertes); ?> <span class="dck-kpi2__tot">/ <?php echo e($k->tachesMois); ?></span></div>
            <div class="dck-kpi2__sub">en cours sur le mois</div>
        </a>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Taux de clôture'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e(is_null($k->tauxTaches) ? '—' : $k->tauxTaches . ' %'); ?></div>
            <div class="dck-kpi2__sub"><?php echo e($k->tachesFinies); ?> tâches terminées</div>
        </div>

        <a class="dck-kpi2" href="<?php echo e(route('taskboards.index')); ?>">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-inbox"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Backlog'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e($k->backlog); ?></div>
            <div class="dck-kpi2__sub">en attente de prise en charge</div>
        </a>

        <a class="dck-kpi2" href="<?php echo e(route('employees.index')); ?>">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-people"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Collaborateurs'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e($k->collaborateurs); ?></div>
            <div class="dck-kpi2__sub">actifs dans le cabinet</div>
        </a>

        <a class="dck-kpi2" href="<?php echo e(route('clients.index')); ?>">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-building"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Clients'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e($k->clients); ?></div>
            <div class="dck-kpi2__sub"><?php echo e($k->contrats); ?> contrats en vigueur</div>
        </a>

        
        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-calendar-check"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Respect des échéances'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e(is_null($k->respect) ? '—' : $k->respect . ' %'); ?></div>
            <div class="dck-kpi2__sub">
                <?php echo e($k->respectATemps); ?> / <?php echo e($k->respectTotal); ?> livrées à temps
            </div>
        </div>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-stopwatch"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Délai moyen'); ?></span>
            </div>
            <div class="dck-kpi2__val">
                <?php echo e(is_null($k->delaiMoyen) ? '—' : $k->delaiMoyen); ?>

                <?php if(!is_null($k->delaiMoyen)): ?><span class="dck-kpi2__tot">jours</span><?php endif; ?>
            </div>
            <div class="dck-kpi2__sub">de la création à la clôture</div>
        </div>

        <a class="dck-kpi2" href="<?php echo e(route('contracts.index')); ?>">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-cash-stack"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Portefeuille'); ?></span>
            </div>
            <?php
                /* Au-dela du million, le montant complet ne tient plus dans
                   une carte a quatre colonnes : on abrege, comme la maquette
                   le fait pour la valeur cumulee. */
                $montant = $k->portefeuille >= 1000000
                    ? number_format($k->portefeuille / 1000000, 1, ',', ' ') . ' M'
                    : number_format($k->portefeuille, 0, ',', ' ');
            ?>
            <div class="dck-kpi2__val">
                <?php echo e($montant); ?>

                <span class="dck-kpi2__tot">FCFA</span>
            </div>
            <div class="dck-kpi2__sub">sur <?php echo e($k->portefeuilleN); ?> contrats en cours</div>
        </a>
    </div>

    
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3><?php echo app('translator')->get('Charge de travail'); ?></h3>
                    <div class="dck-c__sub">Tâches créées et terminées sur six mois</div>
                </div>
            </div>
            <div class="dck-c__body">
                <?php
                    $lab = $cabCharge['labels'];
                    $s1 = $cabCharge['creees'];
                    $s2 = $cabCharge['terminees'];
                    $haut = max(1, max(max($s1), max($s2)));
                    $L = 560; $H = 200;
                    $mg = ['t' => 12, 'r' => 8, 'b' => 24, 'l' => 30];
                    $pl = $L - $mg['l'] - $mg['r'];
                    $ph = $H - $mg['t'] - $mg['b'];
                    $pas = count($lab) > 1 ? $pl / (count($lab) - 1) : $pl;
                    $pt = function ($serie) use ($mg, $pas, $ph, $haut) {
                        $p = [];
                        foreach ($serie as $i => $v) {
                            $p[] = round($mg['l'] + $pas * $i, 1) . ',' . round($mg['t'] + $ph - ($ph * $v / $haut), 1);
                        }
                        return implode(' ', $p);
                    };
                ?>
                <div class="dck-chart">
                    <svg class="dck-aires" viewBox="0 0 <?php echo e($L); ?> <?php echo e($H); ?>" role="img"
                         aria-label="Tâches créées et terminées sur six mois">
                        <?php for($i = 0; $i <= 4; $i++): ?>
                            <?php $y = $mg['t'] + $ph - ($ph * $i / 4); ?>
                            <line class="<?php echo e($i === 0 ? 'dck-zero' : 'dck-grid'); ?>"
                                  x1="<?php echo e($mg['l']); ?>" y1="<?php echo e(round($y, 1)); ?>"
                                  x2="<?php echo e($L - $mg['r']); ?>" y2="<?php echo e(round($y, 1)); ?>"/>
                            <text class="dck-axe" x="<?php echo e($mg['l'] - 6); ?>" y="<?php echo e(round($y + 3, 1)); ?>"
                                  text-anchor="end"><?php echo e(round($haut * $i / 4)); ?></text>
                        <?php endfor; ?>

                        <polyline class="dck-serie dck-serie--1" points="<?php echo e($pt($s1)); ?>"/>
                        <polyline class="dck-serie dck-serie--2" points="<?php echo e($pt($s2)); ?>"/>

                        <?php $__currentLoopData = $lab; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <text class="dck-axe" x="<?php echo e(round($mg['l'] + $pas * $i, 1)); ?>"
                                  y="<?php echo e($H - 6); ?>" text-anchor="middle"><?php echo e($l); ?></text>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </svg>
                </div>
                <div class="dck-legende2">
                    <span><i style="background:#175CD3"></i>Tâches créées</span>
                    <span><i style="background:#0E7C5A"></i>Tâches terminées</span>
                </div>
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3><?php echo app('translator')->get('Répartition des tâches'); ?></h3>
                    <div class="dck-c__sub">Par colonne du tableau</div>
                </div>
            </div>
            <div class="dck-c__body">
                <?php $maxCol = max(1, $cabRepartition->max('n')); ?>
                <?php $__currentLoopData = $cabRepartition; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="dck-bl">
                        <span class="dck-bl__j"><?php echo e($col->column_name); ?></span>
                        <span class="dck-bar">
                            <i style="width:<?php echo e(round(100 * $col->n / $maxCol)); ?>%;background:<?php echo e($col->label_color); ?>"></i>
                        </span>
                        <span class="dck-bl__v"><?php echo e($col->n); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3><?php echo app('translator')->get('Tâches à surveiller'); ?></h3>
                    <div class="dck-c__sub">Échéance dans moins de 15 jours ou déjà dépassée</div>
                </div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="<?php echo e(route('taskboards.index')); ?>">Tout voir</a>
                </div>
            </div>
            <div class="dck-tbl">
                <table>
                    <thead>
                        <tr>
                            <th><?php echo app('translator')->get('app.task'); ?></th>
                            <th><?php echo app('translator')->get('modules.tasks.assignTo'); ?></th>
                            <th><?php echo app('translator')->get('app.status'); ?></th>
                            <th><?php echo app('translator')->get('app.dueDate'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $cabASurveiller; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $depasse = $t->due_date && $t->due_date->isPast(); ?>
                            <tr>
                                <td><a class="dck-ttl" href="<?php echo e(route('tasks.show', $t->id)); ?>"><?php echo e($t->heading); ?></a></td>
                                <td class="dck-cl"><?php echo e($t->users->pluck('name')->implode(', ') ?: '—'); ?></td>
                                <td>
                                    <span class="dck-pastille"
                                          style="background:<?php echo e(optional($t->boardColumn)->label_color ?: '#9ca3af'); ?>"></span>
                                    <?php echo e(optional($t->boardColumn)->column_name ?: '—'); ?>

                                </td>
                                <td class="dck-date <?php echo e($depasse ? 'is-late' : ''); ?>">
                                    <?php echo e($t->due_date ? $t->due_date->translatedFormat('d/m/Y') : '—'); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="dck-vide"><?php echo app('translator')->get('Aucune échéance proche'); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        

        
        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3><?php echo app('translator')->get('Tâches clôturées'); ?></h3>
                    <div class="dck-c__sub">Sur les trois derniers mois</div>
                </div>
            </div>
            <div class="dck-c__body">
                <?php $hautCollab = max(1, ($cabTopCollab ?? collect())->max('n') ?? 1); ?>

                <?php $__empty_1 = true; $__currentLoopData = $cabTopCollab ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="dck-bl">
                        <span class="dck-bl__j"><?php echo e($c->name); ?></span>
                        <span class="dck-bar">
                            <i style="width:<?php echo e(round(100 * $c->n / $hautCollab)); ?>%"></i>
                        </span>
                        <span class="dck-bl__v"><?php echo e($c->n); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="dck-vide2"><?php echo app('translator')->get('Aucune tâche clôturée sur la période.'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="dck-grid">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3><?php echo app('translator')->get('Projets livrées à temps'); ?></h3>
                    <div class="dck-c__sub">Part des projets terminés avant leur date limite</div>
                </div>
            </div>
            <div class="dck-c__body">
                <div class="dck-taux">
                    <b><?php echo e(is_null($cabTauxCloture) ? '—' : $cabTauxCloture . ' %'); ?></b>
                    <span class="dck-bar <?php echo e(($cabTauxCloture ?? 0) >= 80 ? 'is-ok' : 'is-warn'); ?>">
                        <i style="width:<?php echo e($cabTauxCloture ?? 0); ?>%"></i>
                    </span>
                </div>
            </div>
        </div>

        
    </div>

</section>
<?php /**PATH C:\laragon\www\CRM\resources\views/dashboard/ajax/pilotage.blade.php ENDPATH**/ ?>