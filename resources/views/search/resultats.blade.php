@extends('layouts.app')

@section('content')

    <div class="content-wrapper dck-front">

        <x-dck.page-head :titre="__('Résultats de recherche')"
                         :description="$mot !== '' ? __('Pour « :mot »', ['mot' => $mot]) : null" />

        {{-- Rappel de la saisie : la recherche reste modifiable sans
             remonter dans l'en-tete. --}}
        <form class="dck-res__form" action="{{ route('search.resultats') }}" method="GET" role="search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="{{ $mot }}" autocomplete="off"
                   placeholder="{{ __('Rechercher une tâche, un client, un contrat…') }}"
                   aria-label="{{ __('Rechercher') }}">
            <button type="submit" class="btn btn-primary">@lang('app.search')</button>
        </form>

        @if ($tropCourt)
            <div class="dck-note is-warn">
                <span class="dck-note__i"><i class="bi bi-info-circle"></i></span>
                <div>
                    <b>@lang('Saisissez au moins deux caractères')</b>
                    <p>@lang('Une lettre seule ramènerait presque toute la base.')</p>
                </div>
            </div>
        @elseif ($mot === '')
            <div class="dck-note">
                <span class="dck-note__i"><i class="bi bi-search"></i></span>
                <div>
                    <b>@lang('Que cherchez-vous ?')</b>
                    <p>@lang('La recherche porte sur les tâches, les diligences et les clients.')</p>
                </div>
            </div>
        @elseif (($total ?? 0) === 0)
            <div class="dck-note">
                <span class="dck-note__i"><i class="bi bi-inbox"></i></span>
                <div>
                    <b>@lang('Aucun résultat pour « :mot »', ['mot' => $mot])</b>
                    <p>@lang('Vérifiez l\'orthographe, ou cherchez sur un mot plus court.')</p>
                </div>
            </div>
        @else

            {{-- ── Taches ─────────────────────────────────────────── --}}
            @if ($taches->isNotEmpty())
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3>@lang('app.menu.tasks')</h3>
                            <div class="dck-c__sub">{{ $taches->count() }} {{ trans_choice('résultat|résultats', $taches->count()) }}</div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="{{ route('tasks.index') }}?search_keyword={{ urlencode($mot) }}">
                                @lang('Tout voir')
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        @foreach ($taches as $t)
                            <a class="dck-res__item" href="{{ route('tasks.show', $t->id) }}">
                                <span class="dck-res__ico"><i class="bi bi-check2-square"></i></span>
                                <span class="dck-res__txt">
                                    <b>{{ $t->heading }}</b>
                                    <small>
                                        {{ $t->task_short_code ?: '—' }}
                                        @if ($t->due_date) · {{ $t->due_date->translatedFormat('d/m/Y') }} @endif
                                    </small>
                                </span>
                                @if ($t->boardColumn)
                                    <span class="dck-res__badge"
                                          style="background:{{ $t->boardColumn->label_color }}1a;color:{{ $t->boardColumn->label_color }}">
                                        {{ $t->boardColumn->column_name }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Diligences ─────────────────────────────────────── --}}
            @if ($diligences->isNotEmpty())
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3>@lang('app.menu.projects')</h3>
                            <div class="dck-c__sub">{{ $diligences->count() }} {{ trans_choice('résultat|résultats', $diligences->count()) }}</div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="{{ route('projects.index') }}?search_keyword={{ urlencode($mot) }}">
                                @lang('Tout voir')
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        @foreach ($diligences as $d)
                            <a class="dck-res__item" href="{{ route('projects.show', $d->id) }}">
                                <span class="dck-res__ico"><i class="bi bi-folder2-open"></i></span>
                                <span class="dck-res__txt">
                                    <b>{{ $d->project_name }}</b>
                                    <small>{{ $d->project_short_code ?: '—' }}</small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Contrats ───────────────────────────────────────── --}}
            @if ($contrats->isNotEmpty())
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3>@lang('app.menu.contracts')</h3>
                            <div class="dck-c__sub">{{ $contrats->count() }} {{ trans_choice('résultat|résultats', $contrats->count()) }}</div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="{{ route('contracts.index') }}?search_keyword={{ urlencode($mot) }}">
                                @lang('Tout voir')
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        @foreach ($contrats as $ct)
                            <a class="dck-res__item" href="{{ route('contracts.show', $ct->id) }}">
                                <span class="dck-res__ico"><i class="bi bi-file-earmark-text"></i></span>
                                <span class="dck-res__txt">
                                    <b>{{ $ct->subject }}</b>
                                    <small>
                                        {{ $ct->contract_number ?: '—' }}
                                        @if ($ct->client) · {{ $ct->client->name }} @endif
                                        @if ($ct->end_date) · @lang('jusqu\'au') {{ $ct->end_date->translatedFormat('d/m/Y') }} @endif
                                    </small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Clients ────────────────────────────────────────── --}}
            @if ($clients->isNotEmpty())
                <div class="dck-c dck-res">
                    <div class="dck-c__head">
                        <div>
                            <h3>@lang('app.menu.clients')</h3>
                            <div class="dck-c__sub">{{ $clients->count() }} {{ trans_choice('résultat|résultats', $clients->count()) }}</div>
                        </div>
                        <div class="dck-c__right">
                            <a class="dck-lien" href="{{ route('clients.index') }}?search_keyword={{ urlencode($mot) }}">
                                @lang('Tout voir')
                            </a>
                        </div>
                    </div>
                    <div class="dck-c__body dck-pile">
                        @foreach ($clients as $c)
                            <a class="dck-res__item" href="{{ route('clients.show', $c->id) }}">
                                <span class="dck-res__ico"><i class="bi bi-building"></i></span>
                                <span class="dck-res__txt">
                                    <b>{{ $c->company_name ?: $c->name }}</b>
                                    <small>
                                        @if ($c->company_name) {{ $c->name }} · @endif
                                        {{ $c->email }}
                                    </small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        @endif

    </div>

@endsection
