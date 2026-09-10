<!-- HEADER START -->
<header class="main-header clearfix bg-white dck-chrome" id="header">


    <!-- NAVBAR LEFT(MOBILE MENU COLLAPSE) START-->
    <div class="navbar-left float-left d-flex align-items-center">
        <x-app-title class="d-none d-lg-flex" :pageTitle="$pageTitle"></x-app-title>

        <div class="d-block d-lg-none menu-collapse cursor-pointer position-relative" onclick="openMobileMenu()">
            <div class="mc-wrap">
                <div class="mcw-line"></div>
                <div class="mcw-line center"></div>
                <div class="mcw-line"></div>
            </div>
        </div>

        @if (in_array('admin', user_roles()) && $checkListCompleted < $checkListTotal && App::environment('codecanyon'))
            <div class="ml-3 d-none d-lg-block d-md-block">
                <span class="f-12 mb-1"><a href="{{ route('checklist') }}" class="text-lightest ">
                        @lang('modules.accountSettings.setupProgress')</a>
                    <span class="float-right">{{ $checkListCompleted }}/{{ $checkListTotal }}</span>
                </span>
                <div class="progress" style="height: 5px; width: 150px">
                    <div class="progress-bar bg-primary" role="progressbar"
                         style="width: {{ ($checkListCompleted / $checkListTotal) * 100 }}%;" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100">&nbsp;
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- NAVBAR LEFT(MOBILE MENU COLLAPSE) END-->
    <!-- NAVBAR RIGHT(SEARCH, ADD, NOTIFICATION, LOGOUT) START-->
    <div class="page-header-right float-right d-flex align-items-center justify-content-end">

        <span id="timer-clock">
            @if(isset($selfActiveTimer))
                @include('sections.timer_clock', ['selfActiveTimer' => $selfActiveTimer])
            @endif
        </span>

        {{-- Recherche transversale : une saisie, trois modules interroges
             en une fois. Le raccourci Ctrl+K amene le curseur ici. --}}
        <form class="dck-search d-none d-md-flex" action="{{ route('search.resultats') }}" method="GET" role="search">
            <i class="bi bi-search dck-search__ico" aria-hidden="true"></i>
            <input type="search" name="q" id="dck-search-input"
                   class="dck-search__input" autocomplete="off"
                   value="{{ request()->routeIs('search.resultats') ? request('q') : '' }}"
                   placeholder="{{ __('Rechercher une tâche, un client, un contrat…') }}"
                   aria-label="{{ __('Rechercher') }}">
            <kbd class="dck-search__kbd">Ctrl K</kbd>
        </form>

        <ul>
            {{-- [ICONES RETIREES] Loupe et pense-betes.
                 La loupe ouvrait un panneau de recherche ; elle laisse la
                 place au champ de recherche visible en permanence, place a
                 gauche des actions. --}}

        @if (!in_array('client', user_roles()))

            {{-- [ICONE RETIREE] Minuteur. Le module de suivi du temps
                 continue de fonctionner : seul son raccourci disparait de
                 l'en-tete. Le minuteur en cours reste affiche par le bloc
                 #timer-clock, plus haut, quand une saisie est ouverte.
            @if (in_array('timelogs', user_modules()) && (add_timelogs_permission() == 'all' || add_timelogs_permission() == 'added' || manage_active_timelogs() == 'all'))
                    <li data-toggle="tooltip" data-placement="top" title="{{__('modules.timeLogs.startTimer')}}">
                        <div class="add_box dropdown">
                            <a class="d-block dropdown-toggle header-icon-box" type="link" id="show-active-timer"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-clock f-16 text-dark-grey"></i>
                                    <span
                                        class="badge badge-primary active-timer-count position-absolute {{ ($activeTimerCount == 0) ? 'd-none' : '' }}">{{ $activeTimerCount }}</span>
                            </a>
                        @if ($activeTimerCount == 0)
                                <div class="dropdown-menu dropdown-menu-right" id="active-timer-list"
                                     aria-labelledby="dropdownMenuLink" tabindex="0">
                                    <a class="dropdown-item text-primary f-w-500" href="javascript:;"
                                       id="start-timer-modal">
                                        <i class="fa fa-play mr-2"></i>
                                        @lang("modules.timeLogs.startTimer")
                                    </a>
                                </div>
                            @endif
                        </div>
                    </li>
            @endif
            --}}

        @endif

            {{-- Bascule du theme. Elle pilote l'interrupteur cache de la barre
                 laterale : le gestionnaire existant fait le reste, aucune
                 logique n'est dupliquee ici. --}}
            <li>
                <button type="button" class="header-icon-box dck-theme" id="dck-bascule-theme"
                        aria-label="{{ __('Changer de thème') }}"
                        title="{{ __('Changer de thème') }}">
                    <i class="bi {{ user()->dark_theme ? 'bi-sun' : 'bi-moon' }}"></i>
                </button>
            </li>

        <!-- NOTIFICATIONS START -->
            <li title="{{__('app.newNotifications')}}">
                <div class="notification_box dropdown">
                    <a class="d-block dropdown-toggle header-icon-box show-user-notifications" type="link"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell f-16 text-dark-grey"></i>
                        @if ($unreadNotificationCount > 0)
                            <span
                                class="badge badge-primary unread-notifications-count active-timer-count position-absolute">{{ $unreadNotificationCount }}</span>
                        @endif
                    </a>
                    <!-- DROPDOWN - INFORMATION -->
                    <div
                        class="dropdown-menu dropdown-menu-right notification-dropdown border-0 shadow-lg py-0 bg-additional-grey"
                        tabindex="0">
                        <div
                            class="d-flex px-3 justify-content-between align-items-center border-bottom-grey py-1 bg-white">
                            <div class="___class_+?50___">
                                <p class="f-14 mb-0 text-dark f-w-500">@lang('app.newNotifications')</p>
                            </div>
                            @if ($unreadNotificationCount > 0)
                                <div class="f-12 ">
                                    <a href="javascript:;"
                                       class="text-dark-grey mark-notification-read">@lang('app.markRead')</a> |
                                    <a href="{{ route('all-notifications') }}"
                                       class="text-dark-grey">@lang('app.showAll')</a>
                                </div>
                            @endif
                        </div>
                        <div id="notification-list">

                        </div>

                        @if($unreadNotificationCount > 6)
                            <div class="d-flex px-3 pb-1 pt-2 justify-content-center bg-additional-grey">
                                <a href="{{ route('all-notifications') }}"
                                   class="text-darkest-grey f-13">@lang('app.showAll')</a>
                            </div>
                        @endif
                    </div>
                </div>
            </li>
            <!-- NOTIFICATIONS END -->
            {{-- [ICONE RETIREE] Deconnexion : elle figure deja dans le menu
                 du compte, en bas de la barre laterale. Le formulaire de
                 deconnexion plus bas reste en place, c'est lui que ce menu
                 soumet. --}}

            @if (!in_array('client', user_roles()))
                {{-- Bouton de creation. Il ne propose que les quatre objets
                     que le cabinet cree au quotidien ; les modules ecartes
                     du menu lateral n'ont pas a reapparaitre ici. --}}
                <li class="dck-creer-li">
                    <div class="dropdown">
                        <button type="button" class="dck-creer dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-plus-lg"></i>
                            <span>@lang('Créer')</span>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right dck-creer__menu" tabindex="0">
                            <div class="dck-creer__lab">@lang('Créer un élément')</div>

                            @if (in_array('tasks', user_modules()) && Route::has('tasks.create') && (add_tasks_permission() == 'all' || add_tasks_permission() == 'added'))
                                <a class="dck-creer__item openRightModal" href="{{ route('tasks.create') }}">
                                    <i class="bi bi-check2-square"></i>@lang('Nouvelle tâche')
                                </a>
                            @endif

                            @if (in_array('projects', user_modules()) && Route::has('projects.create') && (add_project_permission() == 'all' || add_project_permission() == 'added'))
                                <a class="dck-creer__item openRightModal" href="{{ route('projects.create') }}">
                                    <i class="bi bi-folder2-open"></i>@lang('Nouvelle diligence')
                                </a>
                            @endif

                            @if (in_array('contracts', user_modules()) && Route::has('contracts.create') && (user()->permission('add_contract') == 'all' || user()->permission('add_contract') == 'added'))
                                <a class="dck-creer__item openRightModal" href="{{ route('contracts.create') }}">
                                    <i class="bi bi-file-earmark-text"></i>@lang('Nouveau contrat')
                                </a>
                            @endif

                            @if (in_array('clients', user_modules()) && Route::has('clients.create') && (add_clients_permission() == 'all' || add_clients_permission() == 'added'))
                                <a class="dck-creer__item openRightModal" href="{{ route('clients.create') }}">
                                    <i class="bi bi-building"></i>@lang('Nouveau client')
                                </a>
                            @endif
                        </div>
                    </div>
                </li>
            @endif
        </ul>
    </div>
    <!-- NAVBAR RIGHT(SEARCH, ADD, NOTIFICATION, LOGOUT) START-->
</header>
<!-- HEADER END -->

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    $(document).ready(function () {
        var runTimeClock = true;

        @if(isset($activeTimerCount))
        const activeTimerCount = parseInt("{{ $activeTimerCount }}");

        if (activeTimerCount > 0) {

            $('#show-active-timer').click(function () {
                const url = "{{ route('timelogs.show_active_timer') }}";
                $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_XL, url);
            });

        }
        @endif


        $('#start-timer-modal').click(function () {
            const url = "{{ route('timelogs.show_timer') }}";
            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        });

        /* L'ancienne loupe a ete retiree de l'en-tete ; le gestionnaire reste
           en place car la classe est encore posee sur d'autres ecrans. */
        $('.open-search').click(function () {
            const url = "{{ route('search.index') }}";
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

                const token = '{{ csrf_token() }}';
                $.easyAjax({
                    type: 'POST',
                    url: "{{ route('show_notifications') }}",
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
            const token = '{{ csrf_token() }}';
            $.easyAjax({
                type: 'POST',
                url: "{{ route('mark_notification_read') }}",
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
