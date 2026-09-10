{{--
    Carte d'indicateur du nouveau front.

    Usage :
        <x-dck.kpi icone="bi-file-earmark-text" libelle="Contrats actifs"
                   :valeur="$n" sous="en cours de validite" ton="ok" />

    « ton » accepte : ok, warn, err, info. Sans ton, la carte reste neutre.
    Les valeurs proviennent des variables deja calculees par le controleur :
    ce composant n'execute aucune requete.
--}}
@props([
    'icone'   => 'bi-graph-up',
    'libelle' => '',
    'valeur'  => '0',
    'sous'    => null,
    'ton'     => null,
    'cle'     => null,
])

<div class="dck-kpi">
    <div class="dck-kpi__top">
        <span class="dck-kpi__ico @if ($ton) is-{{ $ton }} @endif">
            <i class="bi {{ $icone }}"></i>
        </span>
        <span class="dck-kpi__lab">{{ $libelle }}</span>
    </div>

    {{-- « cle » permet a une page de reactualiser la valeur sans recharger
         l'ecran, apres une suppression par exemple. Sans cet attribut, le
         rendu est strictement inchange. --}}
    <div class="dck-kpi__val" @if ($cle) data-kpi="{{ $cle }}" @endif>{{ $valeur }}</div>

    @if ($sous)
        <div class="dck-kpi__sub">{{ $sous }}</div>
    @endif
</div>
