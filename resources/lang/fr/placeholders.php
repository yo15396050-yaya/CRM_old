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

    /* --- Identite et coordonnees ------------------------------------- */

    'name' => 'ex. Kouassi Konan',
    'email' => 'ex. kouassi.konan@exemple.com',
    'mobile' => 'ex. 0700000000',
    'mobileWithPlus' => 'ex. +2250700000000',
    'password' => 'Doit contenir au moins 8 caractères',
    'renterPassword' => 'Veuillez saisir votre mot de passe',
    'address' => 'ex. Cocody 7e Tranche, Abidjan',
    'city' => 'ex. Abidjan, Bouaké, Yamoussoukro',
    'state' => 'ex. Lagunes, Vallée du Bandama',
    'postalCode' => 'ex. 01 BP 1234',
    'location' => 'Saisir un lieu',
    'website' => 'ex. https://www.exemple.com',
    'company' => 'ex. Société Anonyme Exemple',
    'relationship' => 'ex. père',
    'title' => 'ex. Responsable',
    'designation' => 'ex. Chef d’équipe',
    'department' => 'ex. Ressources humaines',
    'skills' => 'ex. communication, ReactJS',
    'twitterId' => 'ex. 182180xxxxxx1843842',

    /* --- Diligences, taches, contrats -------------------------------- */

    'project' => 'Saisir le nom de la diligence',
    'writeshortcode' => 'Code court unique de la diligence',
    'taskshortcode' => 'Code court unique de la tâche',
    'task' => 'Saisir un titre de tâche',
    'label' => 'Saisir un titre d’étiquette',
    'category' => 'Saisir un nom de catégorie',
    'columnName' => 'ex. En attente',
    'status' => 'ex. En cours',
    'milestone' => 'Saisir le titre du jalon',
    'milestoneSummary' => 'Saisir le résumé du jalon',
    'hourEstimate' => 'ex. 500',
    'searchForProjects' => 'Rechercher des diligences',
    'searchForCompany' => 'Rechercher une entreprise',

    /* --- Champs generiques ------------------------------------------- */

    'date' => 'Sélectionner une date',
    'dateRange' => 'Date de début à date de fin',
    'days' => 'ex. 3 jours',
    'hours' => 'ex. 10:00',
    'price' => 'ex. 10000',
    'note' => 'Saisir le titre de la note',
    'message' => 'Écrivez votre message ici',
    'search' => 'Saisir un mot-clé à rechercher',
    'sampleText' => 'ex. Ceci est un exemple de texte',
    'colorPicker' => 'Sélectionner une couleur',
    'linkTitle' => 'Titre du lien',
    'url' => 'URL',
    'min' => 'Min',
    'max' => 'Max',
    'select2Min' => 'Veuillez saisir au moins 2 caractères',
    'noneSelectedText' => 'Aucune sélection',
    'noneResultsText' => 'Aucun résultat pour {0}',
    'selectAllText' => 'Tout sélectionner',
    'deselectAllText' => 'Tout désélectionner',

    /* --- Modules divers ---------------------------------------------- */

    'knowledgeBase' => 'Mettre à jour votre photo de profil',
    'leaveType' => 'ex. Maladie, Convenance personnelle',
    'ticketType' => 'ex. Assistance',
    'noticeTitle' => 'ex. Célébration du nouvel an au bureau.',
    'productName' => 'ex. Hébergement web, Ordinateur portable, Application mobile',
    'sku' => 'Saisir la référence (SKU) du produit',
    'hsnSac' => 'ex. 995431',
    'gstNumber' => 'ex. 18AABCU960XXXXX',
    'gst/vat' => 'ex. TVA',
    'emailDomain' => 'ex. gmail.com',
    'categoryName' => 'ex. Client potentiel',
    'consent' => 'Saisir le nom du consentement',
    'consentDescription' => 'Décrire brièvement l’objet du consentement',
    'consent_description' => 'description du consentement',
    'shiftName' => 'ex. Équipe de nuit',
    'shiftShortCode' => 'ex. EN',
    'latitude' => 'ex. 5.3600',
    'longitude' => 'ex. -4.0083',

    /* --- Cles techniques (valeurs volontairement non traduites) ------- */

    'webhook' => 'ex. samplewebhook123',
    'key' => 'ex. 0f99088a2f31e70daxxxx',
    'secret' => 'ex. cbabcd0948aed7dd9xxxx',
    'id' => 'ex. 1275901',
    'cluster' => 'ex. ap2',
    'slackWebhook' => 'ex. https://hooks.slack.com/services/XXXXXXXXXXXXXXXXXXXX',
    'googleMapKey' => 'ex. AIzaSyDSl2bG7XXXXXXXXXXXXXXXXXX',
    'purchaseCode' => 'ex. 147778a2-dfa2-424e-a29f-xxxxxxxxx',
    'recaptchaV2' => 'ex. 6LeL_s8ZAAAAAMVC2clQdxxxXXXxxxxxXXX',
    'recaptchaV3' => 'ex. 6LeL_s8ZAAAAAMVC2clQdxxxXXXxxxxxXXX',
    'recaptchaSecret' => 'ex. XXXXXXxxxxxXXXXXXxxxxx',
    'fileSetting' => 'ex. application/x-zip-compressed',

    /* --- Sous-groupes ------------------------------------------------ */

    'invoices' => [
        'invoicePrefix' => 'Saisir le préfixe des factures',
        'estimatePrefix' => 'Saisir le préfixe des devis',
        'creditNotePrefix' => 'Saisir le préfixe des avoirs',
        'contractPrefix' => 'Préfixe des contrats',
        'contractNumber' => 'Saisir le numéro de contrat',
        'gstNumber' => 'Saisir le numéro fiscal',
        'invoiceTerms' => 'Saisir les conditions de facturation',
        'description' => 'Saisir une description (facultatif)',
        'note' => 'ex. Merci de votre confiance',
        'terms' => 'Indiquez vos conditions de retour et d’annulation',
        'invoiceNumberSeparator' => 'Séparateur de numéro de facture',
        'estimateNumberSeparator' => 'Séparateur de numéro de devis',
        'credit_noteNumberSeparator' => 'Séparateur de numéro d’avoir',
        'contractNumberSeparator' => 'Séparateur de numéro de contrat',
        'otherInfo' => 'Saisir d’autres informations',
        'estimateRequestPrefix' => 'Saisir le préfixe des demandes de devis',
        'estimateRequestNumberSeparator' => 'Séparateur de numéro de demande de devis',
        'proposalPrefix' => 'Saisir le préfixe des propositions',
        'proposalNumberSeparator' => 'Séparateur de numéro de proposition',
        'BankDetails' => 'Ajouter les coordonnées de paiement (compte bancaire, virement…)',
        'title' => 'Description du paiement',
    ],

    'currency' => [
        'currencyName' => 'ex. Franc CFA',
        'currencySymbol' => 'ex. F',
        'currencyCode' => 'ex. XOF',
        'thousandSeparator' => 'ex. ,',
        'decimalSeparator' => 'ex. .',
    ],

    'ticket' => [
        'replyTicket' => 'Saisir le titre du modèle',
        'templateText' => 'Saisir ici le texte du modèle',
    ],

    'tickets' => [
        'ticketChannel' => 'ex. Facebook, Téléphone, etc.',
        'ticketGroup' => 'ex. Commercial, Assistance, etc.',
    ],

    'payments' => [
        'remark' => 'Saisir un résumé du paiement.',
        'transactionId' => 'Saisir l’identifiant de la transaction',
        'paymentGateway' => 'ex. PayPal, Stripe, Virement, Espèces, etc.',
    ],

    'offlinePayment' => [
        'method' => 'ex. Espèces, Chèque, etc.',
        'description' => 'ex. en dollars US',
    ],

    'expense' => [
        'item' => 'ex. Clavier sans fil',
        'vendor' => 'ex. Société Exemple',
    ],

    'timelog' => [
        'memo' => 'ex. Travail sur le nouveau logo',
    ],

    'attendance' => [
        'workFrom' => 'ex. Bureau, Domicile, etc.',
    ],

    'leave' => [
        'reason' => 'ex. Ne me sens pas bien',
    ],

    'appreciation' => [
        'title' => 'ex. Employé du mois',
    ],

    'estimateRequest' => [
        'reason' => 'ex. Hors budget',
    ],

    'gdpr' => [
        'additionDescription' => 'Saisir une description complémentaire',
    ],

    'role' => [
        'roleName' => 'ex. RH',
    ],

    'language' => [
        'languageName' => 'ex. Français',
        'languageCode' => 'ex. fr',
    ],

    'client' => [
        'skype' => 'ex. nomUtilisateurSkype',
        'linkedin' => 'ex. https://www.linkedin.com/XXXXXXXXXX/',
        'twitter' => 'ex. @kouassikonan',
        'facebook' => 'ex. https://www.facebook.com/XXXXXXXXXX/',
    ],

    'bankaccount' => [
        'accountName' => 'ex. Kouassi Konan',
        'bankName' => 'ex. Banque Atlantique',
        'accountNumber' => 'ex. 123456789',
        'balance' => 'ex. 25 000 F',
    ],

    'storageSetting' => [
        'awsKey' => 'ex. AKIAXXXXX67FNYNM2P',
        'awsBucket' => 'ex. test-bucket',
    ],

    'paymentGateway' => [
        'sandboxPaypalClientId' => 'ex. AW-Ydt5KHz2FwhAikHsObpRrpB55qE8MyvUkHbQsFb_6_2Unv3WNBSmBxEqA8N74JzOaFTPBUI-MG4sB',
        'livePaypalClientId' => 'ex. AW-Ydt5KHz2FwhAikHsObpRrpB55qE8MyvUkHbQsFb_6_2Unv3WNBSmBxEqA8N74JzOaFTPBUI-MG4sB',
        'testStripePublishableKey' => 'ex. sk_test_XXXXXXXXXXxBXnjBe1d6G5reXbGAc8a1qumQN0doumYbhb2tChV6qTCuFfvQyxEDInYho7jhQoR4MqNBWcafRYPCb00r2jkEBKe',
        'liveStripePublishableKey' => 'ex. sk_live_51GbndSLM4xBXnjBe1d6G5reXbGAc8a1qumQN0doumYbhb2tChV6qTCuFfvQyxEDInYho7jhQoR4MqNBWcafRYPCbXXXXXXXXX',
        'testRazorpayKey' => 'ex. rzp_test_znKZOLXXT3XXEX',
        'liveRazorpayKey' => 'ex. rzp_live_znKZOLn4TXXXXX',
        'paystackKey' => 'ex. pk_live_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
        'flutterwaveKey' => 'ex. FLWPUBK-xxxxxxxxxxxxxxxxxxxxx-X',
    ],

];
