@php
    /*
     * Carte du tableau des taches.
     *
     * Disposition conforme a la maquette : la reference et la priorite sur
     * la premiere ligne, le titre ensuite, puis les intervenants et les
     * compteurs face a l'echeance.
     *
     * Les crochets du glisser-deposer sont inchanges : classe « task-card »,
     * data-task-id, id « drag-task-N » et « move-disable ». Les toucher
     * casserait le tableau.
     */
    $moveClass = $draggable == 'false' ? 'move-disable' : '';

    // Palette de la maquette, verifiee pour le contraste.
    $prio = [
        'high'   => ['#B42318', __('modules.tasks.high')],
        'medium' => ['#B54708', __('modules.tasks.medium')],
        'low'    => ['#0E7C5A', __('modules.tasks.low')],
    ];
    [$prioCouleur, $prioLibelle] = $prio[$task->priority] ?? $prio['low'];

    $enRetard = !is_null($task->due_date) && $task->due_date->endOfDay()->isPast();
    $aujourdhui = !is_null($task->due_date)
        && $task->due_date->setTimezone($company->timezone)->isToday();
@endphp

<div class="dck-tk {{ $moveClass }} task-card"
     data-task-id="{{ $task->id }}"
     data-need-approval="{{ optional($task->project)->need_approval_by_admin ?? 0 }}"
     id="drag-task-{{ $task->id }}"
     style="--dck-prio:{{ $prioCouleur }}">

    {{-- Reference et priorite --}}
    <div class="dck-tk__top">
        <span class="dck-tk__ref">{{ $task->task_short_code ? 'T-' . $task->task_short_code : '—' }}</span>
        <span class="dck-tk__prio">{{ $prioLibelle }}</span>

        @if ($task->is_private)
            <span class="dck-tk__prive" title="@lang('app.private')">
                <i class="fa fa-lock"></i>
            </span>
        @endif
    </div>

    {{-- Intitule --}}
    <a href="{{ route('tasks.show', [$task->id]) }}" class="dck-tk__titre openRightModal">
        {{ $task->heading }}
    </a>

    {{-- Etiquettes, si la tache en porte --}}
    @if (count($task->labels) > 0)
        <div class="dck-tk__etiq">
            @foreach ($task->labels as $label)
                <span style="background:{{ $label->label_color }}">{{ $label->label_name }}</span>
            @endforeach
        </div>
    @endif

    {{-- Intervenants, compteurs, echeance --}}
    <div class="dck-tk__bas">
        <div class="dck-tk__qui">
            @foreach ($task->users->take(3) as $item)
                @php
                    $initiales = collect(preg_split('/\s+/', trim($item->name)))
                        ->filter()->take(2)
                        ->map(fn ($m) => mb_strtoupper(mb_substr($m, 0, 1)))
                        ->implode('');
                @endphp
                <span class="dck-tk__av" title="{{ $item->name }}">{{ $initiales ?: '?' }}</span>
            @endforeach

            @if ($task->users->count() > 3)
                <span class="dck-tk__av is-plus">+{{ $task->users->count() - 3 }}</span>
            @endif
        </div>

        <div class="dck-tk__meta">
            @if (($task->files_count ?? 0) > 0)
                <span title="@lang('app.file')"><i class="bi bi-paperclip"></i>{{ $task->files_count }}</span>
            @endif

            @if (($task->comments_count ?? 0) > 0)
                <span title="@lang('app.comment')"><i class="bi bi-chat-left"></i>{{ $task->comments_count }}</span>
            @endif

            @if ($task->subtasks_count > 0)
                <span title="@lang('app.subTask')">
                    <i class="bi bi-diagram-2"></i>{{ $task->completed_subtasks_count }}/{{ $task->subtasks_count }}
                </span>
            @endif
        </div>

        @if (!is_null($task->due_date))
            <span class="dck-tk__ech {{ $enRetard ? 'is-late' : ($aujourdhui ? 'is-today' : '') }}">
                {{ $aujourdhui ? __('app.today') : $task->due_date->translatedFormat($company->date_format) }}
            </span>
        @endif
    </div>
</div>
