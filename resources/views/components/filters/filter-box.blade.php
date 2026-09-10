{{--
    Bandeau de filtres place au-dessus des listes.

    Il est desormais masque a l'ecran : les listes portent leur propre
    recherche et leurs propres filtres, et l'en-tete offre la recherche
    transversale. Le bandeau faisait donc doublon en haut de chaque page.

    Le balisage reste rendu, hors du flux : de nombreux ecrans lisent les
    valeurs de ces champs en JavaScript pour construire leurs requetes
    (#filter-form, #datatableRange, #status...). Les supprimer aurait
    casse le filtrage des tableaux. Pour reafficher le bandeau, retirer
    la classe « dck-filtres-caches ».
--}}
<div class="filter-box dck-filtres-caches" aria-hidden="true">
    <!-- FILTER START -->
    <form action="" id="filter-form">
        <div
            {{ $attributes->merge(['class' => 'd-lg-flex d-md-flex d-block flex-wrap filter-box bg-white client-list-filter']) }}>
            {{ $slot }}
        </div>
    </form>

</div>
