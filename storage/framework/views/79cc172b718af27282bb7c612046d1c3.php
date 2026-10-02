<?php if (isset($component)) { $__componentOriginald278722911781386ebf0ce0184b0f0fb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald278722911781386ebf0ce0184b0f0fb = $attributes; } ?>
<?php $component = App\View\Components\Auth::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('auth'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Auth::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <form id="login-form" action="<?php echo e(route('login')); ?>" class="ajax-form" method="POST">
        <?php echo e(csrf_field()); ?>

        <div class="auth-head">
            <h2>Bienvenue sur Taskflow</h2>
            <p>Connectez-vous pour commencer l'aventure.</p>
        </div>

        <?php if($socialAuthSettings->social_auth_enable): ?>
            <div class="social_login mb-3">
                <?php if($socialAuthSettings->google_status == 'enable'): ?>
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = google;">
                        <span><img src="<?php echo e(asset('img/google.png')); ?>" alt="Google"/></span><?php echo app('translator')->get('app.signInGoogle'); ?>
                    </a>
                <?php endif; ?>
                <?php if($socialAuthSettings->facebook_status == 'enable'): ?>
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = facebook;">
                        <span><img src="<?php echo e(asset('img/fb.png')); ?>" alt="Facebook"/></span><?php echo app('translator')->get('app.signInFacebook'); ?>
                    </a>
                <?php endif; ?>
                <?php if($socialAuthSettings->twitter_status == 'enable'): ?>
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = twitter;">
                        <span><img src="<?php echo e(asset('img/twitter.png')); ?>" alt="Twitter"/></span><?php echo app('translator')->get('app.signInTwitter'); ?>
                    </a>
                <?php endif; ?>
                <?php if($socialAuthSettings->linkedin_status == 'enable'): ?>
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = linkedin;">
                        <span><img src="<?php echo e(asset('img/linkedin.png')); ?>" alt="LinkedIn"/></span><?php echo app('translator')->get('app.signInLinkedin'); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        
        <div class="auth-field">
            <label class="auth-field__label" for="email"><?php echo app('translator')->get('auth.email'); ?></label>
            <span class="auth-control">
                <span class="auth-control__icon"><i class="far fa-envelope"></i></span>
                <input tabindex="1" type="email" name="email" id="email" autofocus
                       value="<?php echo e(request()->old('email')); ?>"
                       placeholder="<?php echo app('translator')->get('auth.email'); ?>"
                       class="<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            </span>
            <?php if($errors->has('email')): ?>
                <p class="auth-error"><?php echo e($errors->first('email')); ?></p>
            <?php endif; ?>
            <?php if($socialAuthSettings->social_auth_enable_count > 1): ?>
                <p class="auth-hint" id="forget-pass-email-section">
                    <a class="auth-link" href="<?php echo e(url('forgot-password')); ?>"><?php echo app('translator')->get('app.forgotPassword'); ?></a>
                </p>
            <?php endif; ?>
        </div>

        <?php if($socialAuthSettings->social_auth_enable_count > 1 && !$errors->has('g-recaptcha-response')): ?>
            <button type="submit" id="submit-next" class="auth-submit">
                <?php echo app('translator')->get('auth.next'); ?> <i class="fa fa-arrow-right"></i>
            </button>

            <?php if($company->allow_client_signup): ?>
                <a href="<?php echo e(route('register')); ?>" id="signup-client-next" class="auth-secondary">
                    <?php echo app('translator')->get('app.signUpAsClient'); ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        
        <div id="password-section"
             <?php if($socialAuthSettings->social_auth_enable_count > 1 && !$errors->has('g-recaptcha-response')): ?> class="d-none" <?php endif; ?>>

            <div class="auth-field">
                <div class="auth-field__head">
                    <label class="auth-field__label" for="password"><?php echo app('translator')->get('app.password'); ?></label>
                    <a class="auth-link" href="<?php echo e(url('forgot-password')); ?>"><?php echo app('translator')->get('app.forgotPassword'); ?></a>
                </div>
                <span class="auth-control auth-control--with-action">
                    <span class="auth-control__icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" tabindex="3"
                           placeholder="<?php echo app('translator')->get('placeholders.password'); ?>"
                           class="<?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <button type="button" class="auth-control__action toggle-password"
                            data-toggle="tooltip" data-original-title="<?php echo app('translator')->get('app.viewPassword'); ?>"
                            aria-label="<?php echo app('translator')->get('app.viewPassword'); ?>">
                        <i class="far fa-eye"></i>
                    </button>
                </span>
                <?php if($errors->has('password')): ?>
                    <p class="auth-error"><?php echo e($errors->first('password')); ?></p>
                <?php endif; ?>
            </div>

            <div class="auth-check">
                <input id="checkbox-signup" type="checkbox" name="remember">
                <label for="checkbox-signup"><?php echo app('translator')->get('app.rememberMe'); ?></label>
            </div>

            <?php if($globalSetting->google_recaptcha_status == 'active'): ?>
                <div class="form-group" id="captcha_container"></div>
            <?php endif; ?>

            <input type="hidden" id="g_recaptcha" name="g_recaptcha">

            <?php if($errors->has('g-recaptcha-response')): ?>
                <p class="auth-error"><?php echo e($errors->first('g-recaptcha-response')); ?></p>
            <?php endif; ?>

            <button type="submit" id="submit-login" class="auth-submit">
                <?php echo app('translator')->get('app.login'); ?> <i class="fa fa-arrow-right"></i>
            </button>

            <?php if($company->allow_client_signup): ?>
                <p class="auth-foot">
                    Vous n'avez pas de compte ?
                    <a href="<?php echo e(route('register')); ?>"><?php echo app('translator')->get('app.signUpAsClient'); ?></a>
                </p>
            <?php endif; ?>
        </div>

        <input type="hidden" name="locale" value="<?php echo e(session()->has('locale') ? session('locale') : global_setting()->locale); ?>">
    </form>

     <?php $__env->slot('scripts', null, []); ?> 


        <?php if($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v2_status == 'active'): ?>
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async
                    defer></script>
            <script>
                var gcv3;
                var onloadCallback = function () {
                    // Renders the HTML element with id 'captcha_container' as a reCAPTCHA widget.
                    // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                    gcv3 = grecaptcha.render('captcha_container', {
                        'sitekey': '<?php echo e($globalSetting->google_recaptcha_v2_site_key); ?>',
                        'theme': 'light',
                        'callback': function (response) {
                            if (response) {
                                $('#g_recaptcha').val(response);
                            }
                        },
                    });
                };
            </script>
        <?php endif; ?>
        <?php if($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v3_status == 'active'): ?>
            <script
                src="https://www.google.com/recaptcha/api.js?render=<?php echo e($globalSetting->google_recaptcha_v3_site_key); ?>"></script>
            <script>
                grecaptcha.ready(function () {
                    grecaptcha.execute('<?php echo e($globalSetting->google_recaptcha_v3_site_key); ?>').then(function (token) {
                        // Add your logic to submit to your backend server here.
                        $('#g_recaptcha').val(token);
                    });
                });
            </script>
        <?php endif; ?>

        <script>

            $(document).ready(function () {

                $("form#login-form").submit(function () {
                    const button = $('form#login-form').find('#submit-login');

                    const text = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo e(__('app.loading')); ?>';

                    button.prop("disabled", true);
                    button.html(text);
                });

                function handleFormSubmit(e) {
                    e.preventDefault();
                }

                $('#submit-next').click(function (event) {
                    event.preventDefault();
                    document.addEventListener('click', handleFormSubmit, false);

                    const url = "<?php echo e(route('check_email')); ?>";
                    $.easyAjax({
                        url: url,
                        container: '#login-form',
                        disableButton: true,
                        buttonSelector: "#submit-next",
                        type: "POST",
                        data: $('#login-form').serialize(),
                        success: function (response) {
                            if (response.status === 'success') {
                                $('#submit-next, #signup-client-next').remove();
                                $('#password-section').removeClass('d-none');
                                $('#forget-pass-email-section').remove();
                                $("#password").focus();
                                document.removeEventListener('click', handleFormSubmit);
                            }
                        }
                    })
                });

                <?php if(session('message')): ?>
                Swal.fire({
                    icon: 'error',
                    text: '<?php echo e(session('message')); ?>',
                    showConfirmButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                })
                <?php endif; ?>

            });
        </script>
     <?php $__env->endSlot(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald278722911781386ebf0ce0184b0f0fb)): ?>
<?php $attributes = $__attributesOriginald278722911781386ebf0ce0184b0f0fb; ?>
<?php unset($__attributesOriginald278722911781386ebf0ce0184b0f0fb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald278722911781386ebf0ce0184b0f0fb)): ?>
<?php $component = $__componentOriginald278722911781386ebf0ce0184b0f0fb; ?>
<?php unset($__componentOriginald278722911781386ebf0ce0184b0f0fb); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\CRM\resources\views/auth/login.blade.php ENDPATH**/ ?>