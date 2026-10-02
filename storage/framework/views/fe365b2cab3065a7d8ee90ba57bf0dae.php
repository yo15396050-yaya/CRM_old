
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['href', 'libelle', 'barre' => true]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['href', 'libelle', 'barre' => true]); ?>
<?php foreach (array_filter((['href', 'libelle', 'barre' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $lien = '<a href="' . e($href) . '" class="dck-back">'
        . '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
        . ' stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . '<path d="M19 12H5M12 19l-7-7 7-7" /></svg>'
        . e($libelle) . '</a>';
?>

<?php if($barre): ?>
    <div class="dck-detail-head dck-detail-head--seul dck-front"><?php echo $lien; ?></div>
<?php else: ?>
    <?php echo $lien; ?>

<?php endif; ?>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/dck/back.blade.php ENDPATH**/ ?>