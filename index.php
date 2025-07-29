<?php
declare(strict_types=1);          // ✅ Active le typage strict pour plus de robustesse
session_start();                   // ✅ Démarre la session pour suivre l’utilisateur

require_once __DIR__ . '/vendor/autoload.php'; // ✅ Chargement automatique des classes via Composer

// ✅ Importation des contrôleurs nécessaires au projet
use App\View\SharedView;
use App\Controller\AdministrateurController;
use App\Controller\AnnonceController;
use App\Controller\CandidatureController;
use App\Controller\EntretienController;
use App\Controller\UtilisateurController;
use App\Controller\NewsController;
use App\Controller\CalendrierController;
use App\Controller\CandidatController;

// ✅ Chargement des variables d’environnement (BDD, API, etc.)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ✅ Analyse de l’URL demandée (sans les paramètres GET)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ✅ Découpe l’URL en segments (ex: /admin/edit/5 → ['admin', 'edit', '5'])
$segments = array_values(array_filter(explode('/', $requestUri)));

// ✅ Définition des paramètres de routage
$action = $segments[0] ?? ($_GET['action'] ?? '');
$step   = $segments[1] ?? ($_GET['step'] ?? '');
$id     = $segments[2] ?? ($_GET['id'] ?? '');

// ✅ Inclusion des templates partagés
require_once('assets/templates/head.php');
require_once('assets/templates/menu.php');


// 🚦 Routeur principal
switch ($action) {

    // ✅ Pages statiques
    case 'accueil':
        include "Pages/accueil.php";
        break;
    case 'bureauEtude':
        include "Pages/bureauEtude.php";
        break;
    case 'domaineExpertise':
        include "Pages/domaineExpertise.php";
        break;
    case 'recrutement':
        include "Pages/recrutement.php";
        break;
    case 'contact':
        include "Pages/contact.php";
        break;

    // ✅ ADMINISTRATION
    case 'administrateur':
        $a = new AdministrateurController;
        switch ($step) {
            case 'dashboard':         $a->dashboard(); break;
            case 'profil':            $a->editProfil(); break;
            case 'annonces':          $a->viewAnnonces(); break;
            case 'create-annonce':    $a->createAnnonce(); break;
            case 'edit-annonce':      $a->editAnnonce((int)$id); break;
            case 'archive-annonce':   $a->archiveAnnonce((int)$id); break;
            case 'candidatures':      $a->listCandidatures(); break;
            case 'candidature':       $a->viewCandidature((int)$id); break;
            default:                  $a->dashboard(); break;
        }
        break;

    // ✅ GESTION DES ANNONCES (globale)
    case 'annonce':
        $annonce = new AnnonceController;
        switch ($step) {
            case 'create':  $annonce->createAnnonce((int)$id); break;
            case 'update':  $annonce->updateAnnonce(); break;
            case 'delete':  $annonce->deleteAnnonce((int)$id); break;
            default:        $annonce->listAnnonces(); break;
        }
        break;

    // ✅ GESTION DES CANDIDATURES (globale)
    case 'candidature':
        $candidature = new CandidatureController;
        switch ($step) {
            case 'submit':  $candidature->submitCandidature(); break;
            case 'view':    $candidature->viewCandidature((int)$id); break;
            case 'delete':  $candidature->deleteCandidature((int)$id); break;
            case 'suivi':   $candidature->suivi(); break;  // 💡 Attention, tu avais utilisé $c au lieu de $candidature
            default:        $candidature->listCandidatures(); break;
        }
        break;

    // ✅ ENTRETIENS
    case 'entretien':
        $entretien = new EntretienController;
        switch ($step) {
            case 'planifier':     $entretien->planifierEntretien(); break;
            case 'rappel':        $entretien->envoyerRappel((int)$id); break;
            default:              $entretien->listEntretiens(); break;
        }
        break;

    // ✅ UTILISATEURS
    case 'utilisateur':
        $utilisateur = new UtilisateurController;
        switch ($step) {
            case 'create':   $utilisateur->createUtilisateur(); break;
            case 'edit':     $utilisateur->editUtilisateur((int)$id); break;
            case 'login':    $utilisateur->loginUtilisateur((int)$id); break;
            case 'logout':   $utilisateur->logoutUtilisateur(); break;
            case 'update':   $utilisateur->updateUtilisateur(); break;
            case 'delete':   $utilisateur->deleteUtilisateur((int)$id); break;
            default:         $utilisateur->listUtilisateurs(); break;
        }
        break;

    // ✅ CALENDRIER
    case 'calendrier':
        $cal = new CalendrierController;
        switch ($step) {
            case 'semaine':      $cal->vueSemaine(); break;
            case 'jour':         $cal->vueJour($id); break;
            case 'rappel':       $cal->rappelDuJour(); break;
            case 'rendez-vous':  $cal->infoRendezVous($id); break;
            default:             $cal->vueSemaine(); break;
        }
        break;

    // ✅ CANDIDAT — Accès au front office
    case 'candidat':
        $c = new CandidatController;
        switch ($step) {
            case 'profil':            $c->profil(); break;
            case 'update':            $c->update(); break;
            case 'upload-cv':         $c->uploadCV(); break;
            case 'annonces':          $c->listAnnonces(); break;
            case 'annonce-view':      $c->viewAnnonce((int)$id); break;
            case 'postuler':          $c->postuler((int)$id); break;
            case 'candidatures':      $c->suiviCandidatures(); break;
            default:                  $c->profil(); break;
        }
        break;

    // ✅ Route par défaut → Page d'accueil
    default:
        require_once('Pages/accueil.php');
        break;
}

// ✅ Inclusion du pied de page et de la bulle de contact
require_once("assets/templates/footer.php");
require_once("assets/templates/bulle-flottante.php");
?>
