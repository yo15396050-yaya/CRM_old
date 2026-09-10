{{--
    En-tete de page du nouveau front.
    Titre, description facultative, et zone d'actions a droite.

    Usage :
        <x-dck.page-head titre="Contrats" description="...">
            <x-slot name="actions"> ... </x-slot>
        </x-dck.page-head>

    Ce composant n'emet aucun champ de formulaire : il est purement
    presentationnel et ne peut pas influer sur les controleurs.
--}}
@props(['titre', 'description' => null])

<div class="dck-page-head">
    <div class="dck-page-head__txt">
        <h1>{{ $titre }}</h1>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="dck-page-head__acts">{{ $actions }}</div>
    @endisset
</div>
