<?php
    /*
     * Carte du tableau des taches.
     *
     * Disposition conforme a la maquette : la reference et la priorite sur
     * la premiere ligne, le titre ensuite, puis les intervenants et les
     * compteurs face a l'echeance.
     *
     * Les crochets du glisser-deposer sont inchanges : classe « task-card »,
     * data-task-id, id « drag-task-N » et « move-disable ». Les toucher
     * casserait le tableau.
     */
    $moveClass = $draggable == 'false' ? 'move-disable' : '';

    // Palette de la maquette, verifiee pour le contraste.
    $prio = [
        'high'   => ['#B42318', __('modules.tasks.high')],
        'medium' => ['#B54708', __('modules.tasks.medium')],
        'low'    => ['#0E7C5A', __('modules.tasks.low')],
    ];
    [$prioCouleur, $prioLibelle] = $prio[$task->priority] ?? $prio['low'];

    $enRetard = !is_null($task->due_date) && $task->due_date->endOfDay()->isPast();
    $aujourdhui = !is_null($task->due_date)
        && $task->due_date->setTimezone($company->timezone)->isToday();
?>

<div class="dck-tk <?php echo e($moveClass); ?> task-card"
     data-task-id="<?php echo e($task->id); ?>"
     data-need-approval="<?php echo e(optional($task->project)->need_approval_by_admin ?? 0); ?>"
     id="drag-task-<?php echo e($task->id); ?>"
     style="--dck-prio:<?php echo e($prioCouleur); ?>">

    
    <div class="dck-tk__top">
        <span class="dck-tk__ref"><?php echo e($task->task_short_code ? 'T-' . $task->task_short_code : '—'); ?></span>
        <span class="dck-tk__prio"><?php echo e($prioLibelle); ?></span>

        <?php if($task->is_private): ?>
            <span class="dck-tk__prive" title="<?php echo app('translator')->get('app.private'); ?>">
                <i class="fa fa-lock"></i>
            </span>
        <?php endif; ?>

        
        <?php if(peut_modifier_tache($task)): ?>
            <a href="<?php echo e(route('tasks.edit', [$task->id])); ?>" class="dck-tk__edit openRightModal"
               data-redirect-url="<?php echo e(url()->full()); ?>"
               title="<?php echo app('translator')->get('app.edit'); ?>" aria-label="<?php echo app('translator')->get('app.edit'); ?>">
                <i class="fa fa-edit"></i>
            </a>
        <?php endif; ?>
    </div>

    
    <a href="<?php echo e(route('tasks.show', [$task->id])); ?>" class="dck-tk__titre openRightModal" data-redirect-url="<?php echo e(url()->full()); ?>">
        <?php echo e($task->heading); ?>

    </a>

    
    <?php if(count($task->labels) > 0): ?>
        <div class="dck-tk__etiq">
            <?php $__currentLoopData = $task->labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span style="background:<?php echo e($label->label_color); ?>"><?php echo e($label->label_name); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    
    <div class="dck-tk__bas">
        <div class="dck-tk__qui">
            <?php $__currentLoopData = $task->users->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $initiales = collect(preg_split('/\s+/', trim($item->name)))
                        ->filter()->take(2)
                        ->map(fn ($m) => mb_strtoupper(mb_substr($m, 0, 1)))
                        ->implode('');
                ?>
                <span class="dck-tk__av" title="<?php echo e($item->name); ?>"><?php echo e($initiales ?: '?'); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($task->users->count() > 3): ?>
                <span class="dck-tk__av is-plus">+<?php echo e($task->users->count() - 3); ?></span>
            <?php endif; ?>
        </div>

        <div class="dck-tk__meta">
            <?php if(($task->files_count ?? 0) > 0): ?>
                <span title="<?php echo app('translator')->get('app.file'); ?>"><i class="bi bi-paperclip"></i><?php echo e($task->files_count); ?></span>
            <?php endif; ?>

            <?php if(($task->comments_count ?? 0) > 0): ?>
                <span title="<?php echo app('translator')->get('app.comment'); ?>"><i class="bi bi-chat-left"></i><?php echo e($task->comments_count); ?></span>
            <?php endif; ?>

            <?php if($task->subtasks_count > 0): ?>
                <span title="<?php echo app('translator')->get('app.subTask'); ?>">
                    <i class="bi bi-diagram-2"></i><?php echo e($task->completed_subtasks_count); ?>/<?php echo e($task->subtasks_count); ?>

                </span>
            <?php endif; ?>
        </div>

        <?php if(!is_null($task->due_date)): ?>
            <span class="dck-tk__ech <?php echo e($enRetard ? 'is-late' : ($aujourdhui ? 'is-today' : '')); ?>">
                <?php echo e($aujourdhui ? __('app.today') : $task->due_date->translatedFormat($company->date_format)); ?>

            </span>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/cards/task-card.blade.php ENDPATH**/ ?>