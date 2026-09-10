{{--
    Titre de section de formulaire du nouveau front.
    Reproduit la fonction sectionForm() de la maquette DCK Flow Task :
    un intitule court en majuscules, souligne d'un filet, avec une
    note facultative en gris a sa droite.

    Usage :
        <x-dck.form-section titre="Identification" />
        <x-dck.form-section :titre="__('app.dckSection.identification')" />
        <x-dck.form-section titre="Champs personnalises"
                            note="Definis dans les parametres" />

    Ce composant n'emet aucun champ de formulaire : il est purement
    presentationnel et ne peut pas influer sur les controleurs.
    Il se pose dans une .row Bootstrap et occupe toute la largeur.
    Le filet est porte par le conteneur interne, pour rester aligne
    sur les champs et non sur les bords de la colonne (padding 15px).
--}}
@props(['titre', 'note' => null])

<div class="col-sm-12">
    <div class="dck-form-section">
        <b>{{ $titre }}</b>
        @if ($note)
            <span>{{ $note }}</span>
        @endif
    </div>
</div>
