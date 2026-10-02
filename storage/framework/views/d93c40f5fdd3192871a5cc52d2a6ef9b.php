
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['titre', 'description' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['titre', 'description' => null]); ?>
<?php foreach (array_filter((['titre', 'description' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="dck-page-head">
    <div class="dck-page-head__txt">
        <h1><?php echo e($titre); ?></h1>
        <?php if($description): ?>
            <p><?php echo e($description); ?></p>
        <?php endif; ?>
    </div>

    <?php if(isset($actions)): ?>
        <div class="dck-page-head__acts"><?php echo e($actions); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/dck/page-head.blade.php ENDPATH**/ ?>