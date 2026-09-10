{{--
    Importation massive de taches.

    Trois temps : telecharger le modele, deposer le fichier rempli,
    verifier l'apercu avant de confirmer. Rien n'est enregistre tant
    que la confirmation n'a pas ete donnee.
--}}

<div class="dck-import">

    <div class="dck-import__tete">
        <h2>@lang('Importer des tâches')</h2>
        <p>@lang("Créez plusieurs tâches d'un coup à partir d'un fichier Excel.")</p>
    </div>

    {{-- ── 1. Le modele ──────────────────────────────────────────── --}}
    <div class="dck-import__etape">
        <div class="dck-import__num">1</div>
        <div class="dck-import__corps">
            <h3>@lang('Téléchargez le modèle')</h3>
            <p>@lang("Il contient les colonnes attendues et une ligne d'exemple bâtie sur vos propres projets et catégories. Supprimez cette ligne avant d'importer.")</p>

            <a class="dck-import__b is-principal" href="{{ route('tasks.import.modele') }}">
                <i class="bi bi-file-earmark-arrow-down mr-1"></i>
                @lang('Télécharger le modèle Excel')
            </a>

            <table class="dck-import__cols">
                <thead>
                    <tr>
                        <th>@lang('Colonne')</th>
                        <th>@lang('Obligatoire')</th>
                        <th>@lang('Ce qui est attendu')</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $aide = [
                        'heading'     => __("Le titre de la tâche."),
                        'project'     => __("Le nom exact d'un projet existant. Laissez vide si la tâche n'est rattachée à aucun projet."),
                        'start_date'  => __('Format :') . ' ' . company()->date_format,
                        'due_date'    => __('Même format. Doit être postérieure à la date de début.'),
                        'status'      => __('Un des statuts du tableau ; voir la liste ci-dessous.'),
                        'assigned'    => __("Le nom ou l'e-mail d'un collaborateur. Plusieurs noms séparés par une virgule."),
                        'category'    => __("Le nom exact d'une catégorie existante."),
                        'description' => __('Texte libre.'),
                    ];
                @endphp
                @foreach ($colonnes as $c)
                    <tr>
                        <td><strong>{{ $c['nom'] }}</strong></td>
                        <td>
                            @if ($c['requis'])
                                <span class="dck-import__p is-req">@lang('oui')</span>
                            @else
                                <span class="dck-import__p">@lang('non')</span>
                            @endif
                        </td>
                        <td>{{ $aide[$c['cle']] ?? '' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <p class="dck-import__note">
                <strong>@lang('Statuts reconnus') :</strong>
                {{ \App\Models\TaskboardColumn::pluck('column_name')->implode(' · ') }}.
                <br>
                @lang("La casse et les accents n'ont pas d'importance : « en cours », « En cours » et « EN COURS » sont acceptés.")
                @lang('Sans statut, la tâche est créée en « Incomplète », comme à la création manuelle.')
            </p>
        </div>
    </div>

    {{-- ── 2. Le fichier ─────────────────────────────────────────── --}}
    <div class="dck-import__etape">
        <div class="dck-import__num">2</div>
        <div class="dck-import__corps">
            <h3>@lang('Déposez votre fichier')</h3>
            <p>@lang('Formats acceptés : xlsx, xls, csv.')
               {{ __('Maximum :') }} {{ \App\Imports\TaskImport::MAXIMUM_LIGNES }} {{ __('lignes par import.') }}</p>

            <form id="form-import-taches" enctype="multipart/form-data">
                @csrf
                <div class="dck-import__depot">
                    <input type="file" name="import_file" id="import_file"
                           accept=".xlsx,.xls,.csv" required>
                    <button type="submit" class="dck-import__b is-principal" id="btn-analyser">
                        <i class="bi bi-search mr-1"></i> @lang('Analyser le fichier')
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── 3. L'apercu, rempli par la reponse du serveur ─────────── --}}
    <div class="dck-import__etape d-none" id="etape-apercu">
        <div class="dck-import__num">3</div>
        <div class="dck-import__corps">
            <h3>@lang('Vérifiez avant de confirmer')</h3>
            <div id="zone-apercu"></div>
        </div>
    </div>

</div>

<script>
(function () {
    'use strict';

    var form = document.getElementById('form-import-taches'),
        etape = document.getElementById('etape-apercu'),
        zone = document.getElementById('zone-apercu'),
        bouton = document.getElementById('btn-analyser');

    if (!form) { return; }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var champ = document.getElementById('import_file');

        if (!champ.files.length) { return; }

        bouton.disabled = true;
        bouton.innerHTML = '{{ __("Analyse en cours…") }}';

        var donnees = new FormData(form);

        fetch('{{ route("tasks.import.analyse") }}', {
            method: 'POST',
            body: donnees,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (rep) {
            zone.innerHTML = rep.html || '';
            etape.classList.remove('d-none');
            etape.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function () {
            zone.innerHTML = '<div class="dck-import__msg is-err">{{ __("Le fichier n’a pas pu être analysé.") }}</div>';
            etape.classList.remove('d-none');
        })
        .finally(function () {
            bouton.disabled = false;
            bouton.innerHTML = '<i class="bi bi-search mr-1"></i> {{ __("Analyser le fichier") }}';
        });
    });

    /* Le bouton de confirmation est injecte avec l'apercu : on ecoute
       donc au niveau du conteneur, qui lui existe des le depart. */
    zone.addEventListener('click', function (e) {
        var b = e.target.closest('#btn-confirmer-import');

        if (!b) { return; }

        b.disabled = true;
        b.innerHTML = '{{ __("Importation en cours…") }}';

        var d = new FormData();
        d.append('_token', '{{ csrf_token() }}');
        d.append('jeton', b.dataset.jeton);

        fetch('{{ route("tasks.import.process") }}', {
            method: 'POST',
            body: d,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (rep) {
            if (rep.status === 'success') {
                window.location.href = rep.redirectUrl || '{{ route("tasks.index") }}';
            }
            else {
                zone.innerHTML = '<div class="dck-import__msg is-err">'
                    + (rep.message || '{{ __("L’importation a échoué.") }}') + '</div>';
            }
        })
        .catch(function () {
            b.disabled = false;
            b.innerHTML = '{{ __("Confirmer l’importation") }}';
        });
    });
})();
</script>
