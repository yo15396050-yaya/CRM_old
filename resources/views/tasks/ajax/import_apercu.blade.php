{{--
    Apercu renvoye apres analyse du fichier.

    Il ne sert qu'a montrer : la confirmation relance une analyse cote
    serveur, de sorte que rien de ce qui est affiche ici ne fait
    autorite.
--}}

@if ($resultat->total === 0)

    <div class="dck-import__msg is-err">
        <strong>@lang("Le fichier n'a pas pu être lu.")</strong>
        <p>{{ $resultat->message }}</p>
    </div>

@else

    {{-- ── Bilan ─────────────────────────────────────────────────── --}}
    <div class="dck-import__bilan">
        <div class="dck-import__k">
            <span class="dck-import__kl">@lang('Lignes détectées')</span>
            <span class="dck-import__kv">{{ $resultat->total }}</span>
        </div>
        <div class="dck-import__k is-ok">
            <span class="dck-import__kl">@lang('Valides')</span>
            <span class="dck-import__kv">{{ $resultat->valides }}</span>
        </div>
        <div class="dck-import__k {{ $resultat->enErreur > 0 ? 'is-err' : '' }}">
            <span class="dck-import__kl">@lang('En erreur')</span>
            <span class="dck-import__kv">{{ $resultat->enErreur }}</span>
        </div>
    </div>

    {{-- ── Les erreurs, s'il y en a ──────────────────────────────── --}}
    @if ($resultat->enErreur > 0)
        <div class="dck-import__msg is-err">
            <strong>{{ $resultat->enErreur }}
                {{ trans_choice('ligne comporte une erreur|lignes comportent des erreurs', $resultat->enErreur) }}.</strong>
            <p>@lang("Aucune tâche ne sera créée tant que le fichier n'est pas corrigé. Reprenez votre fichier, corrigez les lignes ci-dessous, puis déposez-le à nouveau.")</p>

            <ul class="dck-import__erreurs">
                @foreach (array_slice($resultat->erreurs, 0, 40) as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>

            @if (count($resultat->erreurs) > 40)
                <p class="dck-import__note">
                    … {{ count($resultat->erreurs) - 40 }} @lang('autres erreurs non affichées.')
                </p>
            @endif
        </div>
    @endif

    {{-- ── Le detail des lignes ──────────────────────────────────── --}}
    <div class="dck-import__scroll">
        <table class="dck-import__tbl">
            <thead>
                <tr>
                    <th>@lang('Ligne')</th>
                    <th>@lang('Tâche')</th>
                    <th>@lang('Projet')</th>
                    <th>@lang('Statut')</th>
                    <th>@lang('Assigné à')</th>
                    <th>@lang('Début')</th>
                    <th>@lang('Fin')</th>
                    <th>@lang('État')</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($resultat->lignes as $l)
                <tr class="{{ $l->erreurs ? 'est-erreur' : '' }}">
                    <td class="dck-import__n">{{ $l->numero }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($l->titre, 46) ?: '—' }}</td>
                    <td>{{ $l->projet->project_name ?? ($l->projetNom ?: '—') }}</td>
                    <td>{{ $l->statut->column_name ?? '—' }}</td>
                    <td>{{ $l->gensNoms ? implode(', ', $l->gensNoms) : '—' }}</td>
                    <td class="dck-import__n">{{ $l->debut?->format(company()->date_format) ?: '—' }}</td>
                    <td class="dck-import__n">{{ $l->fin?->format(company()->date_format) ?: '—' }}</td>
                    <td>
                        @if ($l->erreurs)
                            <span class="dck-import__p is-err">@lang('erreur')</span>
                        @else
                            <span class="dck-import__p is-ok">@lang('valide')</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── La confirmation ───────────────────────────────────────── --}}
    @if ($resultat->ok && $jeton)
        <div class="dck-import__valider">
            <button type="button" class="dck-import__b is-principal" id="btn-confirmer-import"
                    data-jeton="{{ $jeton }}">
                <i class="bi bi-check2 mr-1"></i>
                {{ sprintf(__('Importer les %d tâches'), $resultat->total) }}
            </button>
            <span class="dck-import__note">@lang("Les tâches seront créées comme si elles avaient été saisies une à une.")</span>
        </div>
    @endif

@endif
