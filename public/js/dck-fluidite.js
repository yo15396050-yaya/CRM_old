/* ══════════════════════════════════════════════════════════════
   DCK FLOW TASK — fluidite percue
   ──────────────────────────────────────────────────────────────
   Ce fichier est charge APRES main.js et ne le remplace pas :
   main.js est un bundle compile par Laravel Mix, et node_modules
   n'est pas installe sur ce poste — il ne peut donc pas etre
   regenere. Tout ce qui suit est purement additif.

   Aucun appel reseau supplementaire n'est declenche, sauf le
   prechargement au survol, volontairement limite aux liens du
   menu lateral (navigations simples, sans effet de bord).
   ══════════════════════════════════════════════════════════════ */

(function () {
    'use strict';

    /* ── 1. Squelette du panneau lateral ──────────────────────────
       Aujourd'hui : clic → panneau vide voile en gris → contenu.
       Desormais   : clic → structure affichee → contenu.

       On ecoute en phase de CAPTURE : le gestionnaire s'execute
       avant celui de main.js, donc avant le depart de la requete. */

    var SQUELETTE =
        '<div class="dck-skel" aria-busy="true" aria-live="polite">' +
            '<div class="dck-skel__line" style="width:62%;height:19px"></div>' +
            '<div class="dck-skel__line" style="width:38%;height:13px"></div>' +
            '<div class="dck-skel__sep"></div>' +
            '<div class="dck-skel__row">' +
                '<div class="dck-skel__box"></div>' +
                '<div class="dck-skel__col">' +
                    '<div class="dck-skel__line" style="width:54%"></div>' +
                    '<div class="dck-skel__line" style="width:34%;height:11px"></div>' +
                '</div>' +
            '</div>' +
            '<div class="dck-skel__line" style="width:100%"></div>' +
            '<div class="dck-skel__line" style="width:92%"></div>' +
            '<div class="dck-skel__line" style="width:76%"></div>' +
            '<div class="dck-skel__sep"></div>' +
            '<div class="dck-skel__grid">' +
                '<div class="dck-skel__card"></div>' +
                '<div class="dck-skel__card"></div>' +
            '</div>' +
        '</div>';

    // NE PAS toucher a document.loading : main.js s'en sert aussi pour
    // remplir les boutons en attente (loadingButton, ligne 28204) et un
    // span sr-only. Y mettre le squelette injectait ses ~20 div dans
    // chaque bouton. Le squelette est pose directement ci-dessous, sur
    // le seul conteneur qui le concerne.

    document.addEventListener('click', function (e) {
        var lien = e.target.closest && e.target.closest('.openRightModal');
        if (!lien) { return; }

        var contenu = document.querySelector('#right-modal-content');
        if (contenu) { contenu.innerHTML = SQUELETTE; }

        var titre = document.querySelector('#right-modal-title');
        if (titre) { titre.textContent = lien.textContent.trim().slice(0, 60) || ''; }
    }, true);


    /* ── 2. Reaction immediate a la navigation ────────────────────
       Une barre fine demarre des le clic. L'utilisateur sait que
       son geste est pris en compte, meme si le serveur reflechit. */

    var barre = null;

    function demarrerBarre() {
        if (barre) { return; }
        barre = document.createElement('div');
        barre.className = 'dck-nav-bar';
        document.body.appendChild(barre);
        requestAnimationFrame(function () { barre.style.width = '72%'; });
    }

    function estNavigationSimple(a) {
        if (!a || !a.href) { return false; }
        if (a.target && a.target !== '_self') { return false; }
        if (a.hasAttribute('download')) { return false; }
        if (/^javascript:/i.test(a.getAttribute('href') || '')) { return false; }
        if (a.getAttribute('href').charAt(0) === '#') { return false; }
        if (a.classList.contains('openRightModal')) { return false; }
        if (a.classList.contains('delete-table-row')) { return false; }
        if (a.hasAttribute('data-toggle')) { return false; }
        return a.origin === location.origin;
    }

    document.addEventListener('click', function (e) {
        var a = e.target.closest && e.target.closest('a');
        if (estNavigationSimple(a) && !e.metaKey && !e.ctrlKey && !e.shiftKey) {
            demarrerBarre();
        }
    }, true);

    // Retour arriere depuis le cache navigateur : on efface la barre
    window.addEventListener('pageshow', function () {
        if (barre) { barre.remove(); barre = null; }
    });


    /* ── 3. Prechargement mesure ──────────────────────────────────
       Uniquement les liens du menu lateral : ce sont des pages de
       liste, sans effet de bord. Plafonne a 6 pour ne pas inonder
       le serveur, et une seule fois par adresse. */

    var precharges = new Set();
    var PLAFOND = 6;

    function precharger(url) {
        if (precharges.size >= PLAFOND || precharges.has(url)) { return; }
        precharges.add(url);
        var l = document.createElement('link');
        l.rel = 'prefetch';
        l.href = url;
        document.head.appendChild(l);
    }

    document.addEventListener('mouseover', function (e) {
        var a = e.target.closest && e.target.closest('#sideMenuScroll a');
        if (a && estNavigationSimple(a)) { precharger(a.href); }
    }, { passive: true });


    /* ── 4. Retour immediat sur les boutons d'envoi ───────────────
       Le bouton passe en attente des la soumission, ce qui empeche
       aussi le double clic. Aucun formulaire n'est intercepte :
       on observe seulement l'evenement submit. */

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || form.hasAttribute('data-dck-libre')) { return; }

        var boutons = form.querySelectorAll('button[type="submit"], input[type="submit"], .btn-primary');
        Array.prototype.forEach.call(boutons, function (b) {
            if (b.disabled) { return; }
            b.classList.add('dck-busy');
            // Le formulaire peut echouer cote validation : on rend la
            // main au bout de 8 s pour ne jamais bloquer l'utilisateur.
            setTimeout(function () { b.classList.remove('dck-busy'); }, 8000);
        });
    }, true);


    /* ── 5. Squelette des tableaux au rechargement ────────────────
       DataTables vide la zone pendant qu'il interroge le serveur.
       On y place des lignes grises plutot qu'un espace blanc. */

    if (window.jQuery) {
        jQuery(document).on('preXhr.dt', function (e, settings) {
            var corps = settings.nTBody;
            if (!corps || corps.rows.length > 1) { return; }
            var colonnes = settings.aoColumns ? settings.aoColumns.length : 5;
            var html = '';
            for (var i = 0; i < 6; i++) {
                html += '<tr class="dck-skel-row">';
                for (var j = 0; j < colonnes; j++) {
                    html += '<td><div class="dck-skel__line" style="width:' + (40 + (j * 7) % 45) + '%"></div></td>';
                }
                html += '</tr>';
            }
            corps.innerHTML = html;
        });
    }

})();
