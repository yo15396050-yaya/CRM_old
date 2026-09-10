# État de la refonte Taxflow

Vérification du document `taskflow_refonte.txt` contre le code réel du projet.
Chaque ligne a été contrôlée dans les fichiers ou dans la base, pas déduite.

Date du contrôle : 3 septembre 2026
Branche : `feat/refonte-front-contrats`

---

## Résumé

| État | Nombre |
|---|---|
| Déjà en place | 11 points |
| Reste à faire | 15 points |

Trois points de la spécification étaient **déjà satisfaits avant même de commencer** :
la colonne « En test », le renommage « En attente d'approbation », et l'article 62ᵉ
en exposant. Un quatrième — l'entité « Services » — existe sous un autre nom.

---

## 1. Interface et traductions

| Point | État | Détail |
|---|---|---|
| Assign to → Assigné à | ✅ | `modules.tasks.assignTo` = « Assignée à » |
| Category → Catégorie | ✅ | `app.category` |
| Created on → Créé le | ✅ | `app.createdOn` |
| Tags → Étiquettes | ❌ | clé `app.tags` absente |
| Créé le + date **et heure** | ❌ | seule la date s'affiche |
| Login hello → Connexion | ❌ | à vérifier écran par écran |
| Company signature, Your name | ❌ | non traités |
| 2FA en français | ❌ | interface encore en anglais |
| Signature électronique | ❌ | « Upload signature » non traduit |

**Point d'attention** — le dossier `resources/lang/fr` était exclu de git
(`.gitignore` ligne 29). Il est maintenant suivi : sans cette correction,
toute la francisation serait restée sur le poste local.

---

## 2. Recherche

| Point | État | Détail |
|---|---|---|
| Placeholder « Commencer à saisir pour rechercher » | ❌ | absent de la barre |
| Recherche simultanée Diligences + Clients + Tâches | ❌ | à construire |
| Bouton « Avancé » | ❌ | non renommé |

`SearchController` fait 44 lignes et n'est **pas** une recherche globale : c'est
un aiguilleur qui redirige vers la liste d'un module choisi au préalable, avec
le mot-clé en paramètre d'URL. Six de ses dix branches pointent vers des routes
désactivées (tickets, factures, annonces, avoirs, devis, prospects) et
produiraient une erreur si elles étaient atteintes.

La recherche croisée est donc un développement neuf.

---

## 3. Tableau de bord

KPI actuellement affichés : **Tâches en cours**, **Diligences actives**,
**Contrats en vigueur**.

| KPI demandé | État |
|---|---|
| Volume mensuel (en cours / total du mois) | ❌ |
| Taux de clôture, diligences exclues | ❌ |
| Backlog | ❌ |
| Employés actifs | ❌ retiré sur demande, à remettre |
| Nombre de clients | ❌ |

| Autre point | État | Détail |
|---|---|---|
| Module « Tâches à surveiller » | ⚠️ | le bloc existe, le renommage et la restriction aux tâches restent à faire |
| Retirer Activité récente, Factures, Dépenses, Situation fiscale | ✅ | neutralisés dans `dashboard/ajax/overview.blade.php` |

---

## 4. Mon Espace

| Point | État | Détail |
|---|---|---|
| Renommage « Mon Espace » | ✅ | la vue s'appelle déjà `mon-espace` |
| Retirer « Annonces » | ❌ | encore présente |
| Retirer « Semaines » | ❌ | encore présente |
| Retirer « Diligences » | ❌ | encore présente |
| Limiter à 5 tâches | ❌ | pas de limite |
| Bouton « Voir tout » vers le Kanban | ❌ | absent |

À noter : les indicateurs de congés et de présence ont été retirés de cet écran
et du tableau de bord administrateur, et le calcul mort correspondant
(`espaceCongesRestants`, 3 requêtes à chaque ouverture) a été supprimé.

---

## 5. Tâches et Kanban

| Point | État | Détail |
|---|---|---|
| Colonne « En test » | ✅ | existe déjà, slug `en_test` |
| Renommage « En attente d'approbation » | ✅ | libellé déjà en base |
| Position de la colonne | ❌ | ordre actuel : En cours → En test → **Terminée** → En attente d'approbation |
| Règle admin, **backend** | ✅ | 4 points d'entrée protégés |
| Règle admin, **interface** | ✅ | option retirée de l'action groupée |
| Afficher les noms « Assigné à / par » | ❌ | avatars encore utilisés |
| Retirer le « Short code » | ❌ | encore affiché |
| Fusionner Note et Commentaire | ❌ | deux onglets distincts |
| Masquer les statuts RH des cartes | ❌ | non traité |

### Faille de sécurité corrigée

`TaskController::sendApproval` déplaçait une tâche vers « En attente
d'approbation » **sans aucun contrôle** : ni permission, ni appartenance.
N'importe quel compte connecté pouvait y envoyer n'importe quelle tâche en
passant son identifiant.

La règle est désormais portée par `app/Traits/ApprobationTache.php` et appliquée
à quatre endroits :

| Point d'entrée | Rôle |
|---|---|
| `changeStatus` | tableau et bouton de statut |
| `update` | formulaire d'édition |
| `sendApproval` | demande d'approbation |
| `storeStatusReason` | sortie avec motif |

Vérifié avec de vraies données, employé non-administrateur contre administrateur :

| Acteur | Transition | Résultat |
|---|---|---|
| Employé | En cours → Approbation | **403** |
| Employé | Approbation → En cours | **403** |
| Employé | Demande sur une tâche qui ne le concerne pas | **403** |
| Employé | En cours → En cours | autorisé |
| Administrateur | En cours → Approbation | autorisé |
| Administrateur | Approbation → En cours | autorisé |

**Choix assumé** : le flux « envoyer pour approbation » reste ouvert aux
collaborateurs assignés. La spécification dit « seul l'administrateur peut
déplacer vers cette colonne », mais fermer ce flux aurait cassé le bouton
*Envoyer pour approbation* que les employés utilisent quand la diligence
l'exige. L'employé peut donc **demander**, plus **arbitrer**.

---

## 6. Notifications de changement de statut

Le mécanisme existait entièrement mais était **désactivé** : dans
`TaskObserver`, les trois `event(new TaskEvent(...))` étaient commentés depuis
le commit `29e2f71` du 25 mars 2026. Aucune notification de statut ne partait
plus, alors que le réglage « Task Status Changed » est à `mail = yes` et que
26 employés sur 27 ont les notifications activées.

### Règle retenue : notifier les décisions, pas la progression

Notifier chaque changement produirait **4 courriels pour une seule tâche**
(prise en charge, test, envoi en validation, validation). Avec 32 tâches en
cours, les destinataires filtrent tout au bout d'une semaine — y compris le
message qui comptait.

**On écrit seulement quand le destinataire doit réagir :**

| Passage | Destinataire | Pourquoi |
|---|---|---|
| → En attente d'approbation | l'administrateur | il doit arbitrer |
| sortie d'approbation | l'assigné | verdict sur son travail |
| → Incomplète | l'assigné | il doit reprendre |
| → Terminée par un tiers | l'assigné | sa tâche a été clôturée sans lui |

**Silence complet** sur À faire, En cours et En test : c'est de la progression
normale, lisible sur le tableau.

**Règle la plus importante : on ne prévient jamais l'auteur du geste.** Elle
n'existait pas — le code excluait `added_by`, le *créateur* de la tâche, et non
l'auteur du changement. Un collaborateur déplaçant sa propre tâche recevait donc
un courriel sur son propre clic.

Le réglage existant par diligence (`allow_collaborator_notification`) reste
respecté.

Implémenté dans `app/Traits/NotificationStatutTache.php`.
**Non encore testé en conditions réelles** : le test automatisé n'a pas abouti
dans le temps imparti. À vérifier avant mise en production.

---

## 7. Diligences

| Point | État |
|---|---|
| Retirer dates, client, membres, statut, avancement | ❌ |
| Simplifier la liste, ne garder que la recherche | ❌ |

### Pourquoi ce point est risqué

Ces champs ne servent pas qu'à l'affichage. Exemple mesuré dans
`ProjectController.php:420` :

```php
$projectTask->due_date = $deadline;   // date limite de la diligence
```

La date limite d'une diligence **devient l'échéance des tâches qu'elle génère**.
La retirer prive ces tâches d'échéance : elles disparaissent des retards et du
futur KPI « backlog ».

Dépendances comptées : `deadline` 20 usages, `status` 16 usages,
`completion_percent` 8 usages plus la barre d'avancement dans 3 vues.

**Recommandation** : retirer les champs de la fiche uniquement, en conservant le
calcul en arrière-plan. La diligence devient une catégorie à l'écran sans casser
l'héritage d'échéance ni les rapports.

---

## 8. Clients et contrats

| Point | État | Détail |
|---|---|---|
| Article 62ᵉ en exposant | ✅ | `62<sup>e</sup>` déjà correct dans le PDF |
| En-tête « Ministère de l'Économie… » | ⚠️ | mention présente, libellé exact à confirmer |
| Relecture orthographique des modèles | ❌ | 2 modèles en `longtext` HTML |
| Bouton « Retour » sur les fiches | ❌ | absent |
| Isolation du portail client | ❌ | non traité |

L'isolation client est le chantier le plus lourd restant : 99 gardes de rôle
dispersées dans les contrôleurs, un `CompanyScope` global, et 392 comptes
clients à couvrir.

---

## 9. Rapports

| Point | État |
|---|---|
| Légende explicite sur les graphiques | ❌ |
| Titres techniques précis | ❌ |
| Nom complet dans la colonne « Assigné à » | ❌ |

---

## 10. Menu de navigation

✅ **Conforme à la demande.**

```
Pilotage    Tableau de bord
Travail     Tâches · Diligences · Employés
Gestion     Contrats · Clients
Analyse     Rapports
Système     Paramètres
```

Cinq entrées ont été retirées à la demande (Permissions et congés, Ma présence,
Annonces, Services et produits, Situations fiscales). Elles sont commentées et
non supprimées : les réactiver tient en deux lignes, et les écrans restent
accessibles par leur URL.

---

## 11. Module « Services »

✅ **L'entité existe déjà — ne rien créer.**

La table `teams` porte : Direction, Ressource Humain, Assistance Comptable,
Transformation Digitale, Fiscalité, Levée de Fonds, Formation, Apporteurs CGA.
La liaison employé ↔ service existe dans `employee_teams`, et
`project_departments` relie déjà des diligences.

**Piège à éviter** : une table `services` existe aussi, mais c'est le catalogue
de prestations vendues (Comptabilité, Fiscalité…) avec prix et catégorie. Créer
une seconde table `services` pour l'organigramme entrerait en collision avec
elle.

L'assignation d'une tâche à un service se construira donc sur `teams`.

---

## 12. Profil et sécurité

| Point | État | Détail |
|---|---|---|
| Mot de passe vide par défaut | ✅ | le champ n'a aucun attribut `value` |
| Conserver si vide, mettre à jour sinon | ✅ | `EmployeeController:454` et `UpdateRequest:60` |
| 2FA en français | ❌ | à traduire |

Les trois niveaux étaient déjà corrects : interface, validation et contrôleur.

---

## 13. Assainissement du dépôt

Deux corrections faites avant tout développement.

**Copie du projet retirée du suivi.** `Documents/CRM/` contenait une seconde
copie complète — 3 104 fichiers versionnés, 69 Mo, figée au 13 mars 2026 et
jamais resynchronisée : `dck-flow.css` n'y existait même pas. Une modification y
avait déjà été écrite par erreur au lieu du projet réel.

Le dossier reste sur le disque et l'historique git le conserve
(`git checkout b256825 -- Documents/CRM`). Les 169 autres fichiers de
`Documents/` — bases Excel, documents métier — restent versionnés.

**Langue française versionnable.** `!resources/lang/fr/` ajouté au `.gitignore`,
sur le modèle déjà utilisé pour l'anglais.

---

## Ce que je propose ensuite

Par ordre d'utilité décroissante :

1. **Isolation du portail client** — le seul point restant dont un défaut expose
   des données d'un client à un autre.
2. **Les cinq KPI du tableau de bord** — visible immédiatement, sans risque.
3. **Mon Espace** — retraits et limite à 5 tâches, sans risque.
4. **Fiche de tâche** — noms explicites, retrait du short code, fusion des notes.
5. **Recherche croisée** — développement neuf, à cadrer.
6. **Diligences** — à traiter en dernier, après décision sur les dépendances.
