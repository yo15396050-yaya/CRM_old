

<?php
    // dueTasks est un entier : EmployeeDashboard y stocke deja un count().
    // La conversion accepte aussi une collection, au cas ou la source change.
    $enRetard = ($dueTasks ?? 0) instanceof \Countable || is_array($dueTasks ?? 0)
        ? count($dueTasks)
        : (int) ($dueTasks ?? 0);
?>

<div class="dck-espace">

    
    <?php if($enRetard > 0): ?>
        <div class="dck-note is-err">
            <span class="dck-note__i"><i class="bi bi-exclamation-triangle"></i></span>
            <div>
                <b><?php echo e(trans_choice('Une de vos tâches a dépassé son échéance|:n de vos tâches ont dépassé leur échéance', $enRetard, ['n' => $enRetard])); ?></b>
                <p>Mettez-les à jour ou signalez un blocage à votre responsable.</p>
            </div>
            <a class="dck-note__act" href="<?php echo e(route('tasks.index')); ?>">Ouvrir</a>
        </div>
    <?php endif; ?>

    
    <div class="dck-grid dck-g3">
        <a class="dck-kpi2" href="<?php echo e(route('tasks.index')); ?>">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-check2-square"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Tâches en cours'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e($inProcessTasks ?? 0); ?></div>
            <div class="dck-kpi2__sub">qui vous sont assignées</div>
        </a>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-check2-circle"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Terminées ce mois'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e($espaceTermineesMois ?? 0); ?></div>
            <div class="dck-kpi2__sub">depuis le <?php echo e(now()->startOfMonth()->translatedFormat('j F')); ?></div>
        </div>

        <a class="dck-kpi2" href="<?php echo e(route('projects.index')); ?>">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-folder2-open"></i></span>
                <span class="dck-kpi2__lab"><?php echo app('translator')->get('Mes projets'); ?></span>
            </div>
            <div class="dck-kpi2__val"><?php echo e($totalProjects ?? 0); ?></div>
            <div class="dck-kpi2__sub">dont vous êtes membre</div>
        </a>
    </div>

    
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3><?php echo app('translator')->get('Mes tâches du moment'); ?></h3>
                    <div class="dck-c__sub">Classées par échéance</div>
                </div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="<?php echo e(route('tasks.index')); ?>">Tout voir</a>
                </div>
            </div>
            <div class="dck-c__body dck-pile">
                <?php $__empty_1 = true; $__currentLoopData = ($pendingTasks ?? collect())->sortBy('due_date')->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $tRetard = $t->due_date && $t->due_date->endOfDay()->isPast(); ?>
                    <a class="dck-tc" href="<?php echo e(route('tasks.show', $t->id)); ?>">
                        <div class="dck-tc__top">
                            <span class="dck-tc__id"><?php echo e($t->task_short_code ?: '—'); ?></span>
                            <span class="dck-pill <?php echo e($tRetard ? 'is-late' : 'is-doing'); ?>">
                                <?php echo e($tRetard ? __('En retard') : optional($t->boardColumn)->column_name); ?>

                            </span>
                        </div>
                        <h5><?php echo e($t->heading); ?></h5>
                        <div class="dck-tc__meta">
                            <i class="bi bi-folder2"></i>
                            <span><?php echo e(optional($t->activeProject)->project_name ?: __('Sans projet')); ?></span>
                            <span class="dck-tc__ech <?php echo e($tRetard ? 'is-late' : ''); ?>">
                                <?php echo e($t->due_date ? $t->due_date->translatedFormat('d/m/Y') : '—'); ?>

                            </span>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="dck-vide2"><?php echo app('translator')->get('Aucune tâche en cours. Bonne journée.'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="dck-c">
            <div class="dck-c__head">
                <div><h3><?php echo app('translator')->get('Mes projets'); ?></h3></div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="<?php echo e(route('projects.index')); ?>">Ouvrir</a>
                </div>
            </div>
            <div class="dck-c__body dck-pile-l">
                <?php $__empty_1 = true; $__currentLoopData = $espaceProjets ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $av = (int) $d->completion_percent; ?>
                    <div>
                        <div class="dck-dl2">
                            <a class="dck-lien2" href="<?php echo e(route('projects.show', $d->id)); ?>"><?php echo e($d->project_name); ?></a>
                            <b><?php echo e($av); ?> %</b>
                        </div>
                        <span class="dck-bar <?php echo e($av >= 80 ? 'is-ok' : ($av >= 40 ? '' : 'is-warn')); ?>">
                            <i style="width:<?php echo e($av); ?>%"></i>
                        </span>
                        <div class="dck-dl2__sub"><?php echo e($d->taches_faites); ?> / <?php echo e($d->taches_total); ?> tâches terminées</div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="dck-vide2"><?php echo app('translator')->get('Vous n\'êtes membre d\'aucun projet.'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        
    </div>

</div>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/dashboard/employee/mon-espace.blade.php ENDPATH**/ ?>