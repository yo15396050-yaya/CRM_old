<div class="form-check">
    <input <?php echo e($attributes->merge(['class' => 'form-check-input'])); ?>

           type="checkbox"
           name="<?php echo e($fieldName); ?>"
           id="<?php echo e($fieldId); ?>"
           <?php if(isset($fieldValue)): ?>
               value="<?php echo e($fieldValue); ?>"
           <?php endif; ?>
           <?php if($checked): echo 'checked'; endif; ?>
           <?php if($fieldPermission): echo 'disabled'; endif; ?>
    >
    <?php if($fieldLabel != ''): ?>
        <label
            class="form-check-label form_custom_label text-dark-grey pl-2 mr-4 justify-content-start cursor-pointer checkmark-20 pt-1 text-wrap text-break"
            for="<?php echo e($fieldId); ?>">
            <?php echo e($fieldLabel); ?>

            <?php if(!is_null($popover)): ?>
                &nbsp;<i class="fa fa-question-circle" data-toggle="popover" data-placement="top" data-html="true"
                         data-content="<?php echo e($popover); ?>" data-trigger="hover"></i>
            <?php endif; ?>
        </label>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/forms/checkbox.blade.php ENDPATH**/ ?>