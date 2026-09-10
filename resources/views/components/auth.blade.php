<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $globalSetting->favicon_url }}">
    <link rel="manifest" href="{{ $globalSetting->favicon_url }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ $globalSetting->favicon_url }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}" defer="defer">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Template CSS -->
    <link href="{{ asset('vendor/froiden-helper/helper.css') }}" rel="stylesheet" defer="defer">
    <link type="text/css" rel="stylesheet" media="all" href="{{ asset('css/main.css') }}">

    <title>{{ $globalSetting->global_app_name }}</title>


    @stack('styles')
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

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
    @include('sections.theme_css')
    @if(file_exists(public_path().'/css/login-custom.css'))
        <link href="{{ asset('css/login-custom.css') }}" rel="stylesheet">
    @endif

    @if ($globalSetting->sidebar_logo_style == 'full')
        <style>
            .login_header img {
                max-width: unset;
            }
        </style>
    @endif

</head>

<body
    class="{{ $globalSetting->auth_theme == 'dark' ? 'dark-theme' : '' }} {{ isRtl() ? (session('changedRtl') === false ? '' : 'rtl') : (session('changedRtl') == true ? 'rtl' : '') }}">

<div class="auth-split">

    {{-- Volet gauche : visuel et accroche. Masque sous 1024px. --}}
    <div class="auth-visual">
        @if ($globalSetting->login_background_url)
            {{-- Le WebP pese environ la moitie du JPEG a rendu identique. Le
                 <img> reste la source de verite : si le WebP est absent du
                 serveur, le navigateur retombe sur le JPEG sans rien casser.
                 Le nom de fichier vient de la base, seule l'extension change. --}}
            {{-- display:contents : .auth-visual est un conteneur flex, et
                 <picture> y deviendrait une boite supplementaire. Ainsi il
                 n'en cree aucune, la mise en page reste celle d'avant. --}}
            <picture style="display: contents;">
                <source type="image/webp"
                        srcset="{{ preg_replace('/\.(jpe?g|png)$/i', '.webp', $globalSetting->login_background_url) }}">
                <img class="auth-visual__img" src="{{ $globalSetting->login_background_url }}"
                     alt="" width="2560" height="1709" decoding="async">
            </picture>
        @endif
        <div class="auth-visual__veil"></div>

        <div class="auth-visual__body">
            <h1>Optimisez votre flux de travail quotidien.</h1>
            <p>Rejoignez les professionnels qui gèrent leurs projets et leurs tâches avec DC-KNOWING.</p>
        </div>
    </div>

    {{-- Volet droit : formulaire --}}
    <div class="auth-panel">
        <div class="auth-box">

            <div class="auth-brand">
                @if ($globalSetting->logo_url)
                    <img class="auth-brand__logo mb-2" src="{{ $globalSetting->logo_url }}"
                         alt="{{ $globalSetting->global_app_name ?? $globalSetting->app_name }}">
                @else
                    <div class="auth-brand__name">DC-<span>KNOWING</span></div>
                @endif
                <div class="auth-brand__rule"></div>
            </div>

            {{ $slot }}

            {{ $outsideLoginBox ?? '' }}

            @if ($languages->count() > 1)
                <div class="auth-langs">
                    @foreach ($languages->take(4) as $language)
                        <a href="javascript:;" class="text-dark-grey change-lang d-inline-flex align-items-center"
                           data-lang="{{ $language->language_code }}">
                            <span class="mr-2 flag-icon flag-icon-{{ $language->flag_code === 'en' ? 'gb' : $language->flag_code }} flag-icon-squared"></span>
                            {{ \App\Models\LanguageSetting::LANGUAGES_TRANS[$language->language_code] ?? $language->language_name }}
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
<!-- Global Required Javascript -->
{{--<script src="{{ asset('vendor/bootstrap/javascript/bootstrap-native.js') }}" defer="defer"></script>--}}

<!-- Font Awesome -->
<script src="{{ asset('vendor/jquery/all.min.js') }}" defer="defer"></script>

<!-- Template JS -->
<script src="{{ asset('js/main.js') }}?v={{ @filemtime(public_path('js/main.js')) }}"></script>
<script>
    document.loading = '@lang('app.loading')';
    const MODAL_DEFAULT = '#myModalDefault';
    const MODAL_LG = '#myModal';
    const MODAL_XL = '#myModalXl';
    const MODAL_HEADING = '#modelHeading';
    const RIGHT_MODAL = '#task-detail-1';
    const RIGHT_MODAL_CONTENT = '#right-modal-content';
    const RIGHT_MODAL_TITLE = '#right-modal-title';

    const dropifyMessages = {
        default: "@lang('app.dragDrop')",
        replace: "@lang('app.dragDropReplace')",
        remove: "@lang('app.remove')",
        error: "@lang('messages.errorOccured')",
    };
    $('.change-lang').click(function (event) {
        const locale = $(this).data("lang");
        event.preventDefault();
        let url = "{{ route('front.changeLang', ':locale') }}";
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

{{ $scripts }}

</body>

</html>
