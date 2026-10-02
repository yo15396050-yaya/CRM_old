<?php
$content = "<div class='d-flex align-items-center text-left'>
    <div class='taskEmployeeImg border-0 d-inline-block mr-1'>
        <img class='rounded-circle' loading='lazy' src='".$user->image_url."'>
    </div>
    <div>". htmlentities($user->userBadge());

        if (isset($additionalText) && !is_null($additionalText)) {
        $content .= "<div class='f-10 font-weight-light my-1'>".$additionalText."</div>";
        }

        $content.="</div>";

    if($agent){
        $content .= ' ['.$user->email.'] ';
    }

    if($pill){
        $content = "<span class='badge badge-pill badge-light border abc'>".$content."</span>";
    }

    if ($user->status == 'deactive') {
        $content .= "<span class='badge badge-pill badge-danger border align-center ml-2 px-2'>Inactive</span>";
    } 

    ?>

    <option <?php if($selected): echo 'selected'; endif; ?> 
        data-content="<?php echo $content; ?>" 
        data-email="<?php echo e($user->email); ?>" 
        data-mobile="<?php echo e($user->mobile); ?>" 
        value="<?php echo e($userID ?? $user->id); ?>">
        <?php echo e($user->name_salutation); ?>

    </option>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/user-option.blade.php ENDPATH**/ ?>