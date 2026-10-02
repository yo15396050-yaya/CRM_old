

<?php $__env->startSection('content'); ?>

    <div class="content-wrapper dck-front">

        <?php if (isset($component)) { $__componentOriginale44c97c7bfb598419dda3f1c66ab979c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale44c97c7bfb598419dda3f1c66ab979c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.page-head','data' => ['titre' => __('Résultats de recherche'),'description' => $mot !== '' ? __('Pour « :mot »', ['mot' => $mot]) : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['titre' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Résultats de recherche')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mot !== '' ? __('Pour « :mot »', ['mot' => $mot]) : null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale44c97c7bfb598419dda3f1c66ab979c)): ?>
<?php $attributes = $__attributesOriginale44c97c7bfb598419dda3f1c66ab979c; ?>
<?php unset($__attributesOriginale44c97c7bfb598419dda3f1c66ab979c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale44c97c7bfb598419dda3f1c66ab979c)): ?>
<?php $component = $__componentOriginale44c97c7bfb598419dda3f1c66ab979c; ?>
<?php unset($__componentOriginale44c97c7bfb598419dda3f1c66ab979c); ?>
<?php endif; ?>

        
        <form class="dck-res__form" action="<?php echo e(route('search.resultats')); ?>" method="GET" role="search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="<?php echo e($mot); ?>" autocomplete="off"
                   placeholder="<?php echo e(__('Rechercher une tâche, un client, un contrat…')); ?>"
                   aria-label="<?php echo e(__('Rechercher')); ?>">
            <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('app.search'); ?></button>
        </form>

        <?php if($tropCourt): ?>
            <div class="dck-note is-warn">
                <span class="dck-note__i"><i class="bi bi-info-circle"></i></span>
                <div>
                    <b><?php echo app('translator')->get('Saisissez au moins deux caractères'); ?></b>
                    <p><?php echo app('translator')->get('Une lettre seule ramènerait presque toute la base.'); ?></p>
                </div>
            </div>
        <?php elseif($mot === ''): ?>
            <div class="dck-note">
                <span class="dck-note__i"><i class="bi bi-search"></i></span>
                <div>
                    <b><?php echo app('translator')->get('Que cherchez-vous ?'); ?></b>
                    <p><?php echo app('translator')->get('La recherche porte sur les tâches, les projets et les clients.'); ?></p>
                </div>
            </div>
        <?php elseif(($total ?? 0) === 0): ?>
            <div class="dck-note">
                <span class="dck-note__i"><i class="bi bi-inbox"></i></span>
                <div>
                    <b><?php echo app('translator')->get('Aucun résultat pour « :mot »', ['mot' => $mot]); ?></b>
                    <p><?php echo app('translator')->get('Vérifiez l\'orthographe, ou cherchez sur un mot plus court.'); ?></p>
                </div>
            </div>
        <?php else: ?>

            
            <?php if($taches->isNotEmpty()): ?>
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3><?php echo app('translator')->get('app.menu.tasks'); ?></h3>
                            <div class="dck-c__sub"><?php echo e($taches->count()); ?> <?php echo e(trans_choice('résultat|résultats', $taches->count())); ?></div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="<?php echo e(route('tasks.index')); ?>?search_keyword=<?php echo e(urlencode($mot)); ?>">
                                <?php echo app('translator')->get('Tout voir'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        <?php $__currentLoopData = $taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dck-res__item" href="<?php echo e(route('tasks.show', $t->id)); ?>">
                                <span class="dck-res__ico"><i class="bi bi-check2-square"></i></span>
                                <span class="dck-res__txt">
                                    <b><?php echo e($t->heading); ?></b>
                                    <small>
                                        <?php echo e($t->task_short_code ?: '—'); ?>

                                        <?php if($t->due_date): ?> · <?php echo e($t->due_date->translatedFormat('d/m/Y')); ?> <?php endif; ?>
                                    </small>
                                </span>
                                <?php if($t->boardColumn): ?>
                                    <span class="dck-res__badge"
                                          style="background:<?php echo e($t->boardColumn->label_color); ?>1a;color:<?php echo e($t->boardColumn->label_color); ?>">
                                        <?php echo e($t->boardColumn->column_name); ?>

                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($projets->isNotEmpty()): ?>
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3><?php echo app('translator')->get('app.menu.projects'); ?></h3>
                            <div class="dck-c__sub"><?php echo e($projets->count()); ?> <?php echo e(trans_choice('résultat|résultats', $projets->count())); ?></div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="<?php echo e(route('projects.index')); ?>?search_keyword=<?php echo e(urlencode($mot)); ?>">
                                <?php echo app('translator')->get('Tout voir'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dck-res__item" href="<?php echo e(route('projects.show', $d->id)); ?>">
                                <span class="dck-res__ico"><i class="bi bi-folder2-open"></i></span>
                                <span class="dck-res__txt">
                                    <b><?php echo e($d->project_name); ?></b>
                                    <small><?php echo e($d->project_short_code ?: '—'); ?></small>
                                </span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($contrats->isNotEmpty()): ?>
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3><?php echo app('translator')->get('app.menu.contracts'); ?></h3>
                            <div class="dck-c__sub"><?php echo e($contrats->count()); ?> <?php echo e(trans_choice('résultat|résultats', $contrats->count())); ?></div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="<?php echo e(route('contracts.index')); ?>?search_keyword=<?php echo e(urlencode($mot)); ?>">
                                <?php echo app('translator')->get('Tout voir'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        <?php $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dck-res__item" href="<?php echo e(route('contracts.show', $ct->id)); ?>">
                                <span class="dck-res__ico"><i class="bi bi-file-earmark-text"></i></span>
                                <span class="dck-res__txt">
                                    <b><?php echo e($ct->subject); ?></b>
                                    <small>
                                        <?php echo e($ct->contract_number ?: '—'); ?>

                                        <?php if($ct->client): ?> · <?php echo e($ct->client->name); ?> <?php endif; ?>
                                        <?php if($ct->end_date): ?> · <?php echo app('translator')->get('jusqu\'au'); ?> <?php echo e($ct->end_date->translatedFormat('d/m/Y')); ?> <?php endif; ?>
                                    </small>
                                </span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($clients->isNotEmpty()): ?>
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3><?php echo app('translator')->get('app.menu.clients'); ?></h3>
                            <div class="dck-c__sub"><?php echo e($clients->count()); ?> <?php echo e(trans_choice('résultat|résultats', $clients->count())); ?></div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="<?php echo e(route('clients.index')); ?>?search_keyword=<?php echo e(urlencode($mot)); ?>">
                                <?php echo app('translator')->get('Tout voir'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dck-res__item" href="<?php echo e(route('clients.show', $c->id)); ?>">
                                <span class="dck-res__ico"><i class="bi bi-building"></i></span>
                                <span class="dck-res__txt">
                                    <b><?php echo e($c->company_name ?: $c->name); ?></b>
                                    <small>
                                        <?php if($c->company_name): ?> <?php echo e($c->name); ?> · <?php endif; ?>
                                        <?php echo e($c->email); ?>

                                    </small>
                                </span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/search/resultats.blade.php ENDPATH**/ ?>