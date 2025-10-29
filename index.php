<?php


declare(strict_types=1);
ob_start();
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use App\Controller\{
    AdministrateurController,
    CandidatController,
    AnnonceController,
    CandidatureController,
    EntretienController,
    CalendrierController,
    UtilisateurController,
    NewsController
};

// Chargement des variables d'environnement
Dotenv\Dotenv::createImmutable(__DIR__)->load();
// 🔍 Routing parameters
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $requestUri)));

$action = $_GET['action'] ?? ($segments[0] ?? '');
$step   = $_GET['step']   ?? ($segments[1] ?? '');
$id     = $_GET['id']     ?? ($segments[2] ?? '');

// ➕ Détection d’un appel API (évite d’inclure le layout)
$isApiCall = ($action === 'administrateur' && $step === 'api-rdv');

// 🖼️ Layout control (uniquement si ce n’est pas un appel API)
if (!$isApiCall) {
    $afficherMenuPublic   = in_array($action, ['accueil', 'bureauEtude', 'domaineExpertise', 'recrutement', 'contact']);
    $afficherMenuConnecte = isset([
        'administrateur' => true,
        'candidat'       => true
    ][$action]);

    $afficherFooter = $afficherMenuPublic;

    // Templates head + menu
    require_once('assets/templates/head.php');
    if ($afficherMenuConnecte) require_once('assets/templates/menu-connecte.php');
    if ($afficherMenuPublic)   require_once('assets/templates/menu-public.php');
}


// 🎯 Routes
try {
    switch ($action) {
        case 'accueil':
        case 'bureauEtude':
        case 'domaineExpertise':
        case 'recrutement':
        case 'contact':
            include "Pages/{$action}.php";
            break;

            case 'administrateur':
    $ctrl = new AdministrateurController;

    match ($step) {
        // Profil & sessions
        'dashboard'        => $ctrl->dashboard($_SESSION['utilisateur']['id']),
        'profil'           => $ctrl->profil($_SESSION['utilisateur']['id']),
        'edit-profil'      => $ctrl->editProfil(),
        'delete-profil'    => $ctrl->deleteProfil(),
        'logout'           => $ctrl->logout(),

        // Annonces
        'annonces'         => $ctrl->viewAnnonces(),
        'create-annonce'   => $ctrl->createAnnonce(),
        'edit-annonce'     => $ctrl->editAnnonce((int)$id),
        'delete-annonce'   => $ctrl->deleteAnnonce((int)$id),
        'archive-annonce'  => $ctrl->archiveAnnonce((int)$id),

        // Candidatures
        'candidatures'     => $ctrl->listCandidatures(),
        'candidature'      => $ctrl->viewCandidature((int)$id),

        // Calendrier & entretiens
        'calendrier'       => $ctrl->calendrier(),
        'rdv'              => $id ? $ctrl->viewRdv((int)$id) : $ctrl->calendrier(),
        'creer-entretien'  => $ctrl->creerEntretien(),
        'valider-entretien'=> $ctrl->validerEntretien(),
        'edit-entretien'   => $ctrl->editEntretien(),     // GET = affiche le formulaire ; POST = enregistre
        'delete-entretien' => $ctrl->deleteEntretien(),   // POST = supprime
        'api-rdv'          => $ctrl->apiRdv(),

        // Alias tolérants (facultatif)
        'editEntretien'    => $ctrl->editEntretien(),
        'deleteEntretien'  => $ctrl->deleteEntretien(),

        // Par défaut
        default            => $ctrl->dashboard($_SESSION['utilisateur']['id']),
    };
    break;
            

    case 'candidat':
        $ctrl = new CandidatController;
        match ($step) {
            'profil'          => $ctrl->profil(),
            'edit-profil'     => $ctrl->editProfil(),     
            'update'          => $ctrl->update(),
            'delete'          => $ctrl->delete(),
            'upload-cv'       => $ctrl->uploadCV(),
            'uploadPhoto'     => $ctrl->uploadPhoto(),
            'annonces'        => $ctrl->listAnnonces(),
            'annonce-view'    => $ctrl->viewAnnonce((int)$id),
            'postuler'        => $ctrl->postuler((int)$_GET['id']),
            'candidatures'    => $ctrl->renderSuiviCandidatures(),
            default           => $ctrl->profil(),
        };
        break;

        case 'annonce':
            $ctrl = new AnnonceController;
            
            // Gestion des actions spéciales avec paramètres GET
            if (isset($_GET['status'])) {
                $ctrl->listByStatus($_GET['status']);
                break;
            }
            
            if (isset($_GET['stats'])) {
                $ctrl->showStats();
                break;
            }
            
            // Affichage des messages de succès/erreur
            $ctrl->displayMessages();
            
            // Routes principales des annonces
            match ($step) {
                'create' => $ctrl->createAnnonce(),
                'update' => $id ? $ctrl->updateAnnonce((int)$id) : (function() use ($ctrl) {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour la modification.</div>";
                    $ctrl->index();
                })(),
                'view'   => $id ? $ctrl->viewAnnonce((int)$id) : (function() use ($ctrl) {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour afficher l'annonce.</div>";
                    $ctrl->index();
                })(),
                'delete' => $id ? $ctrl->deleteAnnonce((int)$id) : (function() use ($ctrl) {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour supprimer l'annonce.</div>";
                    $ctrl->index();
                })(),
                'search' => $ctrl->searchAnnonces(),
                'archive' => $id ? $ctrl->archiveAnnonce((int)$id) : (function() use ($ctrl) {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour archiver l'annonce.</div>";
                    $ctrl->index();
                })(),
                'activate' => $id ? $ctrl->activateAnnonce((int)$id) : (function() use ($ctrl) {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour activer l'annonce.</div>";
                    $ctrl->index();
                })(),
                default  => $ctrl->index(), // Affichage par défaut : liste des annonces
            };
            break;

            case 'candidature':
                $ctrl = new CandidatureController;
            
                match ($step) {
                    'submit'         => $ctrl->submitCandidature(),
                    'view'           => $ctrl->viewCandidature((int)$id),
                    'delete'         => $ctrl->deleteCandidature((int)$id),
                    'suivi'          => $ctrl->suivi(),
                    'update-statut'  => $ctrl->updateStatut(),  // ✅ existant
                    'updateStatut'   => $ctrl->updateStatut(),  // ✅ alias camelCase
                    default          => $ctrl->listCandidatures(),
                };
                break;

        case 'entretien':
            $ctrl = new EntretienController;
            match ($step) {
                'planifier' => $ctrl->planifierEntretien(),
                'edit-entretien'    => $ctrl->editEntretien(),     // ✅ GET (affichage) et POST (enregistrement)
                'delete-entretien'  => $ctrl->deleteEntretien(),   // ✅ POST suppression
                'editEntretien'     => $ctrl->editEntretien(),
                'deleteEntretien'   => $ctrl->deleteEntretien(),
                'valider-entretien' => $ctrl->validerEntretien(),
                'rappel'    => $ctrl->envoyerRappel((int)$id),
                default     => $ctrl->listEntretiens(),
            };
            break;

        case 'utilisateur':
            $ctrl = new UtilisateurController;
            match ($step) {
                'create' => $ctrl->createUtilisateur(),
                'edit'   => $ctrl->editUtilisateur((int)$id),
                'login'  => $ctrl->loginUtilisateur(),
                'logout' => $ctrl->logoutUtilisateur(),
                'update' => $ctrl->updateUtilisateur(),
                'delete' => $ctrl->deleteUtilisateur((int)$id),
                default  => $ctrl->listUtilisateur(),
            };
            break;

        case 'calendrier':
            $ctrl = new CalendrierController;
            match ($step) {
                'semaine'      => $ctrl->vueSemaine(),
                'jour'         => $ctrl->vueJour($id),
                'rappel'       => $ctrl->rappelDuJour(),
                'rendez-vous'  => $ctrl->infoRendezVous($id),
                'vue-calendrier' => $ctrl->vueCalendrier(),
                default        => $ctrl->vueSemaine(),
            };
            break;

        // Gestion des requêtes nulles ou vides
        case '':
        case null:
            // Par défaut, afficher les annonces si aucune action n'est spécifiée
            $ctrl = new AnnonceController;
            $ctrl->displayMessages();
            $ctrl->index();
            break;

        default:
            // Action non reconnue, afficher l'accueil
            include "Pages/accueil.php";
            break;
    }

} catch (Exception $e) {
    // Gestion globale des erreurs
    echo "<div class='container' style='margin: 20px auto; padding: 20px; max-width: 800px;'>";
    echo "<div class='alert alert-danger' style='color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px;'>";
    echo "<h3>⚠️ Erreur Système</h3>";
    echo "<p><strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier :</strong> " . htmlspecialchars($e->getFile()) . " (ligne " . $e->getLine() . ")</p>";
    
    echo "<hr style='margin: 15px 0;'>";
    echo "<p><strong>Solutions possibles :</strong></p>";
    echo "<ul>";
    echo "<li>Vérifiez la configuration de votre base de données</li>";
    echo "<li>Assurez-vous que toutes les tables existent</li>";
    echo "<li>Vérifiez les variables d'environnement (.env)</li>";
    echo "<li>Contrôlez les permissions des fichiers</li>";
    echo "</ul>";
    
    echo "<div style='text-align: center; margin-top: 20px;'>";
    echo "<a href='?action=accueil' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>🏠 Retour à l'accueil</a>";
    echo "<a href='?action=annonce' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;'>📋 Voir les annonces</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Log de l'erreur pour le debugging
    error_log("Erreur dans index.php : " . $e->getMessage() . " - " . $e->getFile() . ":" . $e->getLine());
}

// Footer si nécessaire
if (!$isApiCall && isset($afficherFooter) && $afficherFooter) {
    require_once('assets/templates/footer.php');
}

ob_end_flush();

// Ajout de styles CSS de base pour les alertes si elles n'existent pas
echo "<style>
.alert {
    padding: 15px;
    margin: 10px 0;
    border-radius: 4px;
    border: 1px solid transparent;
}
.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}
.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}
.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}
.btn {
    display: inline-block;
    padding: 8px 16px;
    margin: 4px 2px;
    text-decoration: none;
    border-radius: 4px;
    border: 1px solid transparent;
    cursor: pointer;
}
.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}
.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}
.btn:hover {
    opacity: 0.8;
}
</style>";
?>