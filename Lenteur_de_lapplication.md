# Pourquoi l'application était lente

*Relevé du 27 août 2026 — mesures faites sur 89 requêtes réelles.*

---

## Le constat qui a tout orienté

Quand une application est lente, le premier réflexe est d'accuser la base de
données. Ici, les mesures ont dit le contraire.

Créer une diligence prenait **47 secondes**. Sur ces 47 secondes, la base de
données n'en utilisait que **0,3**. Tout le reste se passait ailleurs.

Autre exemple : la page d'accueil mettait 4,2 secondes à s'afficher **sans
poser une seule question à la base**.

Optimiser les requêtes SQL — le travail auquel on pense d'abord — n'aurait donc
presque rien changé. Le problème était ailleurs, et il tenait en trois causes.

---

## Cause n° 1 — L'application attendait le facteur

**Ce qui se passait**

Quand vous créiez une diligence, l'application envoyait les e-mails de
notification **pendant que vous attendiez devant votre écran**. Elle contactait
le serveur de messagerie, attendait sa réponse, passait au destinataire
suivant, et ainsi de suite.

Chaque e-mail prenait une dizaine de secondes. Une diligence qui prévient
quatre personnes bloquait donc l'écran **quarante secondes**.

**L'image**

C'est comme si, en déposant une lettre à la poste, vous deviez attendre au
guichet que le facteur revienne vous confirmer qu'il l'a bien remise. Vous
n'avez rien à faire de cette confirmation, mais vous attendez quand même.

**Ce qui a été fait**

Les e-mails sont désormais déposés dans une file d'attente. Un programme
séparé s'en occupe en arrière-plan pendant que vous continuez à travailler.
Vous déposez la lettre et vous repartez.

> **Résultat : notifier une diligence est passé de 42 secondes à 0,13 seconde.**

---

## Cause n° 2 — Le serveur relisait tout, à chaque fois

**Ce qui se passait**

Une application comme la vôtre est faite de plusieurs milliers de fichiers de
code. À chaque page demandée, le serveur les relisait **tous** et les
retraduisait dans un langage qu'il comprend — avant même de commencer le
moindre travail utile.

PHP sait éviter cela : il garde en mémoire le code déjà traduit. Cette
fonction s'appelle OPcache. Sur votre machine, **elle n'avait jamais été
activée** : toutes les lignes de configuration correspondantes étaient
désactivées.

**L'image**

C'est un cuisinier qui, avant chaque plat, relirait le livre de recettes
entier — y compris les recettes qu'il ne va pas préparer. Il finit par les
connaître, mais on ne lui a jamais permis de s'en souvenir.

**Ce qui a été fait**

OPcache a été activé, avec 256 Mo de mémoire. Le serveur garde maintenant en
tête le code déjà lu. Un réglage a été ajouté pour qu'il remarque quand même
les modifications de code — indispensable pendant le développement.

> **Résultat : le temps passé hors base de données a été divisé par deux à
> trois sur toutes les pages.**

---

## Cause n° 3 — Un serveur qui traitait une demande à la fois

**Ce qui se passait**

Vous travailliez sur l'adresse `127.0.0.1:8001`, servie par un petit serveur
de dépannage fourni avec le langage. Il a un défaut majeur : il traite les
demandes **une par une**.

Or une page ne demande pas une seule chose : elle réclame aussi ses feuilles
de style, ses scripts, ses images — dix-sept fichiers au total. Chacun devait
attendre son tour.

Ce serveur ignorait par ailleurs le fichier de réglages qui active la
**compression**. Le fichier de code principal partait donc en 4,55 Mo au lieu
d'être compressé.

Pendant ce temps, Apache — un vrai serveur, capable de tout traiter en
parallèle — tournait déjà sur votre machine, inutilisé.

**L'image**

Un supermarché avec dix caisses, dont une seule ouverte. Les clients ne sont
pas plus nombreux, mais la file n'avance pas.

**Ce qui a été fait**

Le travail se fait maintenant sur `http://crm.test`, servi par Apache. Les
fichiers partent en parallèle, et la compression — que vous aviez déjà
configurée sans qu'elle puisse fonctionner — est enfin active.

> **Résultat : le fichier principal est passé de 4,55 Mo à 1,12 Mo, soit 75 %
> de moins à télécharger.**

---

## Les résultats, mesurés

| Action | Avant | Après | Gain |
|---|---|---|---|
| Créer une diligence | 47,2 s | **0,9 s** | 53 fois plus rapide |
| Ouvrir le tableau de bord | 5,6 s | **0,3 s** | 18 fois |
| Se connecter | 3,6 s | **0,6 s** | 7 fois |
| Créer une tâche | 22,3 s | **3,8 s** | 6 fois |
| Poids du fichier principal | 4,55 Mo | **1,12 Mo** | −75 % |

**Aucune ligne de code métier n'a été modifiée.** Ces trois causes étaient des
réglages d'environnement : la file d'attente, la mémoire du serveur, et le
serveur web utilisé.

---

## Ce qu'il reste, et pourquoi je m'y suis arrêté

Deux chantiers étaient prévus, et les mesures ont montré qu'ils ne valaient
plus la peine :

- **Alléger le code JavaScript.** La compression a déjà retiré 75 % du poids.
  Le peu qu'il resterait à gagner ne justifie pas de toucher à 37 000 lignes
  de code de thème, avec le risque de casser un écran.

- **Réécrire les requêtes répétitives.** Le tableau de bord est descendu à
  0,3 seconde, les listes à 0,4. Ces cibles ne méritent plus le travail.

Une seule action reste plus lente que les autres : **créer une tâche, à 3,8
secondes**, dont 2 secondes de base de données sur 61 requêtes. C'est
l'opération la plus fréquente de l'application, donc la seule qui vaudrait
encore un examen. Elle reste six fois plus rapide qu'avant.

---

## Deux points d'attention pour la suite

**Le programme d'arrière-plan doit tourner.** C'est lui qui envoie les
e-mails. S'il est arrêté, les notifications s'accumulent sans partir — elles
ne sont pas perdues, elles repartent au redémarrage. La commande est :

```
php artisan queue:work
```

**Un piège évité au passage.** Au moment d'activer la file d'attente, 759
notifications dormaient en base depuis octobre 2024. Les activer telles quelles
aurait envoyé 759 e-mails à vos clients, sur des événements vieux de deux ans.
Elles ont été supprimées après sauvegarde.
