<?php
    /*
     * Jeu d'icones de la refonte : trait de 1.7, grille 24, meme
     * vocabulaire graphique que la maquette.
     *
     * L'ancien mecanisme (<x-slot name="iconPath"> en 16x16 pleine)
     * reste actif : une entree sans attribut « dck » s'affiche comme
     * avant. Les deux systemes cohabitent, la bascule se fait entree
     * par entree.
     */
    $dckIcones = [
        'grid'     => 'M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z',
        'task'     => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 13l2 2 4-4',
        'folder'   => 'M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2z',
        'file'     => 'M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8zM14 3v5h5M9 13h6M9 17h4',
        'users'    => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 3a4 4 0 100 8 4 4 0 000-8M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75',
        'building' => 'M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01',
        'calendar' => 'M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z',
        'box'      => 'M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16zM3.3 7L12 12l8.7-5M12 22V12',
        'cart'     => 'M6 6h15l-1.5 9h-12zM6 6L5 3H2M9 20a1 1 0 100-2 1 1 0 000 2zM18 20a1 1 0 100-2 1 1 0 000 2z',
        'chart'    => 'M3 3v18h18M7 15l4-5 3 3 5-7',
        'scale'    => 'M12 3v18M8 21h8M5 7h14M7 7l-3 7h6zM17 7l-3 7h6z',
        'cog'      => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 004.6 15a1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 009 4.6a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06A1.65 1.65 0 0019.4 9c.14.31.4.55.71.66H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z',
        'bell'     => 'M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 01-3.4 0',
        'inbox'    => 'M22 12h-6l-2 3h-4l-2-3H2M5.45 5.11L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6l-3.45-6.89A2 2 0 0016.76 4H7.24a2 2 0 00-1.79 1.11z',
        'mail'     => 'M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zM22 7l-10 6L2 7',
        'shield'   => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',
        'book'     => 'M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z',
        'clock'    => 'M12 22a10 10 0 100-20 10 10 0 000 20zM12 6v6l4 2',
        'link'     => 'M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71',
    ];

    /* menu-item est adosse a une classe PHP : un attribut non declare
       dans le constructeur arrive dans $attributes, pas dans une variable.
       On le recupere ici, puis on le retire pour qu'il ne soit pas rendu
       comme attribut HTML sur le <li>. */
    $dckNom  = $attributes->get('dck');
    $attributes = $attributes->except('dck');
    $dckPath = ($dckNom && isset($dckIcones[$dckNom])) ? $dckIcones[$dckNom] : null;
?>

<li <?php echo e($attributes->merge(['class' => 'accordionItem closeIt'])); ?>>

    <?php if(!is_null($link)): ?>
        <a class="nav-item text-lightest f-15 sidebar-text-color" href="<?php echo e($link); ?>"
           title="<?php echo e($text); ?>">
            <?php if($dckPath): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.7"
                     stroke-linecap="round" stroke-linejoin="round" class="dck-ico" aria-hidden="true">
                    <path d="<?php echo e($dckPath); ?>" />
                </svg>
            <?php elseif(isset($iconPath)): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-<?php echo e($icon); ?>" viewBox="0 0 16 16">
                    <?php echo $iconPath; ?>

                </svg>
            <?php endif; ?>
            <span class="pl-3"><?php echo e($text); ?></span>
            <?php if($addon): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-gift ml-2 text-yellow" viewBox="0 0 16 16" data-toggle="tooltip"
                     data-original-title="Add On (Need to be bought separately)">
                    <path
                        d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43a.522.522 0 0 0 .023.07zM9 3h2.932a.56.56 0 0 0 .023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0V3zM1 4v2h6V4H1zm8 0v2h6V4H9zm5 3H9v8h4.5a.5.5 0 0 0 .5-.5V7zm-7 8V7H2v7.5a.5.5 0 0 0 .5.5H7z"/>
                </svg>
            <?php endif; ?>
            <?php if($count != 0): ?>
                <span class="badge badge-primary menu-item-count"><?php echo e($count); ?></span>
            <?php endif; ?>
        </a>
    <?php else: ?>
        <a class="nav-item text-lightest f-15 sidebar-text-color accordionItemHeading <?php echo e($active == 1 ? 'active' : ''); ?>"
           title="<?php echo e($text); ?>">
            <?php if($dckPath): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.7"
                     stroke-linecap="round" stroke-linejoin="round" class="dck-ico" aria-hidden="true">
                    <path d="<?php echo e($dckPath); ?>" />
                </svg>
            <?php elseif(isset($iconPath)): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-<?php echo e($icon); ?>" viewBox="0 0 16 16">
                    <?php echo $iconPath; ?>

                </svg>
            <?php endif; ?>
            <span class="pl-3"><?php echo e($text); ?></span>

            <?php if($addon): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                     class="bi bi-gift ml-2 text-yellow" viewBox="0 0 16 16" data-toggle="tooltip"
                     data-original-title="Add On (Need to be bought separately)">
                    <path
                        d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43a.522.522 0 0 0 .023.07zM9 3h2.932a.56.56 0 0 0 .023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0V3zM1 4v2h6V4H1zm8 0v2h6V4H9zm5 3H9v8h4.5a.5.5 0 0 0 .5-.5V7zm-7 8V7H2v7.5a.5.5 0 0 0 .5.5H7z"/>
                </svg>
            <?php endif; ?>
        </a>
    <?php endif; ?>

    <?php echo e($slot); ?>

</li>
<?php /**PATH C:\laragon\www\dc_knowing\CRM\resources\views/components/menu-item.blade.php ENDPATH**/ ?>