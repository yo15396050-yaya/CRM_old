


<?php $__env->startSection('content'); ?>


<div class="content-wrapper dck-front">
    <?php if (isset($component)) { $__componentOriginal4a3dfc3fa275b773c17cfd00b0236231 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4a3dfc3fa275b773c17cfd00b0236231 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dck.back','data' => ['href' => $backUrl ?? route('taskboards.index'),'libelle' => __('app.dckBack.tasks'),'barre' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dck.back'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($backUrl ?? route('taskboards.index')),'libelle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('app.dckBack.tasks')),'barre' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4a3dfc3fa275b773c17cfd00b0236231)): ?>
<?php $attributes = $__attributesOriginal4a3dfc3fa275b773c17cfd00b0236231; ?>
<?php unset($__attributesOriginal4a3dfc3fa275b773c17cfd00b0236231); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4a3dfc3fa275b773c17cfd00b0236231)): ?>
<?php $component = $__componentOriginal4a3dfc3fa275b773c17cfd00b0236231; ?>
<?php unset($__componentOriginal4a3dfc3fa275b773c17cfd00b0236231); ?>
<?php endif; ?>

    <?php echo $__env->make($view, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/tasks/create.blade.php ENDPATH**/ ?>