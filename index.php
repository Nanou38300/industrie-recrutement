<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/vendor/autoload.php';

// Importation des contrôleurs
use App\Controller\AdministrateurController;
use App\Controller\CandidatController;
use App\Controller\AnnonceController;
use App\Controller\CandidatureController;
use App\Controller\EntretienController;
use App\Controller\CalendrierController;
use App\Controller\UtilisateurController;
// use App\Controller\NewsController;

// Chargement des variables d’environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Analyse de l’URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $requestUri)));

$action = $_GET['action'] ?? ($segments[0] ?? '');
$step   = $segments[1] ?? ($_GET['step'] ?? '');
$id     = $segments[2] ?? ($_GET['id'] ?? '');




// 🔍 Définir les pages sans menu/footer
$afficherLayout          = true;
$afficherMenuPublic      = false;
$afficherMenuConnecte    = false;
$afficherFooter          = false;

$pagesStatiques = ['accueil', 'bureauEtude', 'domaineExpertise', 'recrutement', 'contact'];


if (in_array($action, $pagesStatiques)) {
    $afficherMenuPublic = true;
    $afficherFooter = true;
}
$pagesConnectees = [
    'administrateur' => ['profil', 'annonces', 'create-annonce', 'edit-annonce', 'archive-annonce', 'candidatures', 'candidature'],
    'candidat'       => ['profil', 'update', 'delete', 'upload-cv', 'annonces', 'annonce-view', 'postuler', 'candidatures'],
    // 'utilisateur' => ['login', 'create'],
];


if (
    (isset($pagesConnectees[$action]) && in_array($step, $pagesConnectees[$action]))
) {
    $afficherMenuConnecte = true;
}

// Inclusion des templates communs si nécessaire
require_once('assets/templates/head.php');

if ($afficherMenuConnecte) {
    require_once('assets/templates/menu-connecte.php');
}

if ($afficherMenuPublic) {
    require_once('assets/templates/menu-public.php');
}


// Routeur principal
switch ($action) {

    // Pages statiques
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

    // ADMINISTRATEUR
    case 'administrateur':
        $a = new AdministrateurController;
        switch ($step) {
            // case 'dashboard':       $a->dashboard($_SESSION['utilisateur']['id']); break;
            case 'profil':          $a->profil($_SESSION['utilisateur']['id']); break;
            case 'annonces':        $a->viewAnnonces(); break;
            case 'create-annonce':  $a->createAnnonce(); break;
            case 'edit-annonce':    $a->editAnnonce((int)$id); break;
            case 'archive-annonce': $a->archiveAnnonce((int)$id); break;
            case 'candidatures':    $a->listCandidatures(); break;
            case 'candidature':     $a->viewCandidature((int)$id); break;
            default:                $a->dashboard($_SESSION['utilisateur']['id']); break;
        }
        break;

    // CANDIDAT
    case 'candidat':
        $c = new CandidatController;
        switch ($step) {
            case 'profil':          $c->profil(); break;
            case 'update':          $c->update(); break;
            case 'delete':          $c->delete(); break;
            case 'upload-cv':       $c->uploadCV(); break;
            case 'annonces':        $c->listAnnonces(); break;
            case 'annonce-view':    $c->viewAnnonce((int)$id); break;
            case 'postuler':        $c->postuler((int)$id); break;
            case 'candidatures':    $c->suiviCandidatures(); break;
            default:                $c->profil(); break;
        }
        break;

    // ANNONCE
    case 'annonce':
        $annonce = new AnnonceController;
        switch ($step) {
            case 'create':  $annonce->createAnnonce((int)$id); break;
            case 'update':  $annonce->updateAnnonce(); break;
            case 'delete':  $annonce->deleteAnnonce((int)$id); break;
            default:        $annonce->listAnnonces(); break;
        }
        break;

    // CANDIDATURE
    case 'candidature':
        $candidature = new CandidatureController;
        switch ($step) {
            case 'submit':  $candidature->submitCandidature(); break;
            case 'view':    $candidature->viewCandidature((int)$id); break;
            case 'delete':  $candidature->deleteCandidature((int)$id); break;
            case 'suivi':   $candidature->suivi(); break;
            default:        $candidature->listCandidatures(); break;
        }
        break;

    // ENTRETIEN
    case 'entretien':
        $entretien = new EntretienController;
        switch ($step) {
            case 'planifier':     $entretien->planifierEntretien(); break;
            case 'rappel':        $entretien->envoyerRappel((int)$id); break;
            default:              $entretien->listEntretiens(); break;
        }
        break;

    // UTILISATEUR
    case 'utilisateur':
        $utilisateur = new UtilisateurController;
        switch ($step) {
            case 'create':   $utilisateur->createUtilisateur(); break;
            case 'edit':     $utilisateur->editUtilisateur((int)$id); break;
            case 'login':    $utilisateur->loginUtilisateur(); break;
            case 'logout':   $utilisateur->logoutUtilisateur(); break;
            case 'update':   $utilisateur->updateUtilisateur(); break;
            case 'delete':   $utilisateur->deleteUtilisateur((int)$id); break;
            default:         $utilisateur->listUtilisateur(); break;
        }
        break;

    // CALENDRIER
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


    // Route par défaut
    default:
        include "Pages/accueil.php";
        break;


}

// ✅ Inclusion du footer uniquement sur les pages statiques
if ($afficherFooter) {
    require_once('assets/templates/footer.php');
}



?>
