<x-auth>
    <form id="login-form" action="{{ route('login') }}" class="ajax-form" method="POST">
        {{ csrf_field() }}
        <div class="auth-head">
            <h2>Bienvenue sur Taskflow</h2>
            <p>Connectez-vous pour commencer l'aventure.</p>
        </div>

        @if ($socialAuthSettings->social_auth_enable)
            <div class="social_login mb-3">
                @if ($socialAuthSettings->google_status == 'enable')
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = google;">
                        <span><img src="{{ asset('img/google.png') }}" alt="Google"/></span>@lang('app.signInGoogle')
                    </a>
                @endif
                @if ($socialAuthSettings->facebook_status == 'enable')
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = facebook;">
                        <span><img src="{{ asset('img/fb.png') }}" alt="Facebook"/></span>@lang('app.signInFacebook')
                    </a>
                @endif
                @if ($socialAuthSettings->twitter_status == 'enable')
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = twitter;">
                        <span><img src="{{ asset('img/twitter.png') }}" alt="Twitter"/></span>@lang('app.signInTwitter')
                    </a>
                @endif
                @if ($socialAuthSettings->linkedin_status == 'enable')
                    <a class="mb-3 height_50 rounded f-w-500" onclick="window.location.href = linkedin;">
                        <span><img src="{{ asset('img/linkedin.png') }}" alt="LinkedIn"/></span>@lang('app.signInLinkedin')
                    </a>
                @endif
            </div>
        @endif

        {{-- Adresse e-mail --}}
        <div class="auth-field">
            <label class="auth-field__label" for="email">@lang('auth.email')</label>
            <span class="auth-control">
                <span class="auth-control__icon"><i class="far fa-envelope"></i></span>
                <input tabindex="1" type="email" name="email" id="email" autofocus
                       value="{{ request()->old('email') }}"
                       placeholder="@lang('auth.email')"
                       class="@error('email') is-invalid @enderror">
            </span>
            @if ($errors->has('email'))
                <p class="auth-error">{{ $errors->first('email') }}</p>
            @endif
            @if ($socialAuthSettings->social_auth_enable_count > 1)
                <p class="auth-hint" id="forget-pass-email-section">
                    <a class="auth-link" href="{{ url('forgot-password') }}">@lang('app.forgotPassword')</a>
                </p>
            @endif
        </div>

        @if ($socialAuthSettings->social_auth_enable_count > 1 && !$errors->has('g-recaptcha-response'))
            <button type="submit" id="submit-next" class="auth-submit">
                @lang('auth.next') <i class="fa fa-arrow-right"></i>
            </button>

            @if ($company->allow_client_signup)
                <a href="{{ route('register') }}" id="signup-client-next" class="auth-secondary">
                    @lang('app.signUpAsClient')
                </a>
            @endif
        @endif

        {{-- Mot de passe --}}
        <div id="password-section"
             @if ($socialAuthSettings->social_auth_enable_count > 1 && !$errors->has('g-recaptcha-response')) class="d-none" @endif>

            <div class="auth-field">
                <div class="auth-field__head">
                    <label class="auth-field__label" for="password">@lang('app.password')</label>
                    <a class="auth-link" href="{{ url('forgot-password') }}">@lang('app.forgotPassword')</a>
                </div>
                <span class="auth-control auth-control--with-action">
                    <span class="auth-control__icon"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="password" tabindex="3"
                           placeholder="@lang('placeholders.password')"
                           class="@error('password') is-invalid @enderror">
                    <button type="button" class="auth-control__action toggle-password"
                            data-toggle="tooltip" data-original-title="@lang('app.viewPassword')"
                            aria-label="@lang('app.viewPassword')">
                        <i class="far fa-eye"></i>
                    </button>
                </span>
                @if ($errors->has('password'))
                    <p class="auth-error">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <div class="auth-check">
                <input id="checkbox-signup" type="checkbox" name="remember">
                <label for="checkbox-signup">@lang('app.rememberMe')</label>
            </div>

            @if ($globalSetting->google_recaptcha_status == 'active')
                <div class="form-group" id="captcha_container"></div>
            @endif

            <input type="hidden" id="g_recaptcha" name="g_recaptcha">

            @if ($errors->has('g-recaptcha-response'))
                <p class="auth-error">{{ $errors->first('g-recaptcha-response') }}</p>
            @endif

            <button type="submit" id="submit-login" class="auth-submit">
                @lang('app.login') <i class="fa fa-arrow-right"></i>
            </button>

            @if ($company->allow_client_signup)
                <p class="auth-foot">
                    Vous n'avez pas de compte ?
                    <a href="{{ route('register') }}">@lang('app.signUpAsClient')</a>
                </p>
            @endif
        </div>

        <input type="hidden" name="locale" value="{{ session()->has('locale') ? session('locale') : global_setting()->locale }}">
    </form>

    <x-slot name="scripts">


        @if ($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v2_status == 'active')
            <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async
                    defer></script>
            <script>
                var gcv3;
                var onloadCallback = function () {
                    // Renders the HTML element with id 'captcha_container' as a reCAPTCHA widget.
                    // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                    gcv3 = grecaptcha.render('captcha_container', {
                        'sitekey': '{{ $globalSetting->google_recaptcha_v2_site_key }}',
                        'theme': 'light',
                        'callback': function (response) {
                            if (response) {
                                $('#g_recaptcha').val(response);
                            }
                        },
                    });
                };
            </script>
        @endif
        @if ($globalSetting->google_recaptcha_status == 'active' && $globalSetting->google_recaptcha_v3_status == 'active')
            <script
                src="https://www.google.com/recaptcha/api.js?render={{ $globalSetting->google_recaptcha_v3_site_key }}"></script>
            <script>
                grecaptcha.ready(function () {
                    grecaptcha.execute('{{ $globalSetting->google_recaptcha_v3_site_key }}').then(function (token) {
                        // Add your logic to submit to your backend server here.
                        $('#g_recaptcha').val(token);
                    });
                });
            </script>
        @endif

        <script>

            $(document).ready(function () {

                $("form#login-form").submit(function () {
                    const button = $('form#login-form').find('#submit-login');

                    const text = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{__('app.loading')}}';

                    button.prop("disabled", true);
                    button.html(text);
                });

                function handleFormSubmit(e) {
                    e.preventDefault();
                }

                $('#submit-next').click(function (event) {
                    event.preventDefault();
                    document.addEventListener('click', handleFormSubmit, false);

                    const url = "{{ route('check_email') }}";
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

                @if (session('message'))
                Swal.fire({
                    icon: 'error',
                    text: '{{ session('message') }}',
                    showConfirmButton: true,
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                })
                @endif

            });
        </script>
    </x-slot>

</x-auth>
