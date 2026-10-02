
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['titre', 'note' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['titre', 'note' => null]); ?>
<?php foreach (array_filter((['titre', 'note' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="col-sm-12">
    <div class="dck-form-section">
        <b><?php echo e($titre); ?></b>
        <?php if($note): ?>
            <span><?php echo e($note); ?></span>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/dck/form-section.blade.php ENDPATH**/ ?>