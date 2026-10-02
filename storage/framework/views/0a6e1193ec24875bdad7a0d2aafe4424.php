<?php if($file->icon == 'images'): ?>
    <a class="img-lightbox" data-image-url="<?php echo e($file->file_url); ?>" href="javascript:;">
        <img src="<?php echo e($file->file_url); ?>">
    </a>
<?php else: ?>
    <a href="<?php echo e($file->file_url); ?>" target="_blank">
        <i class="fa <?php echo e($file->icon); ?> text-lightest"></i>
    </a>
<?php endif; ?>
<?php /**PATH C:\laragon\www\CRM\resources\views/components/file-view-thumbnail.blade.php ENDPATH**/ ?>