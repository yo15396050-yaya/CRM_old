



<?php $__env->startSection('filter-section'); ?>
    <div class="d-flex filter-box project-header bg-white">
        <div class="project-menu d-lg-flex">
            <?php if (isset($component)) { $__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab = $attributes; } ?>
<?php $component = App\View\Components\Tab::resolve(['href' => route('task-report.index'),'ajax' => 'false','text' => __('Rapport détaillé')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tab'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Tab::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'detaille']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab)): ?>
<?php $attributes = $__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab; ?>
<?php unset($__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab)): ?>
<?php $component = $__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab; ?>
<?php unset($__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab = $attributes; } ?>
<?php $component = App\View\Components\Tab::resolve(['href' => route('task-report.hebdomadaire'),'ajax' => 'false','text' => __('Rapport hebdomadaire')] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tab'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Tab::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'hebdomadaire active']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab)): ?>
<?php $attributes = $__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab; ?>
<?php unset($__attributesOriginal4b0c45ee1a38bb46a01c2a25edd749ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab)): ?>
<?php $component = $__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab; ?>
<?php unset($__componentOriginal4b0c45ee1a38bb46a01c2a25edd749ab); ?>
<?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper dck-front dck-hebdo">

    <?php
        $k = $rapport->synthese;

        /* Chaque indicateur porte le filtre qu'il applique au clic. Les
           deux derniers n'en ont pas : ils resument, ils ne trient pas. */
        $cartes = [
            ['Tâches de la semaine', $k->total, 'dans le périmètre', '', 'tous'],
            ['Terminées', $k->termines,
                $k->total ? round($k->termines / $k->total * 100) . ' % du total' : '—', 'ok', 'completed'],
            ['À faire', $k->aFaire, 'non démarrées', '', 'to_do'],
            ['En cours', $k->enCours, 'en traitement', '', 'doing'],
            ['En retard', $k->retards, 'échéance dépassée', $k->retards > 0 ? 'err' : '', 'retard'],
            ['En attente', $k->attente, "d'approbation", '', 'waiting_approval'],
            ['Délai moyen', $k->delaiMoyen ?? '—',
                'jours, sur ' . $k->delaisJuges . ' ' . trans_choice('tâche|tâches', $k->delaisJuges), '', null],
            ['Collaborateurs', $k->gens, 'impliqués cette semaine', '', null],
        ];

        $libelles = [
            'completed' => 'Terminée', 'doing' => 'En cours', 'to_do' => 'À faire',
            'incomplete' => 'Incomplète', 'waiting_approval' => "En attente d'approbation",
        ];
        $teintes = [
            'completed' => 'ok', 'doing' => 'info', 'to_do' => 'neutre',
            'incomplete' => 'warn', 'waiting_approval' => 'warn',
        ];
        $priorites = ['high' => ['Haute', 'err'], 'medium' => ['Moyenne', 'neutre'], 'low' => ['Basse', 'neutre']];
    ?>

    
    <div class="dck-hebdo__tete">
        <div>
            <h1><?php echo e($rapport->limiteA ? __('Mon rapport hebdomadaire') : __('Rapport hebdomadaire des tâches')); ?></h1>
            <p>Du <?php echo e($rapport->debut->locale('fr')->translatedFormat('l j F Y')); ?>

               au <?php echo e($rapport->fin->locale('fr')->translatedFormat('l j F Y')); ?></p>
        </div>

        <form method="GET" class="dck-hebdo__sem">
            <a class="dck-hebdo__b" href="<?php echo e(route('task-report.hebdomadaire', ['semaine' => $semainePrecedente])); ?>">
                &larr; <?php echo app('translator')->get('Semaine précédente'); ?>
            </a>
            <a class="dck-hebdo__b" href="<?php echo e(route('task-report.hebdomadaire', ['semaine' => $semaineSuivante])); ?>">
                <?php echo app('translator')->get('Semaine suivante'); ?> &rarr;
            </a>
            <a class="dck-hebdo__b <?php if($rapport->semaineIso === $semaineCourante): ?> is-actif <?php endif; ?>"
               href="<?php echo e(route('task-report.hebdomadaire')); ?>"><?php echo app('translator')->get('Cette semaine'); ?></a>
            <input type="week" name="semaine" class="dck-hebdo__b" value="<?php echo e($rapport->semaineIso); ?>"
                   onchange="this.form.submit()" aria-label="<?php echo e(__('Choisir une semaine')); ?>">
        </form>
    </div>

    
    <div class="dck-hebdo__grille">
        <?php $__currentLoopData = $cartes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$lib, $val, $sous, $mod, $filtre]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <<?php echo e($filtre ? 'button type=button' : 'div'); ?>

                class="dck-hebdo__k <?php if($mod): ?> is-<?php echo e($mod); ?> <?php endif; ?>"
                <?php if($filtre): ?> data-filtre="<?php echo e($filtre); ?>" <?php endif; ?>>
                <span class="dck-hebdo__kl"><?php echo e($lib); ?></span>
                <span class="dck-hebdo__kv"><?php echo e($val); ?></span>
                <span class="dck-hebdo__ks"><?php echo e($sous); ?></span>
            </<?php echo e($filtre ? 'button' : 'div'); ?>>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($k->total > 0 && $k->pointeTaches === 0): ?>
        
        <div class="dck-hebdo__note">
            <i class="bi bi-info-circle"></i>
            <div>
                <b><?php echo app('translator')->get('Temps pointé indisponible sur cette semaine'); ?></b>
                <p>Aucune des <?php echo e($k->total); ?> tâches n'a de saisie de temps. La mesure de référence
                   est le <strong>délai de traitement</strong>, calculé sur
                   <?php echo e($k->delaisJuges); ?> <?php echo e(trans_choice('tâche|tâches', $k->delaisJuges)); ?>.</p>
            </div>
        </div>
    <?php endif; ?>

    <?php if($k->total === 0): ?>
        <div class="dck-hebdo__bloc">
            <div class="dck-hebdo__rien is-visible" style="display:block">
                <?php echo app('translator')->get("Aucune tâche n'a été réalisée, échue ou modifiée durant cette semaine."); ?>
            </div>
        </div>
    <?php else: ?>

    
    <?php if(!$rapport->limiteA): ?>
    <div class="dck-hebdo__bloc">
        <div class="dck-hebdo__bt">
            <h2><?php echo app('translator')->get('Activité par collaborateur'); ?></h2>
            <span><?php echo app('translator')->get('Cliquez sur un chiffre pour voir les tâches correspondantes'); ?></span>
        </div>
        <div class="dck-hebdo__scroll">
            <table class="dck-hebdo__tbl">
                <thead>
                    <tr>
                        <th><?php echo app('translator')->get('Collaborateur'); ?></th>
                        <th><?php echo app('translator')->get('Tâches'); ?></th>
                        <th><?php echo app('translator')->get('Terminées'); ?></th>
                        <th><?php echo app('translator')->get('En cours'); ?></th>
                        <th><?php echo app('translator')->get('À faire'); ?></th>
                        <th><?php echo app('translator')->get('En retard'); ?></th>
                        <th><?php echo app('translator')->get('Délai moyen'); ?></th>
                        <?php if($k->pointeTaches > 0): ?><th><?php echo app('translator')->get('Pointé'); ?></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $rapport->collaborateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($c->nom); ?></strong></td>
                        <?php $__currentLoopData = [['tous', $c->total, ''], ['completed', $c->termines, ''],
                                   ['doing', $c->enCours, ''], ['to_do', $c->aFaire, ''],
                                   ['retard', $c->retards, ' est-retard']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$slug, $val, $cls]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td>
                                <button type="button" class="dck-hebdo__cpt<?php echo e($cls); ?>"
                                        data-gens="<?php echo e($c->id); ?>" data-statut="<?php echo e($slug); ?>"
                                        <?php if($val === 0): ?> disabled <?php endif; ?>><?php echo e($val); ?></button>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="dck-hebdo__n">
                            <?php echo e($c->delaiMoyen !== null ? $c->delaiMoyen . ' j' : '—'); ?>

                        </td>
                        <?php if($k->pointeTaches > 0): ?>
                            <td class="dck-hebdo__n"><?php echo e($c->pointe > 0 ? round($c->pointe, 1) . ' h' : '—'); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>

    
    <div class="dck-hebdo__bloc" id="bloc-taches">
        <div class="dck-hebdo__bt">
            <h2><?php echo app('translator')->get('Détail des tâches'); ?></h2>
            <span><span id="hebdo-compteur"><?php echo e($k->total); ?></span> / <?php echo e($k->total); ?> <?php echo app('translator')->get('affichées'); ?></span>
        </div>

        <div class="dck-hebdo__filtres">
            <select id="hebdo-statut">
                <option value="tous"><?php echo app('translator')->get('Tous les statuts'); ?></option>
                <?php $__currentLoopData = $libelles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $lib): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($slug); ?>"><?php echo e($lib); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <option value="retard"><?php echo app('translator')->get('En retard'); ?></option>
            </select>

            <select id="hebdo-gens" <?php if($rapport->limiteA): ?> class="d-none" <?php endif; ?>>
                <option value="tous"><?php echo app('translator')->get('Tous les collaborateurs'); ?></option>
                <?php $__currentLoopData = $rapport->collaborateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->nom); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <select id="hebdo-prio">
                <option value="tous"><?php echo app('translator')->get('Toutes priorités'); ?></option>
                <?php $__currentLoopData = $priorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => [$lib, $x]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($slug); ?>"><?php echo e($lib); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <input type="search" id="hebdo-texte" placeholder="<?php echo e(__('Rechercher une tâche, un client…')); ?>">

            <div class="dck-hebdo__exp">
                <a class="dck-hebdo__b" style="height:34px;font-size:12.5px"
                   href="<?php echo e(route('task-report.hebdomadaire.export', ['semaine' => $rapport->semaineIso])); ?>">
                    <i class="bi bi-file-earmark-spreadsheet mr-1"></i> <?php echo app('translator')->get('Excel'); ?>
                </a>
                <a class="dck-hebdo__b" style="height:34px;font-size:12.5px"
                   href="<?php echo e(route('task-report.hebdomadaire.pdf', ['semaine' => $rapport->semaineIso])); ?>">
                    <i class="bi bi-file-earmark-pdf mr-1"></i> <?php echo app('translator')->get('PDF'); ?>
                </a>
                <button type="button" onclick="window.print()">
                    <i class="bi bi-printer"></i> <?php echo app('translator')->get('Imprimer'); ?>
                </button>
            </div>
        </div>

        <div class="dck-hebdo__actif" id="hebdo-bandeau">
            <span><?php echo app('translator')->get('Filtre actif'); ?> : <b id="hebdo-bandeau-txt"></b></span>
            <button type="button" id="hebdo-effacer"><?php echo app('translator')->get('Tout afficher'); ?></button>
        </div>

        <div class="dck-hebdo__scroll">
            <table class="dck-hebdo__tbl">
                <thead>
                    <tr>
                        <th><?php echo app('translator')->get('Réf.'); ?></th>
                        <th><?php echo app('translator')->get('Tâche'); ?></th>
                        <th><?php echo app('translator')->get('Statut'); ?></th>
                        <th><?php echo app('translator')->get('Priorité'); ?></th>
                        <th><?php echo app('translator')->get('Début'); ?></th>
                        <th><?php echo app('translator')->get('Échéance'); ?></th>
                        <th><?php echo app('translator')->get('Réalisée'); ?></th>
                        <th><?php echo app('translator')->get('Délai'); ?></th>
                        <th><?php echo app('translator')->get('Prévu'); ?></th>
                        <?php if($k->pointeTaches > 0): ?><th><?php echo app('translator')->get('Pointé'); ?></th><?php endif; ?>
                        <th><?php echo app('translator')->get('Collaborateurs'); ?></th>
                        <th><?php echo app('translator')->get('Màj'); ?></th>
                    </tr>
                </thead>
                <tbody id="hebdo-corps">
                <?php $__currentLoopData = $rapport->taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $recherche = mb_strtolower(trim(
                            $t->titre . ' ' . $t->client . ' ' . $t->projet . ' ' . $t->reference . ' '
                            . $t->gens->pluck('nom')->implode(' ')));
                        [$pLib, $pTeinte] = $priorites[$t->priorite] ?? [$t->priorite, 'neutre'];
                    ?>
                    <tr data-slug="<?php echo e($t->slug); ?>" data-retard="<?php echo e($t->retard ? '1' : '0'); ?>"
                        data-prio="<?php echo e($t->priorite); ?>"
                        data-gens="<?php echo e($t->gens->pluck('id')->implode('|')); ?>"
                        data-texte="<?php echo e($recherche); ?>">
                        <td class="dck-hebdo__ref"><?php echo e($t->reference ?: '—'); ?></td>
                        <td>
                            
                            <a class="dck-hebdo__ttl openRightModal" href="<?php echo e(route('tasks.show', $t->id)); ?>">
                                <?php echo e($t->titre); ?>

                            </a>
                            <?php if($t->client || $t->projet): ?>
                                <div class="dck-hebdo__sous"><?php echo e($t->client ?: $t->projet); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="dck-hebdo__p is-<?php echo e($teintes[$t->slug] ?? 'neutre'); ?>">
                                <?php echo e($libelles[$t->slug] ?? $t->statut); ?>

                            </span>
                            <?php if($t->retard): ?>
                                <span class="dck-hebdo__p is-err mt-1"><?php echo app('translator')->get('En retard'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><span class="dck-hebdo__p is-<?php echo e($pTeinte); ?>"><?php echo e($pLib); ?></span></td>
                        <td class="dck-hebdo__n"><?php echo e($t->debut?->translatedFormat('d/m/Y') ?: '—'); ?></td>
                        <td class="dck-hebdo__n"><?php echo e($t->echeance?->translatedFormat('d/m/Y') ?: '—'); ?></td>
                        <td class="dck-hebdo__n"><?php echo e($t->faitLe?->translatedFormat('d/m/Y') ?: '—'); ?></td>
                        <td class="dck-hebdo__n"><?php echo e($t->delai !== null ? $t->delai . ' j' : '—'); ?></td>
                        <td class="dck-hebdo__n"><?php echo e($t->prevu ?: '—'); ?></td>
                        <?php if($k->pointeTaches > 0): ?>
                            <td class="dck-hebdo__n"><?php echo e($t->pointe !== null ? $t->pointe . ' h' : '—'); ?></td>
                        <?php endif; ?>
                        <td>
                            <?php if($t->gens->isNotEmpty()): ?>
                                <div class="dck-hebdo__gens">
                                    <?php $__currentLoopData = $t->gens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="dck-hebdo__g"><?php echo e($g->nom); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <span class="dck-hebdo__vide"><?php echo app('translator')->get('non affectée'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="dck-hebdo__n"><?php echo e($t->maj->translatedFormat('d/m/Y')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="dck-hebdo__rien" id="hebdo-rien">
                <?php echo app('translator')->get('Aucune tâche ne correspond à ce filtre.'); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    'use strict';

    var corps = document.getElementById('hebdo-corps');

    if (!corps) { return; }   // semaine vide : rien a filtrer

    var libelles = <?php echo json_encode(array_merge($libelles, ['retard' => 'En retard', 'tous' => 'Toutes les tâches'])) ?>,
        fStatut  = document.getElementById('hebdo-statut'),
        fGens    = document.getElementById('hebdo-gens'),
        fPrio    = document.getElementById('hebdo-prio'),
        fTexte   = document.getElementById('hebdo-texte'),
        compteur = document.getElementById('hebdo-compteur'),
        rien     = document.getElementById('hebdo-rien'),
        bandeau  = document.getElementById('hebdo-bandeau'),
        bTxt     = document.getElementById('hebdo-bandeau-txt'),
        lignes   = Array.prototype.slice.call(corps.querySelectorAll('tr'));

    function appliquer() {
        var st = fStatut.value, gn = fGens.value, pr = fPrio.value,
            tx = fTexte.value.trim().toLowerCase(), vus = 0;

        lignes.forEach(function (l) {
            // « En retard » n'est pas un statut du tableau mais une
            // condition : non terminee et echeance depassee.
            var okStatut = st === 'tous'
                    || (st === 'retard' ? l.dataset.retard === '1' : l.dataset.slug === st),
                okGens = gn === 'tous' || l.dataset.gens.split('|').indexOf(gn) !== -1,
                okPrio = pr === 'tous' || l.dataset.prio === pr,
                okTexte = tx === '' || l.dataset.texte.indexOf(tx) !== -1,
                voir = okStatut && okGens && okPrio && okTexte;

            l.style.display = voir ? '' : 'none';
            if (voir) { vus++; }
        });

        compteur.textContent = vus;
        rien.classList.toggle('is-visible', vus === 0);

        var actifs = [];
        if (gn !== 'tous') { actifs.push(fGens.options[fGens.selectedIndex].text); }
        if (st !== 'tous') { actifs.push(libelles[st] || st); }
        if (pr !== 'tous') { actifs.push(fPrio.options[fPrio.selectedIndex].text); }
        if (tx !== '')     { actifs.push('« ' + fTexte.value.trim() + ' »'); }

        bandeau.classList.toggle('is-visible', actifs.length > 0);
        bTxt.textContent = actifs.join(' · ');

        document.querySelectorAll('.dck-hebdo__k[data-filtre]').forEach(function (c) {
            c.classList.toggle('is-selectionne', c.dataset.filtre === st && gn === 'tous');
        });
    }

    function versListe() {
        document.getElementById('bloc-taches')
            .scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    [fStatut, fGens, fPrio].forEach(function (s) { s.addEventListener('change', appliquer); });
    fTexte.addEventListener('input', appliquer);

    document.querySelectorAll('.dck-hebdo__cpt').forEach(function (b) {
        b.addEventListener('click', function () {
            fGens.value = b.dataset.gens;
            fStatut.value = b.dataset.statut;
            fPrio.value = 'tous';
            fTexte.value = '';
            appliquer();
            versListe();
        });
    });

    document.querySelectorAll('.dck-hebdo__k[data-filtre]').forEach(function (c) {
        c.addEventListener('click', function () {
            fStatut.value = c.dataset.filtre;
            fGens.value = 'tous';
            appliquer();
            versListe();
        });
    });

    document.getElementById('hebdo-effacer').addEventListener('click', function () {
        fStatut.value = 'tous'; fGens.value = 'tous';
        fPrio.value = 'tous'; fTexte.value = '';
        appliquer();
    });

    appliquer();
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\CRM\resources\views/reports/tasks/hebdomadaire.blade.php ENDPATH**/ ?>