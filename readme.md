Projet : un site internet d'une entreprise de chaudronnerie, tuyauterie et soudure. Axé sur la partie une plateforme de gestion de candidatures.


<!-- ------------------------------- OBJECTIF -------------------------------------- -->
Développement d'un site web en PHP (model MVC) qui permettera :  
-> aux administrateur de gerer les annonces et les recrutements via un dashabord personnalisé. (calendrier, annonces, candidatures).
-> aux candidats de postuler à des annonces et suivre leurs candidatures via un dashboard personnalisé (profil, accés aux annonces, suivi de l'état de la candidature).

<!-- --------------------------- CONFIGURATION DU PROJET  -------------------------------------- -->
Variables à définir dans le fichier .env
Détails du fichier Database.php

<!-- --------------------------- STRUCTURE DU PROJET -------------------------------------- -->

INDUSTRIE
|_ APP
|  |_controller
|  | |_AdministrateurController.php → Gère les actions liées aux recruteurs (création d'annonce, profil, etc.)
|  | |_AnnonceController.php → Liste, création, modification des annonces
|  | |_CandidatureController.php → Traitement des candidatures envoyées par les utilisateurs
|  | |_EntretienController.php → Gestion des entretiens : planification, rappel, affichage
|  | |_UserController.php → CGestion côté candidat (profil, candidature, profil...)
|  |
|  |_model
|  | |_AdministrateurModel.php → Accès aux données des recruteurs
|  | |_AnnonceModel.php → Gestion des annonces en base
|  | |_CandidatureModel.php → Données relatives aux candidatures
|  | |_EntretienModel.php → Accès aux entretiens
|  | |_UtilisateurModel.php → Accès aux données des candidats
|  |
|  |_view
|  | |_Candidat
|  | |_|_profil.php → Affiche les infos du candidat
|  | |_|_annonce.php → 	Affiche les détails d’une annonce
|  | |_|_annonces.php → Liste des annonces disponibles
|  | |_|_candidatures.php → Montre les candidatures du candidat

|  | |_AdministrateurView.php → Interface pour le recruteur
|  | |_UserView.php → Interface côté candidat
|  | |_SharedView.php 
|  |
|  |_Database.php → Classe permettant d’établir une connexion sécurisée à ta base MySQL via PDO. Centralise l’accès aux données.
|
|_ASSETS
| |_css
| | |_btn-retour-haut.scss  → Affiche un bouton qui renvoie en haut de page
| | |_bulle-flottante.scss  → La bulle en bas à droite de l'écran permettant de contacter l'entreprise
| | |_en-tete.scss          → Menu principal
| | |_footer.scss           → Bas de page
| | |_typo.scss             → Les polices importées
| | |_vars.scss             → Les mixins scss et variables
| |
| |_IMAGES
| |
| |_JS
| | |_btn-retour-haut.js    → Le bouton qui permet de remonter en haut de page
| | |_bulle-flotante.js     → La bulle en bas à droite de l'écran, permettant de contacter l'entreprise.
| | |_compteur.js           → Le compteur sur la page d'accueil, année d'expérience/nombre de collaborateurs/CA
| | |_redirection-page-be.js → Permet de naviguer entre les blocs, sur la page Bureau d'étude. (style ONEPAGE)
| | |_redirection-page-expertise.js → Permet de naviguer entre pages selon expertise
| |
| |_TEMPLATES
| | |_bulle-flotante.php → Bulle de coordonnées, situé en bas à droite de l'écran
| | |_footer.php → Bas de page
| | |_head.php → Menu principal
| | |_menu.php → Haut de page
| 
|_PAGES // Pages statiques ou semi-dynamiques
| |_accueil.php → Page d’accueil du site
| |_bureauEtude.php → Présentation d’activités
| |_domaineExpertise.php → Présentation d’activités
| |_recrutement.php → Porte d’entrée vers la plateforme candidat
| |_contact.php → Page de contact 
| |_contact.vcf.php → fichier carte de contact virtuelle
|
|_.env → Fichier des variables d’environnement (connexion BDD, identifiants)
|_.htaccess → Gère les règles Apache (URL rewriting, redirections, sécurité)
|_Docker-compose.yml → Conteneurisation du projet
|_Dockerfile → Conteneurisation du projet
|_nginx.conf → Configuration du serveur 
|_index.php → Point d’entrée du site : initialise le routeur



<!-- --------------------------- TECHNOLOGIES UTILISÉES -------------------------------------- -->

- Frontend : html, scss, javascript
- Backend : php + modele mvc
- Base de donnée : MySql
- Sécurité : hash des mots de passe, requêtes préparées, csef protection


<!-- --------------------------- BASE DE DONNÉES - MySQL -------------------------------------- -->

les tables utilisées : 
- Administrateur : compte du ou des recruteurs
- Utilisateur : compte des candidats
- Annonces : offres d'emploi
- Candidatures : candidatures envoyées
- Entretien : rendez-vous programmés



<!-- --------------------------- INSTALLATION DU PROJET  -------------------------------------- -->

Plateforme → GITHUB Mettre son projet sur GitHub permet de le sauvegarder en ligne, de collaborer facilement avec d'autres personnes et de suivre toutes les modifications du code au fil du temps.

cd ton_dossier_projet/INDUSTRIE

git init                               # Initialise Git dans ton projet
git remote add origin https://github.com/nanou38300/industrie-recrutement.git    // le projet s'appel industrie-recrutement dans github
git add .                              # Ajoute tous les fichiers
git commit -m "Initial commit"         # Premier commit
git branch -M main                     # Nomme la branche principale "main"
git push -u origin main                # Envoie ton projet sur GitHub




<!-- --------------------------- MISE EN PLACE DU ROUTER  -------------------------------------- -->
| 🧭 Action (segment 1)  | 🚪 Étape (segment 2)     | 🎯 Contrôleur                 | 🧩 Méthode                            |
|------------------------|--------------------------|-------------------------------|----------------------------------------|
| `accueil`              |                          | (page statique)               | `Pages/accueil.php`                    |
| `bureauEtude`          |                          | (page statique)               | `Pages/bureauEtude.php`                |
| `domaineExpertise`     |                          | (page statique)               | `Pages/domaineExpertise.php`           |
| `recrutement`          |                          | (page statique)               | `Pages/recrutement.php`                |
| `contact`              |                          | (page statique)               | `Pages/contact.php`                    |

### 👩‍💼 Administration

| `administrateur`       | `dashboard`              | `AdministrateurController`    | `dashboard()`         Affiche les stats globales à l’accueil admin
|                        | `profil`                 |                                | `editProfil()`       Permet à l’admin de modifier ses infos perso
|                        | `annonces`               |                                | `viewAnnonces()`     Liste toutes les offres d’emploi
|                        | `create-annonce`         |                                | `createAnnonce()`	Gère l’affichage + enregistrement d’une nouvelle annonce
|                        | `edit-annonce`           |                                | `editAnnonce(id)` 	Affiche et modifie une annonce existante
|                        | `archive-annonce`        |                                | `archiveAnnonce(id)`	Archive une annonce (sans suppression)
|                        | `candidatures`           |                                | `listCandidatures()`	Montre toutes les candidatures reçues
|                        | `candidature`            |                                | `viewCandidature(id)`	Détail d’une candidature en particulier 

### 📢 Annonces globales

| `annonce`              | `create`                 | `AnnonceController`            | `createAnnonce(id)`                    |
|                        | `update`                 |                                | `updateAnnonce()`                      |
|                        | `delete`                 |                                | `deleteAnnonce(id)`                    |
|                        | *(autre)*                |                                | `listAnnonces()`                       |

### 📄 Candidatures globales

| `candidature`          | `submit`                 | `CandidatureController`        | `submitCandidature()`                  |
|                        | `view`                   |                                | `viewCandidature(id)`                  |
|                        | `delete`                 |                                | `deleteCandidature(id)`                |
|                        | `suivi`                  |                                | `suivi()`                              |
|                        | *(autre)*                |                                | `listCandidatures()`                   |

### 📅 Entretiens

| `entretien`            | `planifier`              | `EntretienController`          | `planifierEntretien()`                 |
|                        | `rappel`                 |                                | `envoyerRappel(id)`                    |
|                        | *(autre)*                |                                | `listEntretiens()`                     |

### 👥 Utilisateurs

| `utilisateur`          | `create`                 | `UtilisateurController`        | `createUtilisateur()`                  |
|                        | `edit`                   |                                | `editUtilisateur(id)`                  |
|                        | `login`                  |                                | `loginUtilisateur(id)`                 |
|                        | `logout`                 |                                | `logoutUtilisateur()`                  |
|                        | `update`                 |                                | `updateUtilisateur()`                  |
|                        | `delete`                 |                                | `deleteUtilisateur(id)`                |
|                        | *(autre)*                |                                | `listUtilisateurs()`                   |

### 📆 Calendrier

| `calendrier`           | `semaine`                | `CalendrierController`         | `vueSemaine()`                         |
|                        | `jour`                   |                                | `vueJour(id)`                          |
|                        | `rappel`                 |                                | `rappelDuJour()`                       |
|                        | `rendez-vous`            |                                | `infoRendezVous(id)`                   |
|                        | *(autre)*                |                                | `vueSemaine()`                         |

### 🧑‍🎓 Candidat (front office)

| `candidat`             | `profil`                 | `CandidatController`           | `profil()`                             |
|                        | `update`                 |                                | `update()`                             |
|                        | `upload-cv`              |                                | `uploadCV()`                           |
|                        | `annonces`               |                                | `listAnnonces()`                       |
|                        | `annonce-view`           |                                | `viewAnnonce(id)`                      |
|                        | `postuler`               |                                | `postuler(id)`                         |
|                        | `candidatures`           |                                | `suiviCandidatures()`                  |
|                        | *(autre)*                |                                | `profil()`                             |





--------------------------------- STRUCTURE DU PROJET --------------------------------------
🔧 1. AnnonceModel.php
Ajout de la méthode getByAdmin(int $idAdmin) pour récupérer les annonces liées à un administrateur.
Amélioration de la méthode create() avec validation des champs obligatoires.
Nettoyage des appels inutiles à id_annonce dans les formulaires liés aux entretiens.


🔧 2. CandidatureModel.php
Normalisation du champ statut avec les valeurs : envoyée, consultée, entretien, recruté, refusé.
Ajout de la validation stricte des statuts dans la méthode update().
Correction de la méthode findByUtilisateur() pour inclure le champ statut.



🔧 3. EntretienModel.php
Création de la méthode create(array $data) sans dépendance à id_annonce ou id_candidature.
Ajout de la méthode getAllRdv() pour alimenter FullCalendar (format requis : title, start, id).
Correction de la requête SQL dans getAllRdv() (ajout du FROM entretien manquant).
Ajout de méthodes de récupération par jour, semaine, mois, et administrateur.



🔧 4. AdministrateurController.php
Ajout de la méthode vueCalendrier() pour afficher les entretiens du mois.
Nettoyage de la méthode validerEntretien() : suppression des champs id_annonce et id_candidature, ajout de validation stricte.
Ajout de la méthode apiRdv() pour exposer les entretiens au format JSON.
Correction de la méthode creerEntretien() pour transmettre uniquement les données nécessaires au formulaire.
Ajout des méthodes :
modifierEntretien(int $id) : affiche un formulaire prérempli et met à jour l’entretien.
supprimerEntretien(int $id) : supprime l’entretien et retourne un code HTTP 200.



🔧 5. CalendrierView.php
Ajout de la méthode renderCalendrier() pour afficher les entretiens mensuels.
Ajout de la méthode renderFormCreation() avec les champs : date, heure, candidat, type, lien visio, commentaire.
Ajout de la méthode renderRappels() pour afficher les rappels du jour.
Ajout de la méthode renderDetails() pour afficher les détails d’un entretien.



🗃️ 6. Base de données
Suppression ou mise en NULL des contraintes sur id_annonce et id_candidature dans la table entretien.
Vérification du type DATE pour le champ date_entretien afin d’assurer la compatibilité avec les filtres mensuels.



📅 INTÉGRATION DE FULLCALENDAR

1. Ajout du fichier calendar.php
Chargement de FullCalendar via CDN.
Initialisation du calendrier en vue hebdomadaire (timeGridWeek).
Configuration des événements via l’URL /administrateur/api-rdv.
Ajout des interactions :
Sélection de créneau → redirection vers le formulaire de création.
Clic sur événement → menu d’action : voir, modifier, supprimer.



2. Méthode apiRdv() dans AdministrateurController.php
Récupère tous les entretiens via EntretienModel::getAllRdv().
Retourne les données au format JSON (id, title, start).
Ajout de exit; pour éviter l’injection de HTML parasite.



3. Correction du layout dans index.php
Ajout de la variable $isApiCall pour détecter les appels AJAX.
Désactivation conditionnelle du header, menu et footer pour /administrateur/api-rdv.
Prévention des erreurs PHP liées à des variables non définies ($afficherFooter, etc.).



4. Ajout des actions sur événement dans FullCalendar
Lors du clic sur un événement :
voir → redirection vers /administrateur/rdv?id=...
modifier → redirection vers /administrateur/modifier-entretien?id=...
supprimer → suppression via fetch() vers /administrateur/supprimer-entretien?id=...



5. Ajout des routes dans index.php
php
'modifier-entretien'   => $ctrl->modifierEntretien((int)$id),
'supprimer-entretien'  => $ctrl->supprimerEntretien((int)$id),


6. Préparation de la vue modifier-entretien.php
Formulaire similaire à celui de création, mais prérempli avec les données existantes.
Permet à l’administrateur de modifier les informations d’un entretien existant.


✅ Résultat final
Le calendrier FullCalendar est désormais :
Dynamique et synchronisé avec la base de données
Interactif pour la création, modification et suppression d’entretiens
Intégré proprement dans la structure MVC du projet