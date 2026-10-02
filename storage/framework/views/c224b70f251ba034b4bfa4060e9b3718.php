
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'icone'   => 'bi-graph-up',
    'libelle' => '',
    'valeur'  => '0',
    'sous'    => null,
    'ton'     => null,
    'cle'     => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'icone'   => 'bi-graph-up',
    'libelle' => '',
    'valeur'  => '0',
    'sous'    => null,
    'ton'     => null,
    'cle'     => null,
]); ?>
<?php foreach (array_filter(([
    'icone'   => 'bi-graph-up',
    'libelle' => '',
    'valeur'  => '0',
    'sous'    => null,
    'ton'     => null,
    'cle'     => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="dck-kpi">
    <div class="dck-kpi__top">
        <span class="dck-kpi__ico <?php if($ton): ?> is-<?php echo e($ton); ?> <?php endif; ?>">
            <i class="bi <?php echo e($icone); ?>"></i>
        </span>
        <span class="dck-kpi__lab"><?php echo e($libelle); ?></span>
    </div>

    
    <div class="dck-kpi__val" <?php if($cle): ?> data-kpi="<?php echo e($cle); ?>" <?php endif; ?>><?php echo e($valeur); ?></div>

    <?php if($sous): ?>
        <div class="dck-kpi__sub"><?php echo e($sous); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/dck/kpi.blade.php ENDPATH**/ ?>