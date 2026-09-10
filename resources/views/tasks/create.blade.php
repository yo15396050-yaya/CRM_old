@extends('layouts.app')


@section('content')

{{-- Cette enveloppe sert a la creation, a la modification et a la
     consultation d'une tache, mais uniquement en page pleine : les
     ouvertures en panneau lateral sont interceptees plus tot par
     TaskController (request()->ajax()) et ne passent pas par ici.

     Le retour est donc toujours pertinent, en particulier quand on
     arrive depuis le lien d'une notification par courriel, sans
     aucun contexte de navigation.

     Il est place hors de @include($view) : la fiche remplace
     #task-detail-section et #nav-tabContent au changement d'onglet,
     ce qui emporterait le bouton s'il etait a l'interieur. --}}
<div class="content-wrapper dck-front">
    <x-dck.back :href="route('tasks.index')" :libelle="__('app.dckBack.tasks')" :barre="false" />

    @include($view)
</div>

@endsection
