{{--
    « Tableau de bord » du cabinet — conforme a PAGES.dashboard de la
    maquette dck-flow-task : quatre indicateurs, la charge de travail
    sur six mois, la repartition des taches par colonne, les diligences
    a surveiller, l'activite recente, les taches en cours et le taux de
    cloture.

    Les valeurs viennent de PilotageCabinet et de $projectActivities,
    deja prepare par OverviewDashboard. Aucune requete ici.
--}}

@php
    $k = $cabIndicateurs;
    $enRetard = \App\Models\Task::withoutGlobalScopes()
        ->where('company_id', company()->id)->whereNull('deleted_at')
        ->whereNotNull('due_date')->whereDate('due_date', '<', now())
        ->where('board_column_id', '<>', \App\Models\TaskboardColumn::completeColumn()->id)
        ->count();
@endphp

<section class="dck-pilotage">

    {{-- ── Alerte ─────────────────────────────────────────────── --}}
    @if ($enRetard > 0)
        <div class="dck-note is-warn">
            <span class="dck-note__i"><i class="bi bi-exclamation-triangle"></i></span>
            <div>
                <b>{{ $enRetard }} {{ trans_choice('tâche a dépassé son échéance|tâches ont dépassé leur échéance', $enRetard) }}</b>
                <p>Ouvrez le tableau des tâches pour les réaffecter.</p>
            </div>
            <a class="dck-note__act" href="{{ route('taskboards.index') }}">Voir les tâches</a>
        </div>
    @endif

    {{-- ── Indicateurs ─────────────────────────────────────────
         Limites aux trois modules du coeur de metier : taches,
         diligences et contrats. --}}
    <div class="dck-grid dck-g4">
        <a class="dck-kpi2" href="{{ route('tasks.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-check2-square"></i></span>
                <span class="dck-kpi2__lab">@lang('Volume du mois')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->tachesOuvertes }} <span class="dck-kpi2__tot">/ {{ $k->tachesMois }}</span></div>
            <div class="dck-kpi2__sub">en cours sur le mois</div>
        </a>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="dck-kpi2__lab">@lang('Taux de clôture')</span>
            </div>
            <div class="dck-kpi2__val">{{ is_null($k->tauxTaches) ? '—' : $k->tauxTaches . ' %' }}</div>
            <div class="dck-kpi2__sub">{{ $k->tachesFinies }} tâches terminées</div>
        </div>

        <a class="dck-kpi2" href="{{ route('taskboards.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-inbox"></i></span>
                <span class="dck-kpi2__lab">@lang('Backlog')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->backlog }}</div>
            <div class="dck-kpi2__sub">en attente de prise en charge</div>
        </a>

        <a class="dck-kpi2" href="{{ route('employees.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-people"></i></span>
                <span class="dck-kpi2__lab">@lang('Collaborateurs')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->collaborateurs }}</div>
            <div class="dck-kpi2__sub">actifs dans le cabinet</div>
        </a>

        <a class="dck-kpi2" href="{{ route('clients.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-building"></i></span>
                <span class="dck-kpi2__lab">@lang('Clients')</span>
            </div>
            <div class="dck-kpi2__val">{{ $k->clients }}</div>
            <div class="dck-kpi2__sub">{{ $k->contrats }} contrats en vigueur</div>
        </a>

        {{-- Respect des echeances : la mesure la plus severe du tableau,
             et la seule qui dise si le cabinet livre en temps voulu. --}}
        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-calendar-check"></i></span>
                <span class="dck-kpi2__lab">@lang('Respect des échéances')</span>
            </div>
            <div class="dck-kpi2__val">{{ is_null($k->respect) ? '—' : $k->respect . ' %' }}</div>
            <div class="dck-kpi2__sub">
                {{ $k->respectATemps }} / {{ $k->respectTotal }} livrées à temps
            </div>
        </div>

        <div class="dck-kpi2">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-stopwatch"></i></span>
                <span class="dck-kpi2__lab">@lang('Délai moyen')</span>
            </div>
            <div class="dck-kpi2__val">
                {{ is_null($k->delaiMoyen) ? '—' : $k->delaiMoyen }}
                @if (!is_null($k->delaiMoyen))<span class="dck-kpi2__tot">jours</span>@endif
            </div>
            <div class="dck-kpi2__sub">de la création à la clôture</div>
        </div>

        <a class="dck-kpi2" href="{{ route('contracts.index') }}">
            <div class="dck-kpi2__top">
                <span class="dck-kpi2__ico"><i class="bi bi-cash-stack"></i></span>
                <span class="dck-kpi2__lab">@lang('Portefeuille')</span>
            </div>
            @php
                /* Au-dela du million, le montant complet ne tient plus dans
                   une carte a quatre colonnes : on abrege, comme la maquette
                   le fait pour la valeur cumulee. */
                $montant = $k->portefeuille >= 1000000
                    ? number_format($k->portefeuille / 1000000, 1, ',', ' ') . ' M'
                    : number_format($k->portefeuille, 0, ',', ' ');
            @endphp
            <div class="dck-kpi2__val">
                {{ $montant }}
                <span class="dck-kpi2__tot">FCFA</span>
            </div>
            <div class="dck-kpi2__sub">sur {{ $k->portefeuilleN }} contrats en cours</div>
        </a>
    </div>

    {{-- ── Charge de travail et repartition ───────────────────── --}}
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Charge de travail')</h3>
                    <div class="dck-c__sub">Tâches créées et terminées sur six mois</div>
                </div>
            </div>
            <div class="dck-c__body">
                @php
                    $lab = $cabCharge['labels'];
                    $s1 = $cabCharge['creees'];
                    $s2 = $cabCharge['terminees'];
                    $haut = max(1, max(max($s1), max($s2)));
                    $L = 560; $H = 200;
                    $mg = ['t' => 12, 'r' => 8, 'b' => 24, 'l' => 30];
                    $pl = $L - $mg['l'] - $mg['r'];
                    $ph = $H - $mg['t'] - $mg['b'];
                    $pas = count($lab) > 1 ? $pl / (count($lab) - 1) : $pl;
                    $pt = function ($serie) use ($mg, $pas, $ph, $haut) {
                        $p = [];
                        foreach ($serie as $i => $v) {
                            $p[] = round($mg['l'] + $pas * $i, 1) . ',' . round($mg['t'] + $ph - ($ph * $v / $haut), 1);
                        }
                        return implode(' ', $p);
                    };
                @endphp
                <div class="dck-chart">
                    <svg class="dck-aires" viewBox="0 0 {{ $L }} {{ $H }}" role="img"
                         aria-label="Tâches créées et terminées sur six mois">
                        @for ($i = 0; $i <= 4; $i++)
                            @php $y = $mg['t'] + $ph - ($ph * $i / 4); @endphp
                            <line class="{{ $i === 0 ? 'dck-zero' : 'dck-grid' }}"
                                  x1="{{ $mg['l'] }}" y1="{{ round($y, 1) }}"
                                  x2="{{ $L - $mg['r'] }}" y2="{{ round($y, 1) }}"/>
                            <text class="dck-axe" x="{{ $mg['l'] - 6 }}" y="{{ round($y + 3, 1) }}"
                                  text-anchor="end">{{ round($haut * $i / 4) }}</text>
                        @endfor

                        <polyline class="dck-serie dck-serie--1" points="{{ $pt($s1) }}"/>
                        <polyline class="dck-serie dck-serie--2" points="{{ $pt($s2) }}"/>

                        @foreach ($lab as $i => $l)
                            <text class="dck-axe" x="{{ round($mg['l'] + $pas * $i, 1) }}"
                                  y="{{ $H - 6 }}" text-anchor="middle">{{ $l }}</text>
                        @endforeach
                    </svg>
                </div>
                <div class="dck-legende2">
                    <span><i style="background:#175CD3"></i>Tâches créées</span>
                    <span><i style="background:#0E7C5A"></i>Tâches terminées</span>
                </div>
            </div>
        </div>

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Répartition des tâches')</h3>
                    <div class="dck-c__sub">Par colonne du tableau</div>
                </div>
            </div>
            <div class="dck-c__body">
                @php $maxCol = max(1, $cabRepartition->max('n')); @endphp
                @foreach ($cabRepartition as $col)
                    <div class="dck-bl">
                        <span class="dck-bl__j">{{ $col->column_name }}</span>
                        <span class="dck-bar">
                            <i style="width:{{ round(100 * $col->n / $maxCol) }}%;background:{{ $col->label_color }}"></i>
                        </span>
                        <span class="dck-bl__v">{{ $col->n }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Taches a surveiller et classement ─────────────────────── --}}
    <div class="dck-grid dck-g23">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Tâches à surveiller')</h3>
                    <div class="dck-c__sub">Échéance dans moins de 15 jours ou déjà dépassée</div>
                </div>
                <div class="dck-c__right">
                    <a class="dck-lien" href="{{ route('taskboards.index') }}">Tout voir</a>
                </div>
            </div>
            <div class="dck-tbl">
                <table>
                    <thead>
                        <tr>
                            <th>@lang('app.task')</th>
                            <th>@lang('modules.tasks.assignTo')</th>
                            <th>@lang('app.status')</th>
                            <th>@lang('app.dueDate')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cabASurveiller as $t)
                            @php $depasse = $t->due_date && $t->due_date->isPast(); @endphp
                            <tr>
                                <td><a class="dck-ttl" href="{{ route('tasks.show', $t->id) }}">{{ $t->heading }}</a></td>
                                <td class="dck-cl">{{ $t->users->pluck('name')->implode(', ') ?: '—' }}</td>
                                <td>
                                    <span class="dck-pastille"
                                          style="background:{{ optional($t->boardColumn)->label_color ?: '#9ca3af' }}"></span>
                                    {{ optional($t->boardColumn)->column_name ?: '—' }}
                                </td>
                                <td class="dck-date {{ $depasse ? 'is-late' : '' }}">
                                    {{ $t->due_date ? $t->due_date->translatedFormat('d/m/Y') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="dck-vide">@lang('Aucune échéance proche')</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- [BLOC RETIRE] Activite recente. Les donnees restent preparees
             par OverviewDashboard ($projectActivities) : seul l'affichage
             est retire. --}}

        {{-- Classement sur le trimestre : sur un mois, il tiendrait a deux
             taches et changerait tous les jours. --}}
        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Tâches clôturées')</h3>
                    <div class="dck-c__sub">Sur les trois derniers mois</div>
                </div>
            </div>
            <div class="dck-c__body">
                @php $hautCollab = max(1, ($cabTopCollab ?? collect())->max('n') ?? 1); @endphp

                @forelse ($cabTopCollab ?? [] as $c)
                    <div class="dck-bl">
                        <span class="dck-bl__j">{{ $c->name }}</span>
                        <span class="dck-bar">
                            <i style="width:{{ round(100 * $c->n / $hautCollab) }}%"></i>
                        </span>
                        <span class="dck-bl__v">{{ $c->n }}</span>
                    </div>
                @empty
                    <p class="dck-vide2">@lang('Aucune tâche clôturée sur la période.')</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Diligences livrees a temps ─────────────────────────────
         Le titre disait « Taux de cloture », comme la carte du haut :
         deux mesures differentes portaient le meme nom. Celle-ci compte
         les diligences respectant leur echeance, celle du haut la part
         de taches terminees. La grille passe en pleine largeur, le bloc
         voisin ayant ete retire. --}}
    <div class="dck-grid">

        <div class="dck-c">
            <div class="dck-c__head">
                <div>
                    <h3>@lang('Diligences livrées à temps')</h3>
                    <div class="dck-c__sub">Part des diligences terminées avant leur date limite</div>
                </div>
            </div>
            <div class="dck-c__body">
                <div class="dck-taux">
                    <b>{{ is_null($cabTauxCloture) ? '—' : $cabTauxCloture . ' %' }}</b>
                    <span class="dck-bar {{ ($cabTauxCloture ?? 0) >= 80 ? 'is-ok' : 'is-warn' }}">
                        <i style="width:{{ $cabTauxCloture ?? 0 }}%"></i>
                    </span>
                </div>
            </div>
        </div>

        {{-- [BLOC RETIRE] Echeancier reglementaire.
             Les donnees restent calculees par PilotageDiligences
             ($dashEcheances) : seul l'affichage est retire. --}}
    </div>

</section>
