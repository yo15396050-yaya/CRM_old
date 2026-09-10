@extends('layouts.app')

{{--
    Rapport hebdomadaire des taches.

    Les onglets sont dans filter-section, comme sur les fiches de detail :
    la zone de contenu est remplacee au chargement, un onglet place a
    l'interieur disparaitrait.

    Le filtrage de la liste se fait cote client, sur des donnees deja
    servies : la semaine tient en quelques dizaines de lignes et une
    requete supplementaire par clic n'aurait aucun interet.
--}}

@section('filter-section')
    <div class="d-flex filter-box project-header bg-white">
        <div class="project-menu d-lg-flex">
            {{-- Le rapport detaille reste reserve aux droits complets :
                 son tableau ne sait pas se restreindre a une personne. --}}
            @if (user()->permission('view_task_report') == 'all')
                <x-tab :href="route('task-report.index')" ajax="false"
                       :text="__('Rapport détaillé')" class="detaille" />
            @endif
            <x-tab :href="route('task-report.hebdomadaire')" ajax="false"
                   :text="__('Rapport hebdomadaire')" class="hebdomadaire active" />
        </div>
    </div>
@endsection

@section('content')
<div class="content-wrapper dck-front dck-hebdo">

    @php
        $k = $rapport->synthese;

        /* Chaque indicateur porte le filtre qu'il applique au clic. Les
           deux derniers n'en ont pas : ils resument, ils ne trient pas. */
        $cartes = [
            ['Tâches de la semaine', $k->total, 'dans le périmètre', '', 'tous'],
            ['Terminées', $k->termines,
                $k->total ? round($k->termines / $k->total * 100) . ' % du total' : '—', 'ok', 'completed'],
            ['À faire', $k->aFaire, 'non démarrées', '', 'to_do'],
            ['En cours', $k->enCours, 'en traitement', '', 'doing'],
            ['En retard', $k->retards, 'échéance dépassée', $k->retards > 0 ? 'err' : '', 'retard'],
            ['En attente', $k->attente, "d'approbation", '', 'waiting_approval'],
            ['Délai moyen', $k->delaiMoyen ?? '—',
                'jours, sur ' . $k->delaisJuges . ' ' . trans_choice('tâche|tâches', $k->delaisJuges), '', null],
            ['Collaborateurs', $k->gens, 'impliqués cette semaine', '', null],
        ];

        $libelles = [
            'completed' => 'Terminée', 'doing' => 'En cours', 'to_do' => 'À faire',
            'incomplete' => 'Incomplète', 'waiting_approval' => "En attente d'approbation",
        ];
        $teintes = [
            'completed' => 'ok', 'doing' => 'info', 'to_do' => 'neutre',
            'incomplete' => 'warn', 'waiting_approval' => 'warn',
        ];
        $priorites = ['high' => ['Haute', 'err'], 'medium' => ['Moyenne', 'neutre'], 'low' => ['Basse', 'neutre']];
    @endphp

    {{-- ── En-tete et navigation de semaine ─────────────────────── --}}
    <div class="dck-hebdo__tete">
        <div>
            <h1>{{ $rapport->limiteA ? __('Mon rapport hebdomadaire') : __('Rapport hebdomadaire des tâches') }}</h1>
            <p>Du {{ $rapport->debut->locale('fr')->translatedFormat('l j F Y') }}
               au {{ $rapport->fin->locale('fr')->translatedFormat('l j F Y') }}</p>
        </div>

        <form method="GET" class="dck-hebdo__sem">
            <a class="dck-hebdo__b" href="{{ route('task-report.hebdomadaire', ['semaine' => $semainePrecedente]) }}">
                &larr; @lang('Semaine précédente')
            </a>
            <a class="dck-hebdo__b" href="{{ route('task-report.hebdomadaire', ['semaine' => $semaineSuivante]) }}">
                @lang('Semaine suivante') &rarr;
            </a>
            <a class="dck-hebdo__b @if ($rapport->semaineIso === $semaineCourante) is-actif @endif"
               href="{{ route('task-report.hebdomadaire') }}">@lang('Cette semaine')</a>
            <input type="week" name="semaine" class="dck-hebdo__b" value="{{ $rapport->semaineIso }}"
                   onchange="this.form.submit()" aria-label="{{ __('Choisir une semaine') }}">
        </form>
    </div>

    {{-- ── Indicateurs ──────────────────────────────────────────── --}}
    <div class="dck-hebdo__grille">
        @foreach ($cartes as [$lib, $val, $sous, $mod, $filtre])
            <{{ $filtre ? 'button type=button' : 'div' }}
                class="dck-hebdo__k @if ($mod) is-{{ $mod }} @endif"
                @if ($filtre) data-filtre="{{ $filtre }}" @endif>
                <span class="dck-hebdo__kl">{{ $lib }}</span>
                <span class="dck-hebdo__kv">{{ $val }}</span>
                <span class="dck-hebdo__ks">{{ $sous }}</span>
            </{{ $filtre ? 'button' : 'div' }}>
        @endforeach
    </div>

    @if ($k->total > 0 && $k->pointeTaches === 0)
        {{-- Dit franchement pourquoi la colonne « Pointé » est vide,
             plutot que de laisser croire a une panne. --}}
        <div class="dck-hebdo__note">
            <i class="bi bi-info-circle"></i>
            <div>
                <b>@lang('Temps pointé indisponible sur cette semaine')</b>
                <p>Aucune des {{ $k->total }} tâches n'a de saisie de temps. La mesure de référence
                   est le <strong>délai de traitement</strong>, calculé sur
                   {{ $k->delaisJuges }} {{ trans_choice('tâche|tâches', $k->delaisJuges) }}.</p>
            </div>
        </div>
    @endif

    @if ($k->total === 0)
        <div class="dck-hebdo__bloc">
            <div class="dck-hebdo__rien is-visible" style="display:block">
                @lang("Aucune tâche n'a été réalisée, échue ou modifiée durant cette semaine.")
            </div>
        </div>
    @else

    {{-- ── Activite par collaborateur ─────────────────────────────
         Sans objet quand le rapport ne porte que sur une personne. --}}
    @if (!$rapport->limiteA)
    <div class="dck-hebdo__bloc">
        <div class="dck-hebdo__bt">
            <h2>@lang('Activité par collaborateur')</h2>
            <span>@lang('Cliquez sur un chiffre pour voir les tâches correspondantes')</span>
        </div>
        <div class="dck-hebdo__scroll">
            <table class="dck-hebdo__tbl">
                <thead>
                    <tr>
                        <th>@lang('Collaborateur')</th>
                        <th>@lang('Tâches')</th>
                        <th>@lang('Terminées')</th>
                        <th>@lang('En cours')</th>
                        <th>@lang('À faire')</th>
                        <th>@lang('En retard')</th>
                        <th>@lang('Délai moyen')</th>
                        @if ($k->pointeTaches > 0)<th>@lang('Pointé')</th>@endif
                    </tr>
                </thead>
                <tbody>
                @foreach ($rapport->collaborateurs as $c)
                    <tr>
                        <td><strong>{{ $c->nom }}</strong></td>
                        @foreach ([['tous', $c->total, ''], ['completed', $c->termines, ''],
                                   ['doing', $c->enCours, ''], ['to_do', $c->aFaire, ''],
                                   ['retard', $c->retards, ' est-retard']] as [$slug, $val, $cls])
                            <td>
                                <button type="button" class="dck-hebdo__cpt{{ $cls }}"
                                        data-gens="{{ $c->id }}" data-statut="{{ $slug }}"
                                        @if ($val === 0) disabled @endif>{{ $val }}</button>
                            </td>
                        @endforeach
                        <td class="dck-hebdo__n">
                            {{ $c->delaiMoyen !== null ? $c->delaiMoyen . ' j' : '—' }}
                        </td>
                        @if ($k->pointeTaches > 0)
                            <td class="dck-hebdo__n">{{ $c->pointe > 0 ? round($c->pointe, 1) . ' h' : '—' }}</td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @endif

    {{-- ── Detail des taches ────────────────────────────────────── --}}
    <div class="dck-hebdo__bloc" id="bloc-taches">
        <div class="dck-hebdo__bt">
            <h2>@lang('Détail des tâches')</h2>
            <span><span id="hebdo-compteur">{{ $k->total }}</span> / {{ $k->total }} @lang('affichées')</span>
        </div>

        <div class="dck-hebdo__filtres">
            <select id="hebdo-statut">
                <option value="tous">@lang('Tous les statuts')</option>
                @foreach ($libelles as $slug => $lib)
                    <option value="{{ $slug }}">{{ $lib }}</option>
                @endforeach
                <option value="retard">@lang('En retard')</option>
            </select>

            <select id="hebdo-gens" @if ($rapport->limiteA) class="d-none" @endif>
                <option value="tous">@lang('Tous les collaborateurs')</option>
                @foreach ($rapport->collaborateurs as $c)
                    <option value="{{ $c->id }}">{{ $c->nom }}</option>
                @endforeach
            </select>

            <select id="hebdo-prio">
                <option value="tous">@lang('Toutes priorités')</option>
                @foreach ($priorites as $slug => [$lib, $x])
                    <option value="{{ $slug }}">{{ $lib }}</option>
                @endforeach
            </select>

            <input type="search" id="hebdo-texte" placeholder="{{ __('Rechercher une tâche, un client…') }}">

            <div class="dck-hebdo__exp">
                <a class="dck-hebdo__b" style="height:34px;font-size:12.5px"
                   href="{{ route('task-report.hebdomadaire.export', ['semaine' => $rapport->semaineIso]) }}">
                    <i class="bi bi-file-earmark-spreadsheet mr-1"></i> @lang('Excel')
                </a>
                <a class="dck-hebdo__b" style="height:34px;font-size:12.5px"
                   href="{{ route('task-report.hebdomadaire.pdf', ['semaine' => $rapport->semaineIso]) }}">
                    <i class="bi bi-file-earmark-pdf mr-1"></i> @lang('PDF')
                </a>
                <button type="button" onclick="window.print()">
                    <i class="bi bi-printer"></i> @lang('Imprimer')
                </button>
            </div>
        </div>

        <div class="dck-hebdo__actif" id="hebdo-bandeau">
            <span>@lang('Filtre actif') : <b id="hebdo-bandeau-txt"></b></span>
            <button type="button" id="hebdo-effacer">@lang('Tout afficher')</button>
        </div>

        <div class="dck-hebdo__scroll">
            <table class="dck-hebdo__tbl">
                <thead>
                    <tr>
                        <th>@lang('Réf.')</th>
                        <th>@lang('Tâche')</th>
                        <th>@lang('Statut')</th>
                        <th>@lang('Priorité')</th>
                        <th>@lang('Début')</th>
                        <th>@lang('Échéance')</th>
                        <th>@lang('Réalisée')</th>
                        <th>@lang('Délai')</th>
                        <th>@lang('Prévu')</th>
                        @if ($k->pointeTaches > 0)<th>@lang('Pointé')</th>@endif
                        <th>@lang('Collaborateurs')</th>
                        <th>@lang('Màj')</th>
                    </tr>
                </thead>
                <tbody id="hebdo-corps">
                @foreach ($rapport->taches as $t)
                    @php
                        $recherche = mb_strtolower(trim(
                            $t->titre . ' ' . $t->client . ' ' . $t->diligence . ' ' . $t->reference . ' '
                            . $t->gens->pluck('nom')->implode(' ')));
                        [$pLib, $pTeinte] = $priorites[$t->priorite] ?? [$t->priorite, 'neutre'];
                    @endphp
                    <tr data-slug="{{ $t->slug }}" data-retard="{{ $t->retard ? '1' : '0' }}"
                        data-prio="{{ $t->priorite }}"
                        data-gens="{{ $t->gens->pluck('id')->implode('|') }}"
                        data-texte="{{ $recherche }}">
                        <td class="dck-hebdo__ref">{{ $t->reference ?: '—' }}</td>
                        <td>
                            {{-- Meme detail que partout ailleurs : openRightModal
                                 sur tasks.show, aucune seconde logique. --}}
                            <a class="dck-hebdo__ttl openRightModal" href="{{ route('tasks.show', $t->id) }}">
                                {{ $t->titre }}
                            </a>
                            @if ($t->client || $t->diligence)
                                <div class="dck-hebdo__sous">{{ $t->client ?: $t->diligence }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="dck-hebdo__p is-{{ $teintes[$t->slug] ?? 'neutre' }}">
                                {{ $libelles[$t->slug] ?? $t->statut }}
                            </span>
                            @if ($t->retard)
                                <span class="dck-hebdo__p is-err mt-1">@lang('En retard')</span>
                            @endif
                        </td>
                        <td><span class="dck-hebdo__p is-{{ $pTeinte }}">{{ $pLib }}</span></td>
                        <td class="dck-hebdo__n">{{ $t->debut?->translatedFormat('d/m/Y') ?: '—' }}</td>
                        <td class="dck-hebdo__n">{{ $t->echeance?->translatedFormat('d/m/Y') ?: '—' }}</td>
                        <td class="dck-hebdo__n">{{ $t->faitLe?->translatedFormat('d/m/Y') ?: '—' }}</td>
                        <td class="dck-hebdo__n">{{ $t->delai !== null ? $t->delai . ' j' : '—' }}</td>
                        <td class="dck-hebdo__n">{{ $t->prevu ?: '—' }}</td>
                        @if ($k->pointeTaches > 0)
                            <td class="dck-hebdo__n">{{ $t->pointe !== null ? $t->pointe . ' h' : '—' }}</td>
                        @endif
                        <td>
                            @if ($t->gens->isNotEmpty())
                                <div class="dck-hebdo__gens">
                                    @foreach ($t->gens as $g)
                                        <span class="dck-hebdo__g">{{ $g->nom }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="dck-hebdo__vide">@lang('non affectée')</span>
                            @endif
                        </td>
                        <td class="dck-hebdo__n">{{ $t->maj->translatedFormat('d/m/Y') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="dck-hebdo__rien" id="hebdo-rien">
                @lang('Aucune tâche ne correspond à ce filtre.')
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var corps = document.getElementById('hebdo-corps');

    if (!corps) { return; }   // semaine vide : rien a filtrer

    var libelles = @json(array_merge($libelles, ['retard' => 'En retard', 'tous' => 'Toutes les tâches'])),
        fStatut  = document.getElementById('hebdo-statut'),
        fGens    = document.getElementById('hebdo-gens'),
        fPrio    = document.getElementById('hebdo-prio'),
        fTexte   = document.getElementById('hebdo-texte'),
        compteur = document.getElementById('hebdo-compteur'),
        rien     = document.getElementById('hebdo-rien'),
        bandeau  = document.getElementById('hebdo-bandeau'),
        bTxt     = document.getElementById('hebdo-bandeau-txt'),
        lignes   = Array.prototype.slice.call(corps.querySelectorAll('tr'));

    function appliquer() {
        var st = fStatut.value, gn = fGens.value, pr = fPrio.value,
            tx = fTexte.value.trim().toLowerCase(), vus = 0;

        lignes.forEach(function (l) {
            // « En retard » n'est pas un statut du tableau mais une
            // condition : non terminee et echeance depassee.
            var okStatut = st === 'tous'
                    || (st === 'retard' ? l.dataset.retard === '1' : l.dataset.slug === st),
                okGens = gn === 'tous' || l.dataset.gens.split('|').indexOf(gn) !== -1,
                okPrio = pr === 'tous' || l.dataset.prio === pr,
                okTexte = tx === '' || l.dataset.texte.indexOf(tx) !== -1,
                voir = okStatut && okGens && okPrio && okTexte;

            l.style.display = voir ? '' : 'none';
            if (voir) { vus++; }
        });

        compteur.textContent = vus;
        rien.classList.toggle('is-visible', vus === 0);

        var actifs = [];
        if (gn !== 'tous') { actifs.push(fGens.options[fGens.selectedIndex].text); }
        if (st !== 'tous') { actifs.push(libelles[st] || st); }
        if (pr !== 'tous') { actifs.push(fPrio.options[fPrio.selectedIndex].text); }
        if (tx !== '')     { actifs.push('« ' + fTexte.value.trim() + ' »'); }

        bandeau.classList.toggle('is-visible', actifs.length > 0);
        bTxt.textContent = actifs.join(' · ');

        document.querySelectorAll('.dck-hebdo__k[data-filtre]').forEach(function (c) {
            c.classList.toggle('is-selectionne', c.dataset.filtre === st && gn === 'tous');
        });
    }

    function versListe() {
        document.getElementById('bloc-taches')
            .scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    [fStatut, fGens, fPrio].forEach(function (s) { s.addEventListener('change', appliquer); });
    fTexte.addEventListener('input', appliquer);

    document.querySelectorAll('.dck-hebdo__cpt').forEach(function (b) {
        b.addEventListener('click', function () {
            fGens.value = b.dataset.gens;
            fStatut.value = b.dataset.statut;
            fPrio.value = 'tous';
            fTexte.value = '';
            appliquer();
            versListe();
        });
    });

    document.querySelectorAll('.dck-hebdo__k[data-filtre]').forEach(function (c) {
        c.addEventListener('click', function () {
            fStatut.value = c.dataset.filtre;
            fGens.value = 'tous';
            appliquer();
            versListe();
        });
    });

    document.getElementById('hebdo-effacer').addEventListener('click', function () {
        fStatut.value = 'tous'; fGens.value = 'tous';
        fPrio.value = 'tous'; fTexte.value = '';
        appliquer();
    });

    appliquer();
})();
</script>
@endpush
