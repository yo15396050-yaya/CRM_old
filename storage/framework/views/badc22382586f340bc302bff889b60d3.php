

<?php $__env->startPush('datatable-styles'); ?>
    <?php echo $__env->make('sections.datatable_css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('filter-section'); ?>

    <?php if (isset($component)) { $__componentOriginald1a72e1108842d163a80559e15f530b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald1a72e1108842d163a80559e15f530b4 = $attributes; } ?>
<?php $component = App\View\Components\Filters\FilterBox::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filters.filter-box'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Filters\FilterBox::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <!-- DATE START -->
        <div class="select-box d-flex pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center"><?php echo app('translator')->get('app.duration'); ?></p>
            <div class="select-status d-flex">
                <input type="text"
                    class="position-relative text-dark form-control border-0 p-2 text-left f-14 f-w-500 border-additional-grey"
                    id="datatableRange" placeholder="<?php echo app('translator')->get('placeholders.dateRange'); ?>">
            </div>
        </div>
        <!-- DATE END -->

        <?php if(!in_array('client', user_roles())): ?>
            <!-- CLIENT START -->
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center"><?php echo app('translator')->get('app.client'); ?></p>
                <div class="select-status">
                    <select class="form-control select-picker" name="client" id="client" data-live-search="true"
                        data-size="8">
                        <?php if(!in_array('client', user_roles())): ?>
                            <option value="all"><?php echo app('translator')->get('app.all'); ?></option>
                        <?php endif; ?>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if (isset($component)) { $__componentOriginal6c7097547485b98631a37d273a171e9f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6c7097547485b98631a37d273a171e9f = $attributes; } ?>
<?php $component = App\View\Components\UserOption::resolve(['user' => $client] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('user-option'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\UserOption::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $attributes = $__attributesOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__attributesOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6c7097547485b98631a37d273a171e9f)): ?>
<?php $component = $__componentOriginal6c7097547485b98631a37d273a171e9f; ?>
<?php unset($__componentOriginal6c7097547485b98631a37d273a171e9f); ?>
<?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <!-- CLIENT END -->
        <!-- STATUS START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center"><?php echo app('translator')->get('modules.contracts.contractType'); ?></p>
            <div class="select-status">
                <select class="form-control select-picker" name="contract_type" id="contract_type" data-live-search="true"
                    data-size="8">
                    <option value="all"><?php echo app('translator')->get('app.all'); ?></option>
                    <?php $__currentLoopData = $contractTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <!-- STATUS END -->

        <!-- SEARCH BY TASK START -->
        <div class="task-search d-flex  py-1 px-lg-3 px-0 border-right-grey align-items-center">
            <form class="w-100 mr-1 mr-lg-0 mr-md-1 ml-md-1 ml-0 ml-lg-0">
                <div class="input-group bg-grey rounded">
                    <div class="input-group-prepend">
                        <span class="input-group-text border-0 bg-additional-grey">
                            <i class="fa fa-search f-13 text-dark-grey"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control f-14 p-1 border-additional-grey" id="search-text-field"
                        placeholder="<?php echo app('translator')->get('app.startTyping'); ?>">
                </div>
            </form>
        </div>
        <!-- SEARCH BY TASK END -->

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <?php if (isset($component)) { $__componentOriginal5e57c6582b8a883148a28bb7ee46d2ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e57c6582b8a883148a28bb7ee46d2ad = $attributes; } ?>
<?php $component = App\View\Components\Forms\ButtonSecondary::resolve(['icon' => 'times-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.button-secondary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\ButtonSecondary::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-xs d-none','id' => 'reset-filters']); ?>
                <?php echo app('translator')->get('app.clearFilters'); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e57c6582b8a883148a28bb7ee46d2ad)): ?>
<?php $attributes = $__attributesOriginal5e57c6582b8a883148a28bb7ee46d2ad; ?>
<?php unset($__attributesOriginal5e57c6582b8a883148a28bb7ee46d2ad); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e57c6582b8a883148a28bb7ee46d2ad)): ?>
<?php $component = $__componentOriginal5e57c6582b8a883148a28bb7ee46d2ad; ?>
<?php unset($__componentOriginal5e57c6582b8a883148a28bb7ee46d2ad); ?>
<?php endif; ?>
        </div>
        <!-- RESET END -->
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald1a72e1108842d163a80559e15f530b4)): ?>
<?php $attributes = $__attributesOriginald1a72e1108842d163a80559e15f530b4; ?>
<?php unset($__attributesOriginald1a72e1108842d163a80559e15f530b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald1a72e1108842d163a80559e15f530b4)): ?>
<?php $component = $__componentOriginald1a72e1108842d163a80559e15f530b4; ?>
<?php unset($__componentOriginald1a72e1108842d163a80559e15f530b4); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php
    $addContractPermission = user()->permission('add_contract');
    $manageContractTemplatePermission = user()->permission('manage_contract_template');

?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper dck-front">

        
        <?php if (isset($component)) { $__componentOriginale44c97c7bfb598419dda3f1c66ab979c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale44c97c7bfb598419dda3f1c66ab979c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.page-head','data' => ['titre' => __('app.menu.contracts'),'description' => __('modules.contracts.totalContracts') . ' : ' . $contractCounts]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['titre' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.menu.contracts')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('modules.contracts.totalContracts') . ' : ' . $contractCounts)]); ?>
             <?php $__env->slot('actions', null, []); ?> 
                <?php if(in_array('clients', user_modules()) && ($addContractPermission == 'all' || $addContractPermission == 'added')): ?>
                    <?php if (isset($component)) { $__componentOriginaldbb84df4c3a5cbdd95fb35d18ba6410f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldbb84df4c3a5cbdd95fb35d18ba6410f = $attributes; } ?>
<?php $component = App\View\Components\Forms\LinkPrimary::resolve(['link' => route('contracts.create'),'icon' => 'plus'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.link-primary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\LinkPrimary::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'openRightModal']); ?>
                        <?php echo app('translator')->get('modules.contracts.createContract'); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldbb84df4c3a5cbdd95fb35d18ba6410f)): ?>
<?php $attributes = $__attributesOriginaldbb84df4c3a5cbdd95fb35d18ba6410f; ?>
<?php unset($__attributesOriginaldbb84df4c3a5cbdd95fb35d18ba6410f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldbb84df4c3a5cbdd95fb35d18ba6410f)): ?>
<?php $component = $__componentOriginaldbb84df4c3a5cbdd95fb35d18ba6410f; ?>
<?php unset($__componentOriginaldbb84df4c3a5cbdd95fb35d18ba6410f); ?>
<?php endif; ?>
                <?php endif; ?>

                <?php if($manageContractTemplatePermission == 'all' || $manageContractTemplatePermission == 'added'): ?>
                    <?php if (isset($component)) { $__componentOriginal29acd9b76240152ae380821b082bd629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal29acd9b76240152ae380821b082bd629 = $attributes; } ?>
<?php $component = App\View\Components\Forms\LinkSecondary::resolve(['link' => route('contract-template.index'),'icon' => 'layer-group'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.link-secondary'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\LinkSecondary::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <?php echo app('translator')->get('app.menu.contractTemplate'); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal29acd9b76240152ae380821b082bd629)): ?>
<?php $attributes = $__attributesOriginal29acd9b76240152ae380821b082bd629; ?>
<?php unset($__attributesOriginal29acd9b76240152ae380821b082bd629); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal29acd9b76240152ae380821b082bd629)): ?>
<?php $component = $__componentOriginal29acd9b76240152ae380821b082bd629; ?>
<?php unset($__componentOriginal29acd9b76240152ae380821b082bd629); ?>
<?php endif; ?>
                <?php endif; ?>
             <?php $__env->endSlot(); ?>
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

        
        <div class="dck-grid dck-grid--4">
            <?php if (isset($component)) { $__componentOriginalf3908f9d134b3fc818feca55596be061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3908f9d134b3fc818feca55596be061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.kpi','data' => ['icone' => 'bi-file-earmark-text','cle' => 'total','libelle' => __('modules.contracts.totalContracts'),'valeur' => $contractCounts,'sous' => __('app.all')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icone' => 'bi-file-earmark-text','cle' => 'total','libelle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('modules.contracts.totalContracts')),'valeur' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($contractCounts),'sous' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.all'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $attributes = $__attributesOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__attributesOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $component = $__componentOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__componentOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalf3908f9d134b3fc818feca55596be061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3908f9d134b3fc818feca55596be061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.kpi','data' => ['icone' => 'bi-check2-circle','ton' => 'ok','cle' => 'actifs','libelle' => __('modules.contracts.active'),'valeur' => max($contractCounts - $expiredCounts, 0),'sous' => __('app.active')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icone' => 'bi-check2-circle','ton' => 'ok','cle' => 'actifs','libelle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('modules.contracts.active')),'valeur' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(max($contractCounts - $expiredCounts, 0)),'sous' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.active'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $attributes = $__attributesOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__attributesOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $component = $__componentOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__componentOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalf3908f9d134b3fc818feca55596be061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3908f9d134b3fc818feca55596be061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.kpi','data' => ['icone' => 'bi-hourglass-split','ton' => 'warn','cle' => 'bientot','libelle' => __('modules.contracts.aboutToExpire'),'valeur' => $aboutToExpireCounts,'sous' => __('app.days') . ' : 7']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icone' => 'bi-hourglass-split','ton' => 'warn','cle' => 'bientot','libelle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('modules.contracts.aboutToExpire')),'valeur' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($aboutToExpireCounts),'sous' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.days') . ' : 7')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $attributes = $__attributesOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__attributesOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $component = $__componentOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__componentOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalf3908f9d134b3fc818feca55596be061 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3908f9d134b3fc818feca55596be061 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.kpi','data' => ['icone' => 'bi-exclamation-triangle','ton' => 'err','cle' => 'expires','libelle' => __('modules.contracts.expired'),'valeur' => $expiredCounts,'sous' => __('modules.contracts.expired')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.kpi'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icone' => 'bi-exclamation-triangle','ton' => 'err','cle' => 'expires','libelle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('modules.contracts.expired')),'valeur' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($expiredCounts),'sous' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('modules.contracts.expired'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $attributes = $__attributesOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__attributesOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf3908f9d134b3fc818feca55596be061)): ?>
<?php $component = $__componentOriginalf3908f9d134b3fc818feca55596be061; ?>
<?php unset($__componentOriginalf3908f9d134b3fc818feca55596be061); ?>
<?php endif; ?>
        </div>

        
        <div class="dck-card">

            <div class="dck-card__bar">
                
                <div id="table-actions" class="d-flex align-items-center"></div>

                <?php if(!in_array('client', user_roles())): ?>
                    <?php if (isset($component)) { $__componentOriginald48019b35c4ccf364e5d37d92848414c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald48019b35c4ccf364e5d37d92848414c = $attributes; } ?>
<?php $component = App\View\Components\Datatable\Actions::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('datatable.actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Datatable\Actions::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                        <div class="select-status mr-3 pl-3">
                            <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                                <option value=""><?php echo app('translator')->get('app.selectAction'); ?></option>
                                <option value="delete"><?php echo app('translator')->get('app.delete'); ?></option>
                            </select>
                        </div>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald48019b35c4ccf364e5d37d92848414c)): ?>
<?php $attributes = $__attributesOriginald48019b35c4ccf364e5d37d92848414c; ?>
<?php unset($__attributesOriginald48019b35c4ccf364e5d37d92848414c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald48019b35c4ccf364e5d37d92848414c)): ?>
<?php $component = $__componentOriginald48019b35c4ccf364e5d37d92848414c; ?>
<?php unset($__componentOriginald48019b35c4ccf364e5d37d92848414c); ?>
<?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <?php echo $dataTable->table(['class' => 'table table-hover border-0 w-100']); ?>

            </div>

        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <?php echo $__env->make('sections.datatable_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

    <script>
        $('#contracts-table').on('preXhr.dt', function(e, settings, data) {
            var dateRangePicker = $('#datatableRange').data('daterangepicker');
            var startDate = $('#datatableRange').val();

            if (startDate == '') {
                startDate = null;
                endDate = null;
            } else {
                startDate = dateRangePicker.startDate.format('<?php echo e(company()->moment_date_format); ?>');
                endDate = dateRangePicker.endDate.format('<?php echo e(company()->moment_date_format); ?>');
            }

            var projectID = $('#filter_project_id').val();
            if (!projectID) {
                projectID = 0;
            }
            var contract_type = $('#contract_type').val();
            var client = $('#client').val();
            var searchText = $('#search-text-field').val();
            data['startDate'] = startDate;
            data['endDate'] = endDate;
            data['contract_type'] = contract_type;
            data['client'] = client;
            data['searchText'] = searchText;

        });
        const showTable = () => {
            window.LaravelDataTables["contracts-table"].draw(false);
        }

        $('#client, #contract_type').on('change keyup', function() {
            if ($('#contract_type').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else if ($('#client').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            } else {
                $('#reset-filters').addClass('d-none');
                showTable();
            }
        });

        $('#search-text-field').on('keyup', function() {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });

        $('#quick-action-type').change(function() {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        });

        $('#quick-action-apply').click(function() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue == 'delete') {
                Swal.fire({
                    title: "<?php echo app('translator')->get('messages.sweetAlertTitle'); ?>",
                    text: "<?php echo app('translator')->get('messages.recoverRecord'); ?>",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "<?php echo app('translator')->get('messages.confirmDelete'); ?>",
                    cancelButtonText: "<?php echo app('translator')->get('app.cancel'); ?>",
                    customClass: {
                        confirmButton: 'btn btn-primary mr-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        applyQuickAction();
                    }
                });

            } else {
                applyQuickAction();
            }
        });

        $('body').on('click', '.delete-table-row', function() {
            var id = $(this).data('contract-id');
            Swal.fire({
                title: "<?php echo app('translator')->get('messages.sweetAlertTitle'); ?>",
                text: "<?php echo app('translator')->get('messages.recoverRecord'); ?>",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "<?php echo app('translator')->get('messages.confirmDelete'); ?>",
                cancelButtonText: "<?php echo app('translator')->get('app.cancel'); ?>",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "<?php echo e(route('contracts.destroy', ':id')); ?>";
                    url = url.replace(':id', id);

                    var token = "<?php echo e(csrf_token()); ?>";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                showTable();
                                majCompteurs(response.compteurs);
                            }
                        }
                    });
                }
            });
        });

        /* Met a jour les cartes de comptage sans recharger l'ecran.
           Le controleur renvoie les nouvelles valeurs avec la reponse de
           suppression ; sans cela le total restait fige jusqu'au prochain
           chargement de la page. */
        function majCompteurs(compteurs) {
            if (!compteurs) { return; }

            Object.keys(compteurs).forEach(function (cle) {
                const cible = document.querySelector('[data-kpi="' + cle + '"]');

                if (cible) { cible.textContent = compteurs[cle]; }
            });
        }

        const applyQuickAction = () => {
            var rowdIds = $("#contracts-table input:checkbox:checked").map(function() {
                return $(this).val();
            }).get();

            var url = "<?php echo e(route('contracts.apply_quick_action')); ?>?row_ids=" + rowdIds;

            $.easyAjax({
                url: url,
                container: '#quick-action-form',
                type: "POST",
                disableButton: true,
                buttonSelector: "#quick-action-apply",
                data: $('#quick-action-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        showTable();
                        resetActionButtons();
                        deSelectAll();
                        $('#quick-action-form').hide();
                    }
                }
            })
        };

        $(document).ready(function() {
            <?php if(!is_null(request('start')) && !is_null(request('end'))): ?>
                $('#datatableRange').val('<?php echo e(request('start')); ?>' +
                    ' <?php echo app('translator')->get('app.to'); ?> ' + '<?php echo e(request('end')); ?>');
                $('#datatableRange').data('daterangepicker').setStartDate("<?php echo e(request('start')); ?>");
                $('#datatableRange').data('daterangepicker').setEndDate("<?php echo e(request('end')); ?>");
                showTable();
            <?php endif; ?>
        });

        $('body').on('click', '.sign-modal', function() {
            var id = $(this).data('contract-id');
            url = "<?php echo e(route('companySignStore.sign', ':id')); ?>";
            url = url.replace(':id', id);
            console.log(url);
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/contracts/index.blade.php ENDPATH**/ ?>