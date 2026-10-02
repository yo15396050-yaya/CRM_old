<!-- SIDEBAR START -->
<aside class="dck-chrome <?php echo e(!user()->dark_theme ? 'sidebar-' . $appTheme->sidebar_theme : ''); ?>">
    <!-- MOBILE CLOSE SIDEBAR PANEL START -->
    <div class="mobile-close-sidebar-panel w-100 h-100" onclick="closeMobileMenu()" id="mobile_close_panel"></div>
    <!-- MOBILE CLOSE SIDEBAR PANEL END -->

    <!-- MAIN SIDEBAR START -->
    <div class="main-sidebar" id="mobile_menu_collapse">
        <!-- SIDEBAR BRAND START -->
        
        <div class="sidebar-brand-box <?php echo e(user()->dark_theme ? 'bg-dark' : ''); ?>">
            <div class="sidebar-brand d-flex align-items-center justify-content-between w-100">

                <?php if(companyOrGlobalSetting()->sidebar_logo_style !== 'full'): ?>
                    <!-- SIDEBAR BRAND NAME START -->
                    <div class="sidebar-brand-name">
                        <h1 class="mb-0 f-16 f-w-500 text-white-shade mt-0" data-placement="bottom" data-toggle="tooltip"
                            data-original-title="<?php echo e($appName); ?>"><?php echo e($appName); ?>

                        </h1>
                        <div class="mb-0 position-relative pro-name">
                            <span class="bg-light-green rounded-circle"></span>
                            <p class="f-13 text-lightest mb-0" data-placement="bottom" data-toggle="tooltip"
                                data-original-title="<?php echo e(user()->name); ?>"><?php echo e(user()->name); ?></p>
                        </div>
                    </div>
                    <!-- SIDEBAR BRAND NAME END -->
                    <!-- SIDEBAR BRAND LOGO START -->
                    <div class="sidebar-brand-logo">
                        <img src="<?php echo e(companyOrGlobalSetting()->logo_url); ?>">
                    </div>
                    <!-- SIDEBAR BRAND LOGO END -->
                <?php else: ?>
                    <!-- SIDEBAR BRAND NAME START -->
                    <div class="sidebar-brand-name">
                        <h1 class="mb-0 f-16 f-w-500 text-white-shade mt-0" data-placement="bottom"
                            data-toggle="tooltip" data-original-title="<?php echo e($appName); ?>">
                            <img src="<?php echo e(companyOrGlobalSetting()->logo_url); ?>">
                        </h1>
                    </div>
                    <!-- SIDEBAR BRAND NAME END -->

                    
                    <div class="dck-marque">
                        <img src="<?php echo e(companyOrGlobalSetting()->favicon_url); ?>"
                             alt="<?php echo e($appName); ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- SIDEBAR BRAND END -->

        <!-- SIDEBAR MENU START -->
        <div class="sidebar-menu <?php echo e(user()->dark_theme ? 'bg-dark' : ''); ?>" id="sideMenuScroll">
            <?php echo $__env->make('sections.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <!-- SIDEBAR MENU END -->

        
        <div class="dck-foot dropup">
            
            
            <button type="button" class="dck-user" id="dropdownMenuLink"
                data-toggle="dropdown" data-display="static"
                aria-haspopup="true" aria-expanded="false"
                aria-label="<?php echo e(__('Menu du compte')); ?>">
                <?php
                    $initiales = collect(preg_split('/\s+/', trim(user()->name)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
                        ->implode('');
                ?>
                <span class="dck-user__av"><?php echo e($initiales ?: '?'); ?></span>
                <span class="dck-user__txt">
                    <b><?php echo e(user()->name); ?></b>
                    <small><?php echo e(user()->employeeDetail->designation->name ?? __('app.menu.profile')); ?></small>
                </span>
            </button>

            <div class="dropdown-menu sidebar-brand-dropdown"
                aria-labelledby="dropdownMenuLink" tabindex="0">
                

                <div class="dck-pop__lab"><?php echo app('translator')->get('Connecté en tant que'); ?></div>
                <div class="dck-pop__mail"><?php echo e(user()->email); ?></div>

                <div class="dck-pop__sep"></div>

                

                

                
                <label class="dck-pop__item" for="dark-theme-toggle">
                    <i class="bi bi-moon"></i><?php echo app('translator')->get('Changer de thème'); ?>
                </label>
                <input type="checkbox" class="dck-pop__bascule" id="dark-theme-toggle"
                    <?php if(user()->dark_theme): ?> checked <?php endif; ?>>

                <?php if(!in_array('client', user_roles()) && ($sidebarUserPermissions['add_employees'] == 4 || $sidebarUserPermissions['add_employees'] == 1) && in_array('employees', user_modules())): ?>
                    <button type="button" class="dck-pop__item invite-member">
                        <i class="bi bi-person-plus"></i><?php echo app('translator')->get('app.inviteMember'); ?> <?php echo e($companyName); ?>

                    </button>
                <?php endif; ?>

                <div class="dck-pop__sep"></div>

                <a class="dck-pop__item is-danger" href="<?php echo e(route('logout')); ?>"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i><?php echo app('translator')->get('Se déconnecter'); ?>
                </a>
            </div>
        </div>
        <!-- PIED DE LA BARRE END -->
    </div>
    <!-- MAIN SIDEBAR END -->
    <!-- Sidebar Toggler -->
    <div
        class="text-center d-flex justify-content-between align-items-center position-fixed sidebarTogglerBox <?php echo e(user()->dark_theme ? 'bg-dark' : ''); ?>">
        <button class="border-0 d-lg-block d-none text-lightest font-weight-bold" id="sidebarToggle"></button>

        <p class="mb-0 text-dark-grey px-1 py-0 rounded f-10">v<?php echo e(\Illuminate\Support\Facades\File::get('version.txt')); ?></p>
    </div>
    <!-- Sidebar Toggler -->
</aside>
<!-- SIDEBAR END -->

<script>
    $(document).ready(function() {

        $('.invite-member').click(function() {
            const url = "<?php echo e(route('employees.invite_member')); ?>";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#dark-theme-toggle').change(function() {
            const darkTheme = ($(this).is(':checked')) ? '1' : '0'

            $.easyAjax({
                type: 'POST',
                url: "<?php echo e(route('profile.dark_theme')); ?>",
                blockUI: true,
                data: {
                    '_token': '<?php echo e(csrf_token()); ?>',
                    'darkTheme': darkTheme
                },
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    }
                }
            });

        });

    });
</script>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/sections/sidebar.blade.php ENDPATH**/ ?>