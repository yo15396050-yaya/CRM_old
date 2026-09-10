<?php

/*
 * Traductions francaises du cabinet.
 *
 * Ces fichiers SONT suivis par git (.gitignore : « !resources/lang/fr/ ») et
 * partent donc en production, ou ils prennent le pas sur les fichiers livres
 * avec l'application. Toute cle absente ici retombe automatiquement sur
 * l'anglais (config/app.php : fallback_locale = 'eng').
 */

return [
    'email' => 'Adresse e-mail',
    'next' => 'Suivant',
    'failed' => 'Ces identifiants ne correspondent à aucun compte.',
    'failedLoginDisabled' => 'La connexion a été désactivée par votre administrateur.',
    'failedBlocked' => 'Votre compte est désactivé. Contactez votre administrateur pour le réactiver.',
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',
    'recaptchaFailed' => 'Le reCAPTCHA n’a pas été validé.',
    'sociaLoginFail' => 'Votre compte n’existe pas. Veuillez vous inscrire.',
    'signInGoogle' => 'Se connecter avec Google',
    'signInFacebook' => 'Se connecter avec Facebook',
    'signInLinkedin' => 'Se connecter avec LinkedIn',
    'signInTwitter' => 'Se connecter avec Twitter',
    'useEmail' => 'ou utilisez votre adresse e-mail',
];
