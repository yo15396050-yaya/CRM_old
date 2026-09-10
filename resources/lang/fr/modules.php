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

    'module' => [
        'tasks' => 'Tâches',
        'projects' => 'Diligences',
        'contracts' => 'Contrats',
        'clients' => 'Clients',
        'employees' => 'Employés',
        'attendance' => 'Présence',
        'leaves' => 'Congés',
        'holidays' => 'Jours fériés',
        'events' => 'Événements',
        'messages' => 'Messages',
        'notices' => 'Annonces',
        'noticeBoard' => 'Tableau d’affichage',
        'notice board' => 'Tableau d’affichage',
        'notes' => 'Notes',
        'timelogs' => 'Feuilles de temps',
        'invoices' => 'Factures',
        'estimates' => 'Devis',
        'proposals' => 'Propositions',
        'payments' => 'Paiements',
        'expenses' => 'Dépenses',
        'payroll' => 'Paie',
        'bankaccount' => 'Compte bancaire',
        'purchase' => 'Achats',
        'orders' => 'Commandes',
        'products' => 'Produits',
        'product' => 'Produit',
        'tickets' => 'Tickets',
        'leads' => 'Prospects',
        'deals' => 'Affaires',
        'reports' => 'Rapports',
        'dashboards' => 'Tableaux de bord',
        'settings' => 'Paramètres',
        'knowledgebase' => 'Base de connaissances',
        'addknowledgebaseCategory' => 'Gérer les catégories d’articles',
        'asset' => 'Actifs',
        'emergency_contact' => 'Contact d’urgence',
        'recruit' => 'Recrutement',
        'letter' => 'Courrier',
        'webhooks' => 'Webhooks',
        'sms' => 'SMS',
        'restapi' => 'API REST',
        'qrcode' => 'QR Code',
        'cybersecurity' => 'Cybersécurité',
        'biolinks' => 'Biolinks',
        'Policy' => 'Centre de politiques',
    ],

    /* ================================================================== */
    /* Module Taches                                                       */
    /* ================================================================== */

    'tasks' => [
        'newTask' => 'Nouvelle tâche',
        'myTask' => 'Mes tâches',
        'updateTask' => 'Modifier la tâche',
        'taskDetail' => 'Détail de la tâche',
        'taskInfo' => 'Informations sur la tâche',
        'taskBoard' => 'Tableau des tâches',
        'tasksTable' => 'Tableau des tâches',
        'subTask' => 'Sous-tâche',
        'editSubTask' => 'Modifier la sous-tâche',
        'noSubTaskFound' => 'Aucune sous-tâche trouvée',
        'dependent' => 'Cette tâche dépend d’une autre tâche',
        'dependentTask' => 'Tâche dont elle dépend',
        'chooseTask' => 'Choisir une tâche',
        'copyTaskLink' => 'Copier le lien de la tâche',

        'selectProject' => 'Sélectionner une diligence',
        'notFinishedProjects' => 'Seules les diligences en cours sont affichées',
        'hideCompletedTask' => 'Masquer les tâches terminées',
        'lastCreated' => 'Créées récemment',
        'dueSoon' => 'Échéance proche',
        'dueDate' => 'Échéance',
        'loadMore' => 'Charger plus',

        'assignTo' => 'Assignée à',
        'assignBy' => 'Assignée par',
        'assigned' => 'Assignée',
        'unassigned' => 'Non assignée',
        'assignMe' => 'M’assigner cette tâche',
        'chooseAssignee' => 'Choisir un responsable',
        'users' => 'Utilisateurs',
        'you' => 'Vous',

        'priority' => 'Priorité',
        'selectPriority' => 'Sélectionner une priorité',
        'high' => 'Haute',
        'medium' => 'Moyenne',
        'low' => 'Basse',

        'changeStatus' => 'Changer le statut',
        'defaultTaskStatus' => 'Statut par défaut des tâches',
        'markComplete' => 'Marquer comme terminée',
        'markIncomplete' => 'Marquer comme non terminée',
        'sendForApproval' => 'Envoyer pour validation',
        'reminder' => 'Envoyer un rappel',
        'moveToBoard' => 'Déplacer vers le tableau',
        'taskboardNote' => ' Vous ne pouvez pas déplacer une tâche vers ou depuis la colonne « En attente de validation ». Pour changer son statut, rendez-vous dans le ',
        'menu' => ' menu.',

        'addBoardColumn' => 'Ajouter une colonne de statut',
        'boardColumn' => 'Colonne du tableau',
        'columnName' => 'Nom de la colonne',
        'position' => 'Position de la colonne',
        'labelColor' => 'Couleur de l’étiquette',
        'taskLabel' => 'Étiquette de tâche',
        'taskboardDefaultLength' => 'Nombre de tâches affichées par colonne',

        'taskCategory' => 'Catégorie de tâche',
        'selectTaskCategory' => 'Sélectionner une catégorie de tâche',
        'noTaskCategory' => 'Aucune catégorie de tâche disponible',
        'category' => 'Catégorie',

        'comment' => 'Commentaire',
        'history' => 'Historique',
        'uplodedFiles' => 'Fichiers joints',
        'sectionVisibleClient' => 'Sections visibles par le client',

        'makePrivate' => 'Rendre privée',
        'privateInfo' => 'Les tâches privées ne sont visibles que par l’administrateur, l’assignateur et l’assigné.',
        'billable' => 'Facturable',
        'billableInfo' => 'Une facture pourra être générée à partir du temps passé sur cette tâche.',
        'setTimeEstimate' => 'Estimation de durée',
        'estimateTime' => 'Durée estimée',

        'deleteRecurringTasks' => 'Supprimer aussi les tâches récurrentes',
        'repeatInfo' => 'La tâche sera recréée automatiquement tous les X jours.',
        'cyclesToolTip' => 'Nombre de répétitions',
        'preDeadlineReminder' => 'Envoyer un rappel <strong>avant</strong> X jours de l’échéance',
        'onDeadlineReminder' => 'Envoyer un rappel le jour de l’échéance',
        'postDeadlineReminder' => 'Envoyer un rappel <strong>après</strong> X jours de l’échéance',

        'createActivity' => 'Tâche créée par',
        'updateActivity' => 'Tâche modifiée par ',
        'statusActivity' => 'Statut modifié par ',
        'commentActivity' => 'Commentaire ajouté par ',
        'fileActivity' => 'Fichier ajouté par ',
        'subTaskCreateActivity' => 'Sous-tâche créée par ',
        'subTaskUpdateActivity' => 'Sous-tâche modifiée par ',

        'timerStartedTask' => 'Minuteur démarré pour la tâche ',
        'timerStartedProject' => 'Minuteur démarré pour la diligence ',
        'timerStartedBy' => 'Minuteur démarré',
        'timerStoppedBy' => 'Minuteur arrêté',
        'timerPausedBy' => 'Minuteur mis en pause',

        'like' => 'J’aime',
        'dislike' => 'Je n’aime pas',
        'likeUser' => ':user a réagi par un pouce levé',
        'dislikeUser' => ':user a réagi par un pouce baissé',

        'leaveMessage' => 'Un ou plusieurs assignés sont en congé aux dates sélectionnées.',
        'leaveOn' => 'est en congé',

        /* Section Notifications du formulaire de tache : la case activant
           l'envoi, puis le selecteur des destinataires qu'elle revele.
           Ces quatre cles manquaient : les vues create et edit affichaient
           le chemin brut modules.tasks.* a la place du libelle. */
        'informCollaborator' => 'Informer les collaborateurs',
        'selectCollaborator' => 'Sélectionner un collaborateur',
        'informClient' => 'Informer le client',
        'selectClient' => 'Sélectionner un client',
    ],

    /* ================================================================== */
    /* Module Diligences (projects)                                        */
    /* ================================================================== */

    'projects' => [
        'addNewProject' => 'Ajouter une diligence',
        'createTitle' => 'Ajouter une diligence',
        'updateTitle' => 'Modifier la diligence',
        'allProject' => 'Toutes les diligences',
        'projectName' => 'Nom de la diligence',
        'projectSummary' => 'Résumé de la diligence',
        'projectDetails' => 'Détails de la diligence',
        'projectInfo' => 'Informations sur la diligence',
        'projectCategory' => 'Catégorie de diligence',
        'manageProjectCategory' => 'Gérer les catégories',
        'projectProgress' => 'Avancement de la diligence',
        'projectCompletionStatus' => 'Taux d’achèvement',
        'projectStatusSettings' => 'Paramètres des statuts de diligence',
        'hideFinishedProjects' => 'Masquer les diligences terminées',
        'withoutDeadline' => 'Aucune date limite définie',
        'deadline' => 'Date limite',
        'startDate' => 'Date de début',
        'daysLeft' => 'Jours restants',
        'taskCount' => 'Nombre de tâches',
        'openTasks' => 'Tâches ouvertes',
        'issuesPending' => 'Incidents en attente',
        'hoursLogged' => 'Heures enregistrées',
        'overview' => 'Vue d’ensemble',
        'note' => 'Notes',

        'selectClient' => 'Sélectionner un client',
        'clientInfo' => 'Informations client',
        'projectClient' => 'Détails du client de la diligence ',
        'projectDoNotHaveClient' => 'Cette diligence n’a aucun client associé',
        'clientFeedback' => 'Retour du client',
        'clientViewTask' => 'Le client peut gérer les tâches de cette diligence',
        'clientTaskNotification' => 'Envoyer les notifications de tâche au client',

        'members' => 'Membres',
        'projectMembers' => 'Membres de la diligence',
        'addMemberTitle' => 'Ajouter des membres',
        'chooseMembers' => 'Choisir les membres',
        'chooseDepartment' => 'Choisir un département',
        'selfAssignAsProjectMember' => 'M’ajouter comme membre',
        'sameAssignee' => 'Reprendre les mêmes assignés',
        'projectAdminInfo' => 'Le responsable de diligence peut ajouter des membres, tâches, discussions, notes, fichiers et jalons',
        'needApproval' => 'La tâche doit être validée par l’administrateur ou le responsable de diligence',

        'files' => 'Fichiers',
        'fileName' => 'Nom du fichier',
        'uploadFile' => 'Téléverser un fichier',
        'dropFile' => 'Déposez vos fichiers ici OU cliquez pour les téléverser',
        'addFileLink' => 'Ajouter un lien vers un fichier',
        'discussion' => 'Discussion',
        'activityTimeline' => 'Journal d’activité',

        'activeTimers' => 'Minuteurs actifs',
        'whoWorking' => 'Qui travaille',
        'activeSince' => 'Actif depuis',
        'manualTimelog' => 'Autoriser la saisie manuelle du temps',
        'calculateTasksProgress' => 'Calculer l’avancement à partir des tâches',

        'milestones' => 'Jalons',
        'createMilestone' => 'Créer un jalon',
        'editMilestone' => 'Modifier le jalon',
        'milestoneTitle' => 'Titre du jalon',
        'milestoneSummary' => 'Résumé du jalon',
        'milestoneCost' => 'Coût du jalon',
        'milestoneStartDate' => 'Date de début du jalon',
        'milestoneEndDate' => 'Date de fin du jalon',

        'budgetInfo' => 'Informations budgétaires',
        'projectBudget' => 'Budget de la diligence',
        'addCostProjectBudget' => 'Ajouter le coût au budget',
        'hours_allocated' => 'Estimation (en heures)',
        'expenses_total' => 'Dépenses',
        'projectExpenseInfo' => 'Calculé depuis le module Dépenses',
        'projectEarningInfo' => 'Calculé depuis le module Paiements',
        'profit' => 'Bénéfice',
        'otherInfo' => 'Autres informations',
        'selectPayment' => 'La passerelle de paiement est obligatoire lorsque le paiement est activé',

        'viewGanttChart' => 'Diagramme de Gantt',
        'viewPublicGanttChart' => 'Diagramme de Gantt public',
        'burndownChart' => 'Graphique d’avancement',

        'rating' => 'Évaluation',
        'noRatingAvailable' => 'Aucune évaluation disponible',
        'visibleRatingToMembers' => 'Évaluation visible par les membres',

        'createPublicProject' => 'Créer une diligence publique',
        'createPrivateProject' => 'Rendre la diligence privée',
        'changeToPublicProject' => 'Rendre la diligence publique',

        'pause' => 'Suspendre',
        'resume' => 'Reprendre',
        'cancel' => 'Annuler',
        'activate' => 'Activer',
        'updateSuccess' => ' détails de la diligence mis à jour.',

        'enableMiroboard' => 'Activer Miroboard',
        'miroBoardId' => 'Identifiant du tableau Miro',
        'clientMiroAccess' => 'Le client peut accéder à Miro ',
    ],

    /* ================================================================== */
    /* Module Contrats                                                     */
    /* ================================================================== */

    'contracts' => [
        'createContract' => 'Créer un contrat',
        'contractName' => 'Nom du contrat',
        'contractNumber' => 'Numéro de contrat',
        'contractValue' => 'Valeur du contrat',
        'totalContracts' => 'Total des contrats',
        'subject' => 'Objet',
        'description' => 'Description',
        'summery' => 'Résumé',
        'notes' => 'Notes',
        'endDate' => 'Date de fin',
        'noEndDate' => 'Aucune date de fin',
        'noValue' => 'Aucune valeur',
        'setZero' => 'Indiquez 0 si le contrat n’a pas de coût.',

        'contractType' => 'Type de contrat',
        'createContractType' => 'Créer un type de contrat',
        'addContractType' => 'Ajouter un type de contrat',
        'manageContractType' => 'Gérer les types de contrat',

        'active' => 'Actif',
        'expired' => 'Expiré',
        'aboutToExpire' => 'Bientôt expiré',

        'renewContract' => 'Renouveler le contrat',
        'renewedThisContract' => 'a renouvelé ce contrat',
        'contractRenewalHistory' => 'Historique des renouvellements',
        'newStartDate' => 'Nouvelle date de début',
        'newEndDate' => 'Nouvelle date de fin',
        'newAmount' => 'Nouvelle valeur du contrat',
        'keepSignature' => 'Conserver la signature du client',

        'discussion' => 'Discussion',
        'addComment' => 'Ajouter un commentaire',
        'editDiscussion' => 'Modifier la discussion',
        'discussionAdded' => 'Discussion ajoutée.',
        'discussionUpdated' => 'Discussion mise à jour.',
        'discussionDeleted' => 'Discussion supprimée.',

        'companyLogo' => 'Logo de l’entreprise',
        'alternateAddress' => 'Adresse alternative',
        'streetAddress1' => 'Adresse ligne 1',
        'streetAddress2' => 'Adresse ligne 2',
    ],

    /* ================================================================== */
    /* Tableau de bord                                                     */
    /* ================================================================== */

    'dashboard' => [
        'dashboardWidgets' => 'Widgets du tableau de bord',
        'dashboardWidgetsSettings' => 'Paramètres des widgets',
        'profile' => 'Profil',
        'thisMonth' => 'Ce mois-ci',

        // Taches et diligences
        'tasks' => 'Tâches',
        'myTask' => 'Mes tâches',
        'totalPendingTasks' => 'Tâches en attente',
        'totalCompletedTasks' => 'Tâches terminées',
        'overdueTasks' => 'Tâches en retard',
        'privateTaskInfo' => 'Les tâches privées ne sont pas comptées',
        'dueDate' => 'Échéance',
        'projects' => 'Diligences',
        'totalProject' => 'Total des diligences',
        'totalProjects' => 'Total des diligences',
        'newProjects' => 'Nouvelles diligences',
        'totalArchiveProjects' => 'Diligences archivées',
        'totalOverdueProject' => 'Diligences en retard',
        'statusWiseProject' => 'Diligences par statut',
        'pendingMilestone' => 'Jalons en attente',
        'projectActivityTimeline' => 'Journal d’activité des diligences',
        'userActivityTimeline' => 'Journal d’activité des utilisateurs',
        'freeEmployees' => 'Sans diligence en cours',
        'totalPendingIssues' => 'Total des incidents en attente',
        'pendingClientIssues' => 'Incidents en attente',

        // Contrats
        'totalContractsSigned' => 'Contrats signés',
        'totalContractsGenerated' => 'Contrats générés',
        'contractDate' => 'Date du contrat',

        // Temps
        'timelogs' => 'Temps passé',
        'weekTimelog' => 'Temps de la semaine',
        'totalHoursLogged' => 'Heures enregistrées',
        'clientWiseTimelogs' => 'Temps par client',

        // Clients et prospects
        'totalClients' => 'Total des clients',
        'newClients' => 'Nouveaux clients',
        'totalActiveClients' => 'Clients actifs',
        'latestClient' => 'Derniers clients',
        'clientFeedback' => 'Retours clients',
        'lead' => 'Prospects',
        'totalLeads' => 'Total des prospects',
        'newLead' => 'Nouveau prospect',
        'totalConvertedClient' => 'Prospects convertis en clients',
        'totalLeadConversions' => 'Affaires converties',
        'leadVsStatus' => 'Affaires par étape et pipeline',
        'leadVsSource' => 'Prospects par origine',
        'followUpDate' => 'Date de relance',
        'nextFollowUp' => 'Prochaine relance',
        'pendingFollowUp' => 'Relance en attente',
        'totalPendingFollowUps' => 'Relances en attente',
        'totalDeals' => 'Total des affaires',

        // Employes et RH
        'totalEmployees' => 'Total des employés',
        'totalNewEmployee' => 'Nouveaux employés',
        'totalEmployeeExits' => 'Départs',
        'headcount' => 'Effectif',
        'joiningVsAttrition' => 'Arrivées / Départs',
        'departmentWiseEmployee' => 'Employés par département',
        'designationWiseEmployee' => 'Employés par fonction',
        'genderWiseEmployee' => 'Employés par genre',
        'roleWiseEmployee' => 'Employés par rôle',
        'birthday' => 'Anniversaires',
        'workAnniversary' => 'Anniversaires d’ancienneté',
        'joinedToday' => 'Arrivé aujourd’hui',
        'inDays' => 'dans :days jours',
        'inDay' => 'dans :days jour',
        'appreciation' => 'Reconnaissances',
        'noticePeriodDuration' => 'Durée du préavis',
        'probationDate' => 'Fin de période d’essai',
        'internshipDate' => 'Fin de stage',
        'shiftSchedule' => 'Planning des équipes',

        // Presence et conges
        'totalTodayAttendance' => 'Présences du jour',
        'averageAttendance' => 'Présence moyenne',
        'lateAttendanceMark' => 'Retards',
        'lateMark' => 'Retard',
        'leave' => 'En congé aujourd’hui',
        'leavesTaken' => 'Congés pris',
        'totalLeavesApproved' => 'Congés approuvés',
        'settingsLeaves' => 'Calendrier des congés',
        'workFromHome' => 'En télétravail aujourd’hui',
        'fullDay' => 'Journée entière',
        'holidayCheck' => 'Aujourd’hui est férié pour',

        // Finance
        'totalEarnings' => 'Revenus totaux',
        'recentEarnings' => 'Revenus récents',
        'totalExpenses' => 'Dépenses totales',
        'totalProfit' => 'Bénéfice total',
        'totalPaidAmount' => 'Montant encaissé',
        'totalPendingAmount' => 'Montant en attente',
        'totalOutstandingAmount' => 'Montant restant dû',
        'earningsByClient' => 'Revenus par client',
        'earningsByProjects' => 'Revenus par diligence',
        'clientWiseEarnings' => 'Revenus par client',
        'lastReceivedPayments' => 'Derniers paiements reçus',
        'totalPaidInvoices' => 'Factures réglées',
        'totalUnpaidInvoices' => 'Factures dues',
        'invoiceTab' => 'Factures',
        'estimateTab' => 'Devis',
        'expenseTab' => 'Dépenses',
        'paymentTab' => 'Paiements',
        'duePaymentsTab' => 'Paiements dus',
        'proposalTab' => 'Propositions',
        'invoiceOverview' => 'Aperçu des factures',
        'invoiceDraft' => 'Brouillon',
        'invoiceNotSent' => 'Non envoyée',
        'invoiceUnpaid' => 'Impayée',
        'invoicePartiallyPaid' => 'Partiellement payée',
        'invoiceOverdue' => 'En retard',
        'invoicePaid' => 'Payée',
        'estimateOverview' => 'Aperçu des devis',
        'estimateDraft' => 'Brouillon',
        'estimateNotSent' => 'Non envoyé',
        'estimateSent' => 'Envoyé',
        'estimateExpired' => 'Expiré',
        'estimateDeclined' => 'Refusé',
        'estimateAccepted' => 'Accepté',
        'proposalOverview' => 'Aperçu des propositions',
        'proposalWaiting' => 'En attente',
        'proposalConverted' => 'Convertie',
        'proposalDeclined' => 'Refusée',
        'proposalAccepted' => 'Acceptée',
        'proposalExpired' => 'Expirée',

        // Tickets
        'ticket' => 'Tickets',
        'totalTickets' => 'Tickets',
        'openTickets' => 'Tickets ouverts',
        'totalOpenTickets' => 'Tickets ouverts',
        'newTickets' => 'Tickets ouverts',
        'totalResolvedTickets' => 'Tickets résolus',
        'totalUnresolvedTickets' => 'Tickets non résolus',
        'totalUnassignedTicket' => 'Tickets non assignés',
        'typeWiseTicket' => 'Tickets par type',
        'statusWiseTicket' => 'Tickets par statut',
        'channelWiseTicket' => 'Tickets par canal',

        // Divers
        'notices' => 'Annonces',
        'myCalender' => 'Mon calendrier',
        'newproduct' => 'Nouveau produit',
        'addNewProduct' => 'Ajouter un produit',
        'recentLoginActivities' => 'Connexions récentes',
        'weatherSetLocation' => 'Définissez votre position pour voir la météo',
        'verificationPending' => 'Vérification du compte en attente',
        'verificationPendingInfo' => 'La vérification de votre compte est en attente auprès du propriétaire.',
    ],

    /* ================================================================== */
    /* Roles et permissions                                                */
    /* ================================================================== */

    'permission' => [
        'permissions' => 'Permissions',
        'roleName' => 'Nom du rôle',
        'addMembers' => 'Ajouter des membres',
        'addRoleMember' => 'Gérer les membres du rôle',
        'importFromRole' => 'Importer depuis un rôle',
        'selectAll' => 'Tout sélectionner',
        'deselectAll' => 'Tout désélectionner',
        'projectNote' => 'Un utilisateur peut consulter les informations de base des diligences qui lui sont affectées, même sans permission.',
        'taskNote' => 'Un utilisateur peut consulter les tâches qui lui sont assignées, même sans permission.',
        'attendanceNote' => 'Un utilisateur peut consulter sa propre présence, même sans permission.',
        'ticketNote' => 'Un utilisateur peut consulter les tickets qu’il a créés, même sans permission.',
        'eventNote' => 'Un utilisateur peut consulter les événements auxquels il participe, même sans permission.',
        'holidayNote' => 'Un utilisateur peut consulter les jours fériés, même sans permission.',
    ],

    /* ================================================================== */
    /* Liste de demarrage                                                  */
    /* ================================================================== */

    'checklist' => [
        'checklistInfo' => 'Voici une liste d’étapes pour vous aider à démarrer rapidement.',
        'installation' => 'Installation',
        'installationInfo' => 'Déposer les fichiers sur le serveur et configurer la base de données.',
        'accountSetup' => 'Configuration du compte',
        'accountSetupInfo' => 'Renseigner les informations du compte administrateur.',
        'companyLogo' => 'Ajouter le logo de l’entreprise',
        'companyLogoInfo' => 'Ce logo apparaît à la connexion, à l’inscription et dans le menu latéral.',
        'favicon' => 'Ajouter le favicon',
        'faviconInfo' => 'Cette icône s’affiche à côté du titre de la page dans le navigateur.',
        'profileImage' => 'Ajouter une photo de profil',
        'profileImageInfo' => 'Ajoutez votre photo de profil.',
        'emailSetup' => 'Configurer les paramètres e-mail',
        'configureEmailSetting' => 'La configuration e-mail est nécessaire pour notifier les utilisateurs des diligences, tâches, etc.',
        'crontSetup' => 'Configurer la tâche planifiée (cron)',
        'cronSetupInfo' => 'La tâche planifiée est nécessaire aux rappels et aux traitements en arrière-plan.',
    ],

    'client' => [
        'profileInfo'    => 'Informations du dossier',
        'companyName'    => 'Raison sociale',

        'salutation'               => 'Civilité',
        'cell'                     => 'Téléphone portable',
        'officePhoneNumber'        => 'Téléphone du bureau',
        'clientCanLogin'           => 'Connexion autorisée ?',
        'clientOtherDetails'       => 'Autres informations',
        'generateRandomPassword'   => 'Générer un mot de passe aléatoire',
    ],

    'employees' => [
        'profile'        => 'Profil',
        'fullName'       => 'Nom complet',

        'addNewEmployee'           => 'Ajouter un employé',
        'accountDetails'           => 'Informations du compte',
        'employeeName'             => 'Nom de l\'employé',
        'employeeEmail'            => 'E-mail de l\'employé',
        'employeeId'               => 'Matricule',
        'employeeIdInfo'           => 'Le matricule est l\'identifiant unique attribué à chaque employé',
        'employeeIdHelp'           => '<p>Identifiant unique qui vous sert à repérer vos employés au sein du cabinet.</p><p>Pour les employés créés par <b>Invitation</b>, un matricule est généré automatiquement ; vous pourrez le modifier ensuite.</p>',
        'gender'                   => 'Sexe',
        'dateOfBirth'              => 'Date de naissance',
        'maritalStatus'            => 'Situation familiale',
        'marriageAnniversaryDate'  => 'Date d\'anniversaire de mariage',
        'reportingTo'              => 'Responsable hiérarchique',
        'employmentType'           => 'Type de contrat',
        'joiningDate'              => 'Date d\'entrée',
        'hourlyRate'               => 'Taux horaire',
        'probationEndDate'         => 'Fin de la période d\'essai',
        'noticePeriodStartDate'    => 'Début du préavis',
        'noticePeriodEndDate'      => 'Fin du préavis',
        'contractEndDate'          => 'Date de fin de contrat',
        'internshipEndDate'        => 'Date de fin de stage',
        'slackUsername'            => 'Identifiant Slack',
    ],

    'emailSettings' => [
        'emailNotifications'       => 'Recevoir les notifications par e-mail ?',
    ],

    'profile' => [
        'companyLogo'    => 'Logo de la société',

        'profilePicture'           => 'Photo de profil',
    ],

    'invoices' => [
        'currency'                 => 'Devise',
    ],

    'timeLogs' => [
        'endDate'                  => 'Date de fin',
    ],

    'taskCategory' => [
        'addTaskCategory'          => 'Ajouter une catégorie',
    ],

    'taskLabel' => [
        'addLabel'                 => 'Ajouter une étiquette',
    ],

    'events' => [
        'repeat'                   => 'Répéter',
        'repeatEvery'              => 'Répéter tous les',
        'cycles'                   => 'Cycles',
    ],

    'stripeCustomerAddress' => [
        'city'                     => 'Ville',
        'state'                    => 'Région',
        'country'                  => 'Pays',
        'postalCode'               => 'Code postal',
    ],

];
