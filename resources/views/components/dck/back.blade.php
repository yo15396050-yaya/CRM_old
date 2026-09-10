{{--
    Lien de retour d'une page de detail vers sa liste.

    Usage :
        <x-dck.back :href="route('tasks.index')" :libelle="__('app.dckBack.tasks')" />

    A placer dans @section('filter-section') et non dans @section('content') :
    au changement d'onglet, le script AJAX remplace tout .content-wrapper et
    emporterait le bouton avec lui.

    L'attribut « barre » enveloppe le lien dans son propre bandeau. On le
    laisse a false quand la page possede deja un .dck-detail-head, comme la
    fiche de diligence, pour ne pas empiler deux bandeaux.
--}}
@props(['href', 'libelle', 'barre' => true])

@php
    $lien = '<a href="' . e($href) . '" class="dck-back">'
        . '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
        . ' stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . '<path d="M19 12H5M12 19l-7-7 7-7" /></svg>'
        . e($libelle) . '</a>';
@endphp

@if ($barre)
    <div class="dck-detail-head dck-detail-head--seul dck-front">{!! $lien !!}</div>
@else
    {!! $lien !!}
@endif
