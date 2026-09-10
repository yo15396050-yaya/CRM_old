<!-- SIDEBAR START -->
<aside class="dck-chrome {{ !user()->dark_theme ? 'sidebar-' . $appTheme->sidebar_theme : '' }}">
    <!-- MOBILE CLOSE SIDEBAR PANEL START -->
    <div class="mobile-close-sidebar-panel w-100 h-100" onclick="closeMobileMenu()" id="mobile_close_panel"></div>
    <!-- MOBILE CLOSE SIDEBAR PANEL END -->

    <!-- MAIN SIDEBAR START -->
    <div class="main-sidebar" id="mobile_menu_collapse">
        <!-- SIDEBAR BRAND START -->
        {{-- Le bloc de marque ne porte plus le menu du profil : celui-ci est
             passe au pied de la barre, comme dans la maquette dck-flow-task. --}}
        <div class="sidebar-brand-box {{ user()->dark_theme ? 'bg-dark' : '' }}">
            <div class="sidebar-brand d-flex align-items-center justify-content-between w-100">

                @if (companyOrGlobalSetting()->sidebar_logo_style !== 'full')
                    <!-- SIDEBAR BRAND NAME START -->
                    <div class="sidebar-brand-name">
                        <h1 class="mb-0 f-16 f-w-500 text-white-shade mt-0" data-placement="bottom" data-toggle="tooltip"
                            data-original-title="{{ $appName }}">{{ $appName }}
                        </h1>
                        <div class="mb-0 position-relative pro-name">
                            <span class="bg-light-green rounded-circle"></span>
                            <p class="f-13 text-lightest mb-0" data-placement="bottom" data-toggle="tooltip"
                                data-original-title="{{ user()->name }}">{{ user()->name }}</p>
                        </div>
                    </div>
                    <!-- SIDEBAR BRAND NAME END -->
                    <!-- SIDEBAR BRAND LOGO START -->
                    <div class="sidebar-brand-logo">
                        <img src="{{ companyOrGlobalSetting()->logo_url }}">
                    </div>
                    <!-- SIDEBAR BRAND LOGO END -->
                @else
                    <!-- SIDEBAR BRAND NAME START -->
                    <div class="sidebar-brand-name">
                        <h1 class="mb-0 f-16 f-w-500 text-white-shade mt-0" data-placement="bottom"
                            data-toggle="tooltip" data-original-title="{{ $appName }}">
                            <img src="{{ companyOrGlobalSetting()->logo_url }}">
                        </h1>
                    </div>
                    <!-- SIDEBAR BRAND NAME END -->

                    {{-- Marque carree, visible seulement quand la barre est repliee.
                         Le repli ramene la barre a 60 px : le logo horizontal y
                         serait ecrase et son texte coupe. On reutilise le favicon,
                         qui est deja la version carree de la marque — inutile
                         d'ajouter un reglage pour la meme image. --}}
                    <div class="dck-marque">
                        <img src="{{ companyOrGlobalSetting()->favicon_url }}"
                             alt="{{ $appName }}">
                    </div>
                @endif
            </div>
        </div>
        <!-- SIDEBAR BRAND END -->

        <!-- SIDEBAR MENU START -->
        <div class="sidebar-menu {{ user()->dark_theme ? 'bg-dark' : '' }}" id="sideMenuScroll">
            @include('sections.menu')
        </div>
        <!-- SIDEBAR MENU END -->

        {{-- PIED DE LA BARRE — identite et menu du profil.
             La maquette ouvre ce menu depuis le bloc utilisateur, en bas :
             le menu remonte donc (dropup) au lieu de descendre. --}}
        <div class="dck-foot dropup">
            {{-- Un bouton, pas un lien : il n'y a pas de destination. Avec
                 href="javascript:;", le navigateur affichait cette valeur
                 dans sa barre d'etat au survol. --}}
            {{-- data-display="static" ecarte Popper. Sans cela il ecrit
                 top:0, left:0 et une transformation EN STYLE INLINE, qui
                 l'emportent sur la feuille : le menu se dessinait alors
                 par-dessus le nom au lieu de remonter. Bootstrap applique
                 dans ce mode le positionnement de .dropup, celui voulu. --}}
            <button type="button" class="dck-user" id="dropdownMenuLink"
                data-toggle="dropdown" data-display="static"
                aria-haspopup="true" aria-expanded="false"
                aria-label="{{ __('Menu du compte') }}">
                @php
                    $initiales = collect(preg_split('/\s+/', trim(user()->name)))
                        ->filter()
                        ->take(2)
                        ->map(fn ($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
                        ->implode('');
                @endphp
                <span class="dck-user__av">{{ $initiales ?: '?' }}</span>
                <span class="dck-user__txt">
                    <b>{{ user()->name }}</b>
                    <small>{{ user()->employeeDetail->designation->name ?? __('app.menu.profile') }}</small>
                </span>
            </button>

            <div class="dropdown-menu sidebar-brand-dropdown"
                aria-labelledby="dropdownMenuLink" tabindex="0">
                {{-- Disposition conforme au menu de profil de dck-flow-task :
                     un intitule de section, l'adresse du compte, puis les
                     actions a icone. L'entree « Prevalisualiser en tant que »
                     de la maquette n'est pas reprise : elle simule un
                     changement de role, fonction que l'application n'a pas. --}}

                <div class="dck-pop__lab">@lang('Connecté en tant que')</div>
                <div class="dck-pop__mail">{{ user()->email }}</div>

                <div class="dck-pop__sep"></div>

                {{-- [ENTREES RETIREES] Mon profil et Parametres : le module
                     Parametres porte deja « Parametres de profil » et « Les
                     parametres de securite ». Trois chemins menaient au meme
                     endroit.
                @if (!in_array('client', user_roles()))
                    <a class="dck-pop__item" href="{{ route('employees.show', user()->id) }}">
                        <i class="bi bi-person"></i>@lang('Mon profil')
                    </a>
                @endif
                --}}

                {{-- [ENTREE RETIREE] Parametres : la barre laterale porte deja
                     son propre acces, en bas du menu. Deux chemins vers le meme
                     ecran n'apportaient rien.
                <a class="dck-pop__item" href="{{ route('profile-settings.index') }}">
                    <i class="bi bi-gear"></i>@lang('Paramètres')
                </a>
                --}}

                {{-- Le libelle porte le for= de l'interrupteur, reste cache :
                     cliquer l'entree declenche donc le gestionnaire existant,
                     sans qu'une ligne de JavaScript soit modifiee. --}}
                <label class="dck-pop__item" for="dark-theme-toggle">
                    <i class="bi bi-moon"></i>@lang('Changer de thème')
                </label>
                <input type="checkbox" class="dck-pop__bascule" id="dark-theme-toggle"
                    @if (user()->dark_theme) checked @endif>

                @if (!in_array('client', user_roles()) && ($sidebarUserPermissions['add_employees'] == 4 || $sidebarUserPermissions['add_employees'] == 1) && in_array('employees', user_modules()))
                    <button type="button" class="dck-pop__item invite-member">
                        <i class="bi bi-person-plus"></i>@lang('app.inviteMember') {{ $companyName }}
                    </button>
                @endif

                <div class="dck-pop__sep"></div>

                <a class="dck-pop__item is-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>@lang('Se déconnecter')
                </a>
            </div>
        </div>
        <!-- PIED DE LA BARRE END -->
    </div>
    <!-- MAIN SIDEBAR END -->
    <!-- Sidebar Toggler -->
    <div
        class="text-center d-flex justify-content-between align-items-center position-fixed sidebarTogglerBox {{ user()->dark_theme ? 'bg-dark' : '' }}">
        <button class="border-0 d-lg-block d-none text-lightest font-weight-bold" id="sidebarToggle"></button>

        <p class="mb-0 text-dark-grey px-1 py-0 rounded f-10">v{{ \Illuminate\Support\Facades\File::get('version.txt') }}</p>
    </div>
    <!-- Sidebar Toggler -->
</aside>
<!-- SIDEBAR END -->

<script>
    $(document).ready(function() {

        $('.invite-member').click(function() {
            const url = "{{ route('employees.invite_member') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });

        $('#dark-theme-toggle').change(function() {
            const darkTheme = ($(this).is(':checked')) ? '1' : '0'

            $.easyAjax({
                type: 'POST',
                url: "{{ route('profile.dark_theme') }}",
                blockUI: true,
                data: {
                    '_token': '{{ csrf_token() }}',
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
