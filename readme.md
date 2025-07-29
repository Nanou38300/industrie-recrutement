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




<!-- ---------------------------   -------------------------------------- -->
