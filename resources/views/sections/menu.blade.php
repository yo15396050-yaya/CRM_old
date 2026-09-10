<ul>
    <li class="dck-sect" aria-hidden="true">Pilotage</li>
    <!-- NAV ITEM - DASHBOARD COLLAPSE MENU-->
    @if (in_array('admin', user_roles())
    || $sidebarUserPermissions['view_overview_dashboard'] == 4
    || $sidebarUserPermissions['view_project_dashboard'] == 4
    || $sidebarUserPermissions['view_client_dashboard'] == 4
    || $sidebarUserPermissions['view_hr_dashboard'] == 4
    || $sidebarUserPermissions['view_ticket_dashboard'] == 4
    || $sidebarUserPermissions['view_finance_dashboard'] == 4
    )
        <x-menu-item dck="grid" icon="house" :text="__('app.menu.dashboard')">
            <x-slot name="iconPath">
                <path fill-rule="evenodd"
                      d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                <path fill-rule="evenodd"
                      d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
            </x-slot>
            <div class="accordionItemContent">
                <x-sub-menu-item :link="route('dashboard')"
                                 :text="__('app.menu.privateDashboard')" />  
                <x-sub-menu-item :link="route('dashboard.advanced')"
                                 :text="__('app.menu.advanceDashboard')" />
            </div>
        </x-menu-item> 
    @else
        <x-menu-item dck="grid" icon="house" :text="__('app.menu.dashboard')" :link="route('dashboard')">
            <x-slot name="iconPath">
                <path fill-rule="evenodd"
                      d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                <path fill-rule="evenodd"
                      d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
            </x-slot>
        </x-menu-item>
    @endif

    {{-- [MODULE RETIRE] Notes
    <x-menu-item dck="file" icon="journal-text" :text="__('Notes')" :link="route('notes.index')">
        <x-slot name="iconPath">
            <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z" />
            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z" />
            <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z" />
        </x-slot>
    </x-menu-item>
    --}}

    <!-- MENU CLIENT -->
    @if (in_array('client', user_roles()))
    
        <x-menu-item dck="folder" class="message-menu" icon="chat-left-text" :link="route('projects.index')" :text="__('Diligences administratives')">
            <x-slot name="iconPath">
                <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z"/>
            </x-slot>
        </x-menu-item>
        {{-- [MODULE RETIRE] Espace client : Comptabilite et Gestion, Formation, Fiscalite, Autres services, Factures
        <x-menu-item dck="chart" class="message-menu" icon="chat-left-text" :link="(Route::has('projects.comptaGestion') ? route('projects.comptaGestion') : null)" :text="__('Comptabilité et Gestion')">
            <x-slot name="iconPath">
                <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1v4.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V9a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1.217L5.07 1.243a.5.5 0 0 1 .686-.172zM2 9v4.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9H2zM1 7v1h14V7H1zm3 3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 4 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 6 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z" />
            </x-slot>
        </x-menu-item>
        <x-menu-item dck="book" class="message-menu" icon="chat-left-text" :link="(Route::has('projects.formation') ? route('projects.formation') : null)" :text="__('Formation et information')">
            <x-slot name="iconPath">
                <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5z" />
            </x-slot>
        </x-menu-item>
        <x-menu-item dck="scale" class="message-menu" icon="chat-left-text" :link="(Route::has('projects.fiscalite') ? route('projects.fiscalite') : null)" :text="__('Fiscalité')">
            <x-slot name="iconPath">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z" />
            </x-slot>
        </x-menu-item>
        <x-menu-item dck="box" class="message-menu" icon="chat-left-text" :link="(Route::has('projects.autresServices') ? route('projects.autresServices') : null)" :text="__('Autres services')">
            <x-slot name="iconPath">
                <path d="M7.5 1.018a7 7 0 0 0-4.79 11.566L7.5 7.793V1.018zm1 0V7.5h6.482A7.001 7.001 0 0 0 8.5 1.018zM14.982 8.5H8.207l-4.79 4.79A7 7 0 0 0 14.982 8.5zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z" />
            </x-slot>
        </x-menu-item>
        <x-menu-item dck="file" class="message-menu" icon="chat-left-text" :link="(Route::has('invoices.index') ? route('invoices.index') : null)" :text="__('app.menu.invoices')">
            <x-slot name="iconPath">
                <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z" />
            </x-slot>
        </x-menu-item>
        --}}
    @endif

    <!-- MENU APPORTEURS -->
    @if(in_array('apporteurs', user_roles()))
    <x-menu-item dck="building" icon="building" :text="__('app.menu.clients')" :link="route('clients.index')">
        <x-slot name="iconPath">
            <path fill-rule="evenodd"
                d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022zM6 8.694 1 10.36V15h5V8.694zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5V15z" />
            <path
                d="M2 11h1v1H2v-1zm2 0h1v1H4v-1zm-2 2h1v1H2v-1zm2 0h1v1H4v-1zm4-4h1v1H8V9zm2 0h1v1h-1V9zm-2 2h1v1H8v-1zm2 0h1v1h-1v-1zm2-2h1v1h-1V9zm0 2h1v1h-1v-1zM8 7h1v1H8V7zm2 0h1v1h-1V7zm2 0h1v1h-1V7zM8 5h1v1H8V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm0-2h1v1h-1V3z" />
        </x-slot>
    </x-menu-item>
    <x-menu-item dck="file" icon="briefcase" :text="__('app.menu.contracts')" :link="route('contracts.index')">
        <x-slot name="iconPath">
            <path
                d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z" />
        </x-slot>
    </x-menu-item>
    @endif

    @if (!in_array('client', user_roles()) && !in_array('apporteurs', user_roles()))

        {{-- ═══ TRAVAIL ═══════════════════════════════════════════
             Les trois modules du coeur de metier, a plat : l'ancien
             accordeon « Travail » ajoutait un clic avant chaque ecran
             reellement consulte. --}}
        <li class="dck-sect" aria-hidden="true">Travail</li>

        @if (in_array('tasks', user_modules()) && $sidebarUserPermissions['view_tasks'] != 5 && $sidebarUserPermissions['view_tasks'] != 'none')
            {{-- Vue par defaut du module Taches : le tableau kanban.
                 La vue liste reste accessible via son icone dans la barre d'outils. --}}
            <x-menu-item dck="task" icon="check2-square" :text="__('app.menu.tasks')"
                         :link="route('taskboards.index')" />
        @endif

        @if (in_array('projects', user_modules()) && $sidebarUserPermissions['view_projects'] != 5 && $sidebarUserPermissions['view_projects'] != 'none')
            <x-menu-item dck="folder" icon="folder" :text="__('app.menu.projects')"
                         :link="route('projects.index')" />
        @endif

        @if (in_array('employees', user_modules()) && $sidebarUserPermissions['view_employees'] != 5 && $sidebarUserPermissions['view_employees'] != 'none')
            <x-menu-item dck="users" icon="people" :text="__('app.menu.employees')"
                         :link="route('employees.index')" />
        @endif

        {{-- ═══ GESTION ═══════════════════════════════════════════
             Les contrats sont ranges avec les clients : ils portent la
             relation commerciale, pas la production quotidienne. --}}
        <li class="dck-sect" aria-hidden="true">Gestion</li>

        @if (in_array('contracts', user_modules()) && $sidebarUserPermissions['view_contract'] != 5 && $sidebarUserPermissions['view_contract'] != 'none')
            <x-menu-item dck="file" icon="file-earmark-text" :text="__('app.menu.contracts')"
                         :link="route('contracts.index')" />
        @endif

        @if (in_array('clients', user_modules()) && $sidebarUserPermissions['view_clients'] != 5 && $sidebarUserPermissions['view_clients'] != 'none')
            <x-menu-item dck="building" icon="building" :text="__('app.menu.clients')"
                         :link="route('clients.index')" />
        @endif

        {{-- ═══ ANALYSE ═══════════════════════════════════════════ --}}
        <li class="dck-sect" aria-hidden="true">Analyse</li>

        {{-- [MODULE RETIRE] Permissions et conges, Ma presence, Annonces,
             Services et produits, Situations fiscales.
             Les ecrans restent accessibles par leur URL ; seules les
             entrees de menu sont retirees. La section Gestion ne porte
             donc plus que les rapports.

        @if (Route::has('leaves.index') && in_array('leaves', user_modules()) && $sidebarUserPermissions['view_leave'] != 5 && $sidebarUserPermissions['view_leave'] != 'none')
            <x-menu-item dck="calendar" icon="calendar-event" :text="__('Permissions et congés')"
                         :link="(Route::has('leaves.index') ? route('leaves.index') : null)" />
        @endif

        @if (Route::has('attendances.index') && in_array('attendance', user_modules()) && $sidebarUserPermissions['view_attendance'] != 5 && $sidebarUserPermissions['view_attendance'] != 'none')
            <x-menu-item dck="clock" icon="clock" :text="__('Ma présence')"
                         :link="(Route::has('attendances.index') ? route('attendances.index') : null)" />
        @endif

        @if (Route::has('notices.index') && isset($sidebarUserPermissions['view_notice']) && $sidebarUserPermissions['view_notice'] != 5 && $sidebarUserPermissions['view_notice'] != 'none')
            <x-menu-item dck="mail" icon="clipboard" :text="__('Annonces')"
                         :link="(Route::has('notices.index') ? route('notices.index') : null)" />
        @endif

        @if (Route::has('products.index') && in_array('products', user_modules()) && $sidebarUserPermissions['view_product'] != 5 && $sidebarUserPermissions['view_product'] != 'none')
            <x-menu-item dck="box" icon="basket" :text="__('Services et produits')"
                         :link="(Route::has('products.index') ? route('products.index') : null)" />
        @endif

        @if (in_array('clients', user_modules()) && $sidebarUserPermissions['view_clients'] != 5 && $sidebarUserPermissions['view_clients'] != 'none')
            <x-menu-item dck="scale" icon="file-earmark-ruled" :text="__('Situations fiscales')"
                         :link="route('clients.fiscale')" />
        @endif
        --}}

        @if (in_array('reports', user_modules()) && in_array('tasks', user_modules())
             && isset($sidebarUserPermissions['view_task_report'])
             && in_array($sidebarUserPermissions['view_task_report'], [4, 2]))
            {{-- 4 = « all », 2 = « owned ».

                 Un droit complet ouvre le rapport detaille, qui montre
                 tout le cabinet. Un collaborateur va directement au
                 rapport hebdomadaire : c'est le seul des deux ecrans qui
                 sache se restreindre a ses propres taches. --}}
            <x-menu-item dck="chart" icon="graph-up" :text="__('app.menu.reports')"
                         :link="$sidebarUserPermissions['view_task_report'] == 4
                                ? route('task-report.index')
                                : route('task-report.hebdomadaire')" />
        @endif

    @endif
    <!-- NAV ITEM - CUSTOM LINK -->

    @php
    $role = user()->role->last();
    @endphp
    
    @foreach ($customLink as $item)
        @if((in_array($role->role_id, json_decode($item->can_be_viewed_by)) || in_array('admin', user_roles())) && $item->status == 'active')
            <li>
                <a class="nav-item text-lightest f-15 sidebar-text-color" href={{$item->url}} target="_blank"
                title={{$item->link_title}}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-link" viewBox="0 0 16 16">
                        <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/>
                        <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"/>
                    </svg>
                    <span class="pl-3">{{$item->link_title}}</span>
                </a>
            </li>
        @endif
    @endforeach
    <li class="dck-sect" aria-hidden="true">Système</li>
<!-- NAV ITEM - REPORTS COLLAPASE MENU -->
    <!-- NAV ITEM - SETTINGS -->
    <x-menu-item dck="cog" icon="gear" :text="__('app.menu.settings')"
                 :link="($sidebarUserPermissions['manage_company_setting'] == 4 ? route('company-settings.index') : route('profile-settings.index'))">
        <x-slot name="iconPath">
            <path
                d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
            <path
                d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
        </x-slot>
    </x-menu-item>
</ul>

{{-- Masque les sections du menu qui ne contiennent aucune entree visible
     pour le role courant. Aucune condition de permission n'est evaluee
     ici : on observe simplement ce que Blade a effectivement rendu. --}}
<script>
    (function () {
        function nettoyerSections() {
            var items = document.querySelectorAll('#sideMenuScroll ul > li');
            var section = null;
            var vide = true;

            items.forEach(function (li) {
                if (li.classList.contains('dck-sect')) {
                    if (section && vide) { section.style.display = 'none'; }
                    section = li;
                    vide = true;
                    return;
                }
                // une entree reellement affichee rend la section utile
                if (li.offsetParent !== null || li.getClientRects().length) { vide = false; }
            });

            if (section && vide) { section.style.display = 'none'; }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', nettoyerSections);
        } else {
            nettoyerSections();
        }
    })();
</script>
