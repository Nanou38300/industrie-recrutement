<?php


declare(strict_types=1);
ob_start();
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/vendor/autoload.php';

use App\Controller\{
    AdministrateurController,
    CandidatController,
    AnnonceController,
    CandidatureController,
    UtilisateurController,
};

// Chargement des variables d'environnement
Dotenv\Dotenv::createImmutable(__DIR__)->load();
// 🔍 Routing parameters
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $requestUri)));

$action = $_GET['action'] ?? ($segments[0] ?? '');
$step   = $_GET['step']   ?? ($segments[1] ?? '');
$id     = $_GET['id']     ?? ($segments[2] ?? '');


// ====== SEO ======
$actionNorm = $action !== '' ? $action : 'accueil';

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST'];

$currentPath = strtok($_SERVER['REQUEST_URI'], '?'); // sans query-string
$canonical = rtrim($baseUrl, '/') . $currentPath;

$metaByAction = [
  'accueil' => [
    'title' => "Page d'accueil",
    'description' => "Spécialistes en chaudronnerie, tuyauterie et soudure, nous accompagnons les industriels dans la fabrication, l’installation et la maintenance de leurs équipements. Grâce à notre expertise technique, notre réactivité et notre exigence qualité, nous intervenons sur des installations complexes dans les secteurs du nucléaire, de la chimie et de la maintenance industrielle. Notre objectif : garantir la fiabilité, la sécurité et la performance de vos infrastructures.",
  ],
  'bureauEtude' => [
    'title' => "Bureau d’études — TCS Chaudronnerie",
    'description' => "Conception, ingénierie, dossiers techniques (DMOS/QMOS), et accompagnement de la conception à la mise en service.",
  ],
  'domaineExpertise' => [
    'title' => "Domaines d’expertise — TCS Chaudronnerie",
    'description' => "Nous intervenons dans les secteurs du nucléaire, de la chimie et de la maintenance industrielle, en mettant à disposition notre savoir-faire en chaudronnerie et tuyauterie. Nos équipes qualifiées réalisent des travaux en zones contrôlées, fabriquent des équipements sous pression, installent des réseaux de tuyauterie pour fluides complexes et assurent la maintenance d’installations industrielles, avec un haut niveau d’exigence en matière de sécurité, conformité et réactivité.",
  ],
  'recrutement' => [
    'title' => "Recrutement — TCS Chaudronnerie",
    'description' => "Nos offres d’emploi en chaudronnerie, tuyauterie et soudage. Rejoignez une équipe experte.",
  ],
  'contact' => [
    'title' => "Contact — TCS Chaudronnerie",
    'description' => "Parlez-nous de votre projet : maintenance, fabrication et installation d’équipements industriels.",
  ],
];

// Par défaut (pages non publiques / back-office…) : on met noindex
$defaultMeta = [
  'title' => "TCS Chaudronnerie",
  'description' => "Solutions de chaudronnerie, tuyauterie et soudure pour l’industrie.",
  'robots' => 'noindex, nofollow',
];

// Pages publiques (menu public) indexables
$publicActions = ['accueil','bureauEtude','domaineExpertise','recrutement','contact'];

// Construction du SEO final
$SEO = $metaByAction[$actionNorm] ?? $defaultMeta;
$SEO['canonical'] = $canonical;

// robots: index/follow pour les pages publiques, noindex ailleurs
if (in_array($actionNorm, $publicActions, true)) {
  $SEO['robots'] = 'index, follow';
} else {
  $SEO['robots'] = $SEO['robots'] ?? 'noindex, nofollow';
}

// Exemple de cas particulier : annonce/view/{id} -> indexable avec title/description dynamiques simples
if ($action === 'annonce' && $step === 'view' && ctype_digit((string)$id)) {
  $SEO['title'] = "Offre #$id — TCS Chaudronnerie";
  $SEO['description'] = "Découvrez l’offre d’emploi #$id chez TCS Chaudronnerie. Postulez dès maintenant.";
  $SEO['robots'] = 'index, follow';
}
// ➕ Détection d’un appel API (évite d’inclure le layout)
$isApiCall = ($action === 'administrateur' && $step === 'api-rdv');


// 🖼️ Layout control (uniquement si ce n’est pas un appel API)
if (!$isApiCall) {
    // ➕ Ajout d'une condition pour afficher le menu public sur /utilisateur/login et /utilisateur/create
    $afficherMenuPublic =
        in_array($action, ['accueil', 'bureauEtude', 'domaineExpertise', 'recrutement', 'contact'], true)
        || ($action === 'utilisateur' && in_array($step, ['login', 'create'], true));

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

        // Traitement des formulaires contact + candidature spontanée (contact.php / recrutement.php)
        case 'candidature.php':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nom    = trim($_POST['nom'] ?? '');
                $prenom = trim($_POST['prenom'] ?? '');
                $email  = trim($_POST['email'] ?? '');
                $msg    = trim($_POST['message'] ?? '');

                // Destination RH (à adapter si besoin)
                $to = 'rh@tcs-chaudronnerie.fr';

                // Sujet différent selon la page d'origine
                $fromPage = strpos($_SERVER['HTTP_REFERER'] ?? '', 'contact') !== false ? 'Formulaire de contact' : 'Candidature spontanée';
                $subject  = "[$fromPage] $nom $prenom";

                $body  = "Origine : $fromPage\n\n";
                $body .= "Nom    : $nom\n";
                $body .= "Prénom : $prenom\n";
                $body .= "Email  : $email\n\n";
                $body .= "Message :\n$msg\n";

                // Tentative d'envoi de mail basique
                @mail($to, $subject, $body, "From: $email\r\nReply-To: $email\r\n");

                $_SESSION['flash'] = $fromPage === 'Formulaire de contact'
                    ? "✅ Votre message a bien été envoyé."
                    : "✅ Votre candidature spontanée a bien été envoyée.";
                $_SESSION['flash_type'] = 'success';

                // Redirection : contact ou recrutement
                if ($fromPage === 'Formulaire de contact') {
                    header('Location: /contact');
                } else {
                    header('Location: /recrutement');
                }
                exit;
            }
            // Si appel non-POST, retour à l'accueil
            header('Location: /accueil');
            exit;

        case 'administrateur':
    $ctrl = new AdministrateurController;

    match ($step) {
        // Profil & sessions
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
        default           => $ctrl->profil($_SESSION['utilisateur']['id']),
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
    // Message générique pour l'utilisateur
    echo "<div class='container' style='margin: 20px auto; padding: 20px; max-width: 600px;'>";
    echo "<div class='alert alert-danger' style='color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 6px; text-align: center;'>";
    echo "<h3>⚠️ Une erreur est survenue</h3>";
    echo "<p>Nous ne pouvons pas afficher la page demandée pour le moment. Veuillez réessayer plus tard ou retourner à l'accueil.</p>";
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='?action=accueil' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;'>🏠 Retour à l'accueil</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Log détaillé uniquement côté serveur
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
