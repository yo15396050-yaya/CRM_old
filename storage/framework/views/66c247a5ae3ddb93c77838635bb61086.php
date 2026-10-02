<!-- HEADER START -->
<header class="main-header clearfix bg-white dck-chrome" id="header">


    <!-- NAVBAR LEFT(MOBILE MENU COLLAPSE) START-->
    <div class="navbar-left float-left d-flex align-items-center">
        <?php if (isset($component)) { $__componentOriginal000fcba1c6c5a1e4a2897e45738ec35e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal000fcba1c6c5a1e4a2897e45738ec35e = $attributes; } ?>
<?php $component = App\View\Components\AppTitle::resolve(['pageTitle' => $pageTitle] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppTitle::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'd-none d-lg-flex']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal000fcba1c6c5a1e4a2897e45738ec35e)): ?>
<?php $attributes = $__attributesOriginal000fcba1c6c5a1e4a2897e45738ec35e; ?>
<?php unset($__attributesOriginal000fcba1c6c5a1e4a2897e45738ec35e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal000fcba1c6c5a1e4a2897e45738ec35e)): ?>
<?php $component = $__componentOriginal000fcba1c6c5a1e4a2897e45738ec35e; ?>
<?php unset($__componentOriginal000fcba1c6c5a1e4a2897e45738ec35e); ?>
<?php endif; ?>

        <div class="d-block d-lg-none menu-collapse cursor-pointer position-relative" onclick="openMobileMenu()">
            <div class="mc-wrap">
                <div class="mcw-line"></div>
                <div class="mcw-line center"></div>
                <div class="mcw-line"></div>
            </div>
        </div>

        <?php if(in_array('admin', user_roles()) && $checkListCompleted < $checkListTotal && App::environment('codecanyon')): ?>
            <div class="ml-3 d-none d-lg-block d-md-block">
                <span class="f-12 mb-1"><a href="<?php echo e(route('checklist')); ?>" class="text-lightest ">
                        <?php echo app('translator')->get('modules.accountSettings.setupProgress'); ?></a>
                    <span class="float-right"><?php echo e($checkListCompleted); ?>/<?php echo e($checkListTotal); ?></span>
                </span>
                <div class="progress" style="height: 5px; width: 150px">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: <?php echo e(($checkListCompleted / $checkListTotal) * 100); ?>%;" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100">&nbsp;
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- NAVBAR LEFT(MOBILE MENU COLLAPSE) END-->
    <!-- NAVBAR RIGHT(SEARCH, ADD, NOTIFICATION, LOGOUT) START-->
    <div class="page-header-right float-right d-flex align-items-center justify-content-end">

        <span id="timer-clock">
            <?php if(isset($selfActiveTimer)): ?>
                <?php echo $__env->make('sections.timer_clock', ['selfActiveTimer' => $selfActiveTimer], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </span>

        
        <form class="dck-search d-none d-md-flex" action="<?php echo e(route('search.resultats')); ?>" method="GET" role="search">
            <i class="bi bi-search dck-search__ico" aria-hidden="true"></i>
            <input type="search" name="q" id="dck-search-input"
                   class="dck-search__input" autocomplete="off"
                   value="<?php echo e(request()->routeIs('search.resultats') ? request('q') : ''); ?>"
                   placeholder="<?php echo e(__('Rechercher une tâche, un client, un contrat…')); ?>"
                   aria-label="<?php echo e(__('Rechercher')); ?>">
            <kbd class="dck-search__kbd">Ctrl K</kbd>
        </form>

        <ul>
            

        <?php if(!in_array('client', user_roles())): ?>

            

        <?php endif; ?>

            
            <li>
                <button type="button" class="header-icon-box dck-theme" id="dck-bascule-theme"
                        aria-label="<?php echo e(__('Changer de thème')); ?>"
                        title="<?php echo e(__('Changer de thème')); ?>">
                    <i class="bi <?php echo e(user()->dark_theme ? 'bi-sun' : 'bi-moon'); ?>"></i>
                </button>
            </li>

        <!-- NOTIFICATIONS START -->
            <li title="<?php echo e(__('Nouvelles notifications ')); ?>">
                <div class="notification_box dropdown">
                    <a class="d-block dropdown-toggle header-icon-box show-user-notifications" type="link"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell f-16 text-dark-grey"></i>
                        <?php if($unreadNotificationCount > 0): ?>
                            <span
                                class="badge badge-primary unread-notifications-count active-timer-count position-absolute"><?php echo e($unreadNotificationCount); ?></span>
                        <?php endif; ?>
                    </a>
                    <!-- DROPDOWN - INFORMATION -->
                    <div
                        class="dropdown-menu dropdown-menu-right notification-dropdown border-0 shadow-lg py-0 bg-additional-grey"
                        tabindex="0">
                        <div
                            class="d-flex px-3 justify-content-between align-items-center border-bottom-grey py-1 bg-white">
                            <div class="___class_+?50___">
                                <p class="f-14 mb-0 text-dark f-w-500"><?php echo app('translator')->get('Nouvelles notifications '); ?></p>
                            </div>
                            
                            <div class="f-12 ">
                                <?php if($unreadNotificationCount > 0): ?>
                                    <a href="javascript:;"
                                       class="text-dark-grey mark-notification-read"><?php echo app('translator')->get('app.markRead'); ?></a> |
                                <?php endif; ?>
                                <a href="<?php echo e(route('all-notifications')); ?>"
                                   class="text-dark-grey"><?php echo app('translator')->get('app.showAll'); ?></a>
                            </div>
                        </div>
                        <div id="notification-list">

                        </div>

                        <?php if($unreadNotificationCount > 6): ?>
                            <div class="d-flex px-3 pb-1 pt-2 justify-content-center bg-additional-grey">
                                <a href="<?php echo e(route('all-notifications')); ?>"
                                   class="text-darkest-grey f-13"><?php echo app('translator')->get('app.showAll'); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
            <!-- NOTIFICATIONS END -->
            

            <?php if(!in_array('client', user_roles())): ?>
                
                <li class="dck-creer-li">
                    <div class="dropdown">
                        <button type="button" class="dck-creer dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-plus-lg"></i>
                            <span><?php echo app('translator')->get('Créer'); ?></span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right dck-creer__menu" tabindex="0">
                            <div class="dck-creer__lab"><?php echo app('translator')->get('Créer un élément'); ?></div>

                            <?php if(in_array('tasks', user_modules()) && Route::has('tasks.create') && (add_tasks_permission() == 'all' || add_tasks_permission() == 'added')): ?>
                                <a class="dck-creer__item openRightModal" href="<?php echo e(route('tasks.create')); ?>">
                                    <i class="bi bi-check2-square"></i><?php echo app('translator')->get('Nouvelle tâche'); ?>
                                </a>
                            <?php endif; ?>

                            <?php if(in_array('projects', user_modules()) && Route::has('projects.create') && (add_project_permission() == 'all' || add_project_permission() == 'added')): ?>
                                <a class="dck-creer__item openRightModal" href="<?php echo e(route('projects.create')); ?>">
                                    <i class="bi bi-folder2-open"></i><?php echo app('translator')->get('Nouveau projet'); ?>
                                </a>
                            <?php endif; ?>

                            <?php if(in_array('contracts', user_modules()) && Route::has('contracts.create') && (user()->permission('add_contract') == 'all' || user()->permission('add_contract') == 'added')): ?>
                                <a class="dck-creer__item openRightModal" href="<?php echo e(route('contracts.create')); ?>">
                                    <i class="bi bi-file-earmark-text"></i><?php echo app('translator')->get('Nouveau contrat'); ?>
                                </a>
                            <?php endif; ?>

                            <?php if(in_array('clients', user_modules()) && Route::has('clients.create') && (add_clients_permission() == 'all' || add_clients_permission() == 'added')): ?>
                                <a class="dck-creer__item openRightModal" href="<?php echo e(route('clients.create')); ?>">
                                    <i class="bi bi-building"></i><?php echo app('translator')->get('Nouveau client'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <!-- NAVBAR RIGHT(SEARCH, ADD, NOTIFICATION, LOGOUT) START-->
</header>
<!-- HEADER END -->

<form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
</form>

<script>
    $(document).ready(function () {
        var runTimeClock = true;

        <?php if(isset($activeTimerCount)): ?>
        const activeTimerCount = parseInt("<?php echo e($activeTimerCount); ?>");

        if (activeTimerCount > 0) {

            $('#show-active-timer').click(function () {
                const url = "<?php echo e(route('timelogs.show_active_timer')); ?>";
                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_XL, url);
            });

        }
        <?php endif; ?>


        $('#start-timer-modal').click(function () {
            const url = "<?php echo e(route('timelogs.show_timer')); ?>";
            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        });

        /* L'ancienne loupe a ete retiree de l'en-tete ; le gestionnaire reste
           en place car la classe est encore posee sur d'autres ecrans. */
        $('.open-search').click(function () {
            const url = "<?php echo e(route('search.index')); ?>";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        /* La lune de l'en-tete actionne l'interrupteur cache de la barre
           laterale : c'est lui qui porte l'enregistrement du choix, on ne
           duplique donc pas l'appel reseau. */
        $('#dck-bascule-theme').on('click', function () {
            const interrupteur = document.getElementById('dark-theme-toggle');

            if (interrupteur) {
                interrupteur.checked = !interrupteur.checked;
                $(interrupteur).trigger('change');
            }
        });

        /* Ctrl+K amene le curseur dans le champ de recherche de l'en-tete. */
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                const champ = document.getElementById('dck-search-input');

                if (champ) {
                    e.preventDefault();
                    champ.focus();
                    champ.select();
                }
            }
        });



        $('.show-user-notifications').click(function () {
            const openStatus = $(this).attr('aria-expanded');

            if (typeof openStatus == "undefined" || openStatus == "false") {

                const token = '<?php echo e(csrf_token()); ?>';
                $.easyAjax({
                    type: 'POST',
                    url: "<?php echo e(route('show_notifications')); ?>",
                    container: "#notification-list",
                    blockUI: true,
                    data: {
                        '_token': token
                    },
                    success: function (data) {
                        if (data.status === 'success') {
                            $('#notification-list').html(data.html);
                        }
                    }
                });

            }

        });

        $('.mark-notification-read').click(function () {
            const token = '<?php echo e(csrf_token()); ?>';
            $.easyAjax({
                type: 'POST',
                url: "<?php echo e(route('mark_notification_read')); ?>",
                blockUI: true,
                data: {
                    '_token': token
                },
                success: function (data) {
                    if (data.status === 'success') {
                        $('#notification-list').html('');
                        $('.unread-notifications-count').remove();
                        window.location.reload();
                    }
                }
            });

        });

    });
</script>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/sections/topbar.blade.php ENDPATH**/ ?>