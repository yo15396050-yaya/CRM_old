<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e($globalSetting->favicon_url); ?>">
    <link rel="manifest" href="<?php echo e($globalSetting->favicon_url); ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?php echo e($globalSetting->favicon_url); ?>">
    <meta name="theme-color" content="#ffffff">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('vendor/css/all.min.css')); ?>" defer="defer">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Template CSS -->
    <link href="<?php echo e(asset('vendor/froiden-helper/helper.css')); ?>" rel="stylesheet" defer="defer">
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo e(asset('css/main.css')); ?>">

    <title><?php echo e($globalSetting->global_app_name); ?></title>


    <?php echo $__env->yieldPushContent('styles'); ?>
    <script src="<?php echo e(asset('vendor/jquery/jquery.min.js')); ?>"></script>

    <style defer="defer">
        .login_header {
            background-color: #fff      !important;
        }

        /* Pages d'authentification : aucun debordement lateral possible. */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Centrage sans troncature : si le formulaire depasse la hauteur de
           l'ecran, les marges auto le laissent defiler normalement. */
        .login_section > .container {
            margin: auto;
            width: 100%;
        }

    </style>
    <?php echo $__env->make('sections.theme_css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if(file_exists(public_path().'/css/login-custom.css')): ?>
        <link href="<?php echo e(asset('css/login-custom.css')); ?>" rel="stylesheet">
    <?php endif; ?>

    <?php if($globalSetting->sidebar_logo_style == 'full'): ?>
        <style>
            .login_header img {
                max-width: unset;
            }
        </style>
    <?php endif; ?>

</head>

<body
    class="<?php echo e($globalSetting->auth_theme == 'dark' ? 'dark-theme' : ''); ?> <?php echo e(isRtl() ? (session('changedRtl') === false ? '' : 'rtl') : (session('changedRtl') == true ? 'rtl' : '')); ?>">

<div class="auth-split">

    
    <div class="auth-visual">
        <?php if($globalSetting->login_background_url): ?>
            
            
            <picture style="display: contents;">
                <source type="image/webp"
                        srcset="<?php echo e(preg_replace('/\.(jpe?g|png)$/i', '.webp', $globalSetting->login_background_url)); ?>">
                <img class="auth-visual__img" src="<?php echo e($globalSetting->login_background_url); ?>"
                     alt="" width="2560" height="1709" decoding="async">
            </picture>
        <?php endif; ?>
        <div class="auth-visual__veil"></div>

        <div class="auth-visual__body">
            <h1>Optimisez votre flux de travail quotidien.</h1>
            <p>Rejoignez les professionnels qui gèrent leurs projets et leurs tâches avec DC-KNOWING.</p>
        </div>
    </div>

    
    <div class="auth-panel">
        <div class="auth-box">

            <div class="auth-brand">
                <?php if($globalSetting->logo_url): ?>
                    <img class="auth-brand__logo mb-2" src="<?php echo e($globalSetting->logo_url); ?>"
                         alt="<?php echo e($globalSetting->global_app_name ?? $globalSetting->app_name); ?>">
                <?php else: ?>
                    <div class="auth-brand__name">DC-<span>KNOWING</span></div>
                <?php endif; ?>
                <div class="auth-brand__rule"></div>
            </div>

            <?php echo e($slot); ?>


            <?php echo e($outsideLoginBox ?? ''); ?>


            <?php if($languages->count() > 1): ?>
                <div class="auth-langs">
                    <?php $__currentLoopData = $languages->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="javascript:;" class="text-dark-grey change-lang d-inline-flex align-items-center"
                           data-lang="<?php echo e($language->language_code); ?>">
                            <span class="mr-2 flag-icon flag-icon-<?php echo e($language->flag_code === 'en' ? 'gb' : $language->flag_code); ?> flag-icon-squared"></span>
                            <?php echo e(\App\Models\LanguageSetting::LANGUAGES_TRANS[$language->language_code] ?? $language->language_name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<!-- Global Required Javascript -->


<!-- Font Awesome -->
<script src="<?php echo e(asset('vendor/jquery/all.min.js')); ?>" defer="defer"></script>

<!-- Template JS -->
<script src="<?php echo e(asset('js/main.js')); ?>?v=<?php echo e(@filemtime(public_path('js/main.js'))); ?>"></script>
<script>
    document.loading = '<?php echo app('translator')->get('app.loading'); ?>';
    const MODAL_DEFAULT = '#myModalDefault';
    const MODAL_LG = '#myModal';
    const MODAL_XL = '#myModalXl';
    const MODAL_HEADING = '#modelHeading';
    const RIGHT_MODAL = '#task-detail-1';
    const RIGHT_MODAL_CONTENT = '#right-modal-content';
    const RIGHT_MODAL_TITLE = '#right-modal-title';

    const dropifyMessages = {
        default: "<?php echo app('translator')->get('app.dragDrop'); ?>",
        replace: "<?php echo app('translator')->get('app.dragDropReplace'); ?>",
        remove: "<?php echo app('translator')->get('app.remove'); ?>",
        error: "<?php echo app('translator')->get('messages.errorOccured'); ?>",
    };
    $('.change-lang').click(function (event) {
        const locale = $(this).data("lang");
        event.preventDefault();
        let url = "<?php echo e(route('front.changeLang', ':locale')); ?>";
        url = url.replace(':locale', locale);
        $.easyAjax({
            url: url,
            container: '#login-form',
            blockUI: true,
            type: "GET",
            success: function (response) {
                if (response.status === 'success') {
                    window.location.reload();
                }
            }
        })
    });
</script>

<?php echo e($scripts); ?>


</body>

</html>
<?php /**PATH C:\laragon\www\CRM\resources\views/components/auth.blade.php ENDPATH**/ ?>