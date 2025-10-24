<?php
namespace App\Controller;

use App\Model\AnnonceModel;
use App\View\AnnonceView;
use PDO;
use Exception;

class AnnonceController {
    private AnnonceModel $model;
    private AnnonceView $view;

// App/Controller/AnnonceController.php
public function __construct(?AnnonceModel $model = null, ?AnnonceView $view = null) {
    if ($model && $view) {
        $this->model = $model;
        $this->view  = $view;
        return;
    }

    $host = $_ENV['DB_HOST_LOCAL'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME_LOCAL'] ?? '';
    $user = $_ENV['DB_USER_LOCAL'] ?? '';
    $pass = $_ENV['DB_PASSWORD_LOCAL'] ?? '';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $this->model = new AnnonceModel($pdo);
    $this->view  = new AnnonceView();
}
    
    /**
     * Méthode par défaut qui s'exécute si aucune action n'est spécifiée
     * ou si l'action est 'annonce' sans step
     */
    public function index() {
        $this->listAnnonces();
    }

    /**
     * Méthode principale de routage - gère toutes les requêtes
     */
    public function handleRequest() {
        $step = $_GET['step'] ?? null;
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        switch ($step) {
            case 'view':
                if ($id) {
                    $this->viewAnnonce($id);
                } else {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour afficher l'annonce.</div>";
                    $this->index(); // Retour à la liste
                }
                break;
            
            case 'create':
                $this->createAnnonce();
                break;
            
            case 'update':
                $this->updateAnnonce($id);
                break;
            
            case 'delete':
                if ($id) {
                    $this->deleteAnnonce($id);
                } else {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour supprimer l'annonce.</div>";
                    $this->index(); // Retour à la liste
                }
                break;
            
            case 'search':
                $this->searchAnnonces();
                break;
            
            case 'archive':
                if ($id) {
                    $this->archiveAnnonce($id);
                } else {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour archiver l'annonce.</div>";
                    $this->index();
                }
                break;
            
            case 'activate':
                if ($id) {
                    $this->activateAnnonce($id);
                } else {
                    echo "<div class='alert alert-warning'>⚠️ ID manquant pour activer l'annonce.</div>";
                    $this->index();
                }
                break;
            
            default:
                // Action par défaut : afficher la liste des annonces
                $this->index();
                break;
        }
    }

    /**
     * Affiche la liste des annonces
     */
    public function listAnnonces() {
        try {
            $annonces = $this->model->getAll();
            $this->view->renderListe($this->model->getAnnoncesDisponibles());

        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>⚠️ Erreur lors du chargement des annonces : " . htmlspecialchars($e->getMessage()) . "</div>";
            // Afficher un formulaire de création en cas d'erreur de base de données
            echo "<p>Voulez-vous créer une nouvelle annonce ?</p>";
            echo "<a href='?action=annonce&step=create' class='btn btn-primary'>➕ Créer une annonce</a>";
        }
    }

    /**
     * Affiche le détail d'une annonce
     */
    public function viewAnnonce(int $id) {
        try {
            $annonce = $this->model->getById($id);
            if ($annonce) {
                $this->view->renderDetails($annonce);
            } else {
                echo "<div class='alert alert-warning'>⚠️ Annonce introuvable.</div>";
                echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Retour à la liste</a>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>⚠️ Erreur lors du chargement de l'annonce : " . htmlspecialchars($e->getMessage()) . "</div>";
            echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Retour à la liste</a>";
        }
    }

    /**
     * Gère la création d'annonces
     */
    public function createAnnonce() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validation basique des données requises
                $requiredFields = ['titre', 'description', 'mission', 'profil_recherche', 
                                 'localisation', 'code_postale', 'secteur_activite', 'type_contrat'];
                
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Le champ '$field' est requis.");
                    }
                }

                $result = $this->model->create($_POST);
                if ($result) {
                    header("Location: index.php?action=annonce&success=created");
                    exit;
                } else {
                    echo "<div class='alert alert-danger'>⚠️ Erreur lors de la création de l'annonce.</div>";
                }
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>⚠️ Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        
        // Afficher le formulaire de création
        $this->view->renderForm('create');
    }

    /**
     * Gère la mise à jour d'annonces
     */
    public function updateAnnonce(?int $id) {
        // Si c'est une soumission POST, on traite la mise à jour
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            try {
                $result = $this->model->update((int)$_POST['id'], $_POST);
                if ($result) {
                    header("Location: index.php?action=annonce&success=updated");
                    exit;
                } else {
                    echo "<div class='alert alert-danger'>⚠️ Erreur lors de la mise à jour de l'annonce.</div>";
                }
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>⚠️ Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        
        // Affichage du formulaire de modification
        if ($id) {
            try {
                $annonce = $this->model->getById($id);
                if ($annonce) {
                    $this->view->renderForm('update', $annonce);
                } else {
                    echo "<div class='alert alert-warning'>⚠️ Annonce introuvable.</div>";
                    echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Retour à la liste</a>";
                }
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>⚠️ Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
                echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Retour à la liste</a>";
            }
        } else {
            echo "<div class='alert alert-warning'>⚠️ ID manquant pour la modification.</div>";
            echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Retour à la liste</a>";
        }
    }

    /**
     * Supprime une annonce
     */
    public function deleteAnnonce(int $id) {
        try {
            // Vérifier que l'annonce existe avant de la supprimer
            $annonce = $this->model->getById($id);
            if (!$annonce) {
                header("Location: index.php?action=annonce&error=not_found");
                exit;
            }

            $result = $this->model->delete($id);
            if ($result) {
                header("Location: index.php?action=annonce&success=deleted");
            } else {
                header("Location: index.php?action=annonce&error=delete_failed");
            }
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression : " . $e->getMessage());
            header("Location: index.php?action=annonce&error=delete_error");
        }
        exit;
    }

    /**
     * Recherche d'annonces
     */
    public function searchAnnonces() {
        $keyword = $_GET['q'] ?? '';
        
        if (!empty($keyword)) {
            try {
                $annonces = $this->model->search($keyword);
                echo "<h3>🔍 Résultats de recherche pour : \"" . htmlspecialchars($keyword) . "\" (" . count($annonces) . " résultat(s))</h3>";
                echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Voir toutes les annonces</a><br><br>";
                
                if (empty($annonces)) {
                    echo "<div class='alert alert-info'>Aucune annonce trouvée pour ce terme de recherche.</div>";
                    echo "<a href='?action=annonce&step=create' class='btn btn-primary'>➕ Créer une nouvelle annonce</a>";
                } else {
                    $this->view->renderListe($annonces);
                }
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>⚠️ Erreur lors de la recherche : " . htmlspecialchars($e->getMessage()) . "</div>";
                $this->index();
            }
        } else {
            // Si pas de mot-clé, retour à la liste complète
            echo "<div class='alert alert-warning'>⚠️ Veuillez saisir un terme de recherche.</div>";
            $this->index();
        }
    }

    /**
     * Archive une annonce
     */
    public function archiveAnnonce(int $id) {
        try {
            $result = $this->model->archive($id);
            if ($result) {
                header("Location: index.php?action=annonce&success=archived");
            } else {
                header("Location: index.php?action=annonce&error=archive_failed");
            }
        } catch (Exception $e) {
            header("Location: index.php?action=annonce&error=archive_error");
        }
        exit;
    }

    /**
     * Active une annonce
     */
    public function activateAnnonce(int $id) {
        try {
            $result = $this->model->activate($id);
            if ($result) {
                header("Location: index.php?action=annonce&success=activated");
            } else {
                header("Location: index.php?action=annonce&error=activate_failed");
            }
        } catch (Exception $e) {
            header("Location: index.php?action=annonce&error=activate_error");
        }
        exit;
    }

    /**
     * Affiche les annonces par statut
     */
    public function listByStatus(string $status) {
        try {
            $annonces = $this->model->getByStatus($status);
            echo "<h3>📋 Annonces avec le statut : " . ucfirst($status) . " (" . count($annonces) . ")</h3>";
            echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Voir toutes les annonces</a><br><br>";
            $this->view->renderListe($annonces);
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>⚠️ Erreur lors du chargement des annonces : " . htmlspecialchars($e->getMessage()) . "</div>";
            $this->index();
        }
    }

    /**
     * Affiche les statistiques des annonces
     */
    public function showStats() {
        try {
            $total = $this->model->count();
            $active = $this->model->getByStatus('active');
            $inactive = $this->model->getByStatus('inactive');
            $archived = $this->model->getByStatus('archivee');
            
            echo "<div class='stats-container'>";
            echo "<h3>📊 Statistiques des annonces</h3>";
            echo "<div class='stats-grid'>";
            echo "<div class='stat-item'><strong>Total :</strong> $total</div>";
            echo "<div class='stat-item'><strong>Actives :</strong> " . count($active) . "</div>";
            echo "<div class='stat-item'><strong>Inactives :</strong> " . count($inactive) . "</div>";
            echo "<div class='stat-item'><strong>Archivées :</strong> " . count($archived) . "</div>";
            echo "</div>";
            echo "<a href='?action=annonce' class='btn btn-secondary'>⬅️ Retour à la liste</a>";
            echo "</div>";
            
            echo "<style>
            .stats-container { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
            .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
            .stat-item { padding: 15px; background: #f8f9fa; border-radius: 5px; text-align: center; }
            </style>";
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>⚠️ Erreur lors du chargement des statistiques : " . htmlspecialchars($e->getMessage()) . "</div>";
            $this->index();
        }
    }

    /**
     * Affiche les messages de succès/erreur
     */
    public function displayMessages() {
        if (isset($_GET['success'])) {
            switch ($_GET['success']) {
                case 'created':
                    echo "<div class='alert alert-success'>✅ Annonce créée avec succès !</div>";
                    break;
                case 'updated':
                    echo "<div class='alert alert-success'>✅ Annonce mise à jour avec succès !</div>";
                    break;
                case 'deleted':
                    echo "<div class='alert alert-success'>✅ Annonce supprimée avec succès !</div>";
                    break;
                case 'archived':
                    echo "<div class='alert alert-success'>✅ Annonce archivée avec succès !</div>";
                    break;
                case 'activated':
                    echo "<div class='alert alert-success'>✅ Annonce activée avec succès !</div>";
                    break;
            }
        }
        
        if (isset($_GET['error'])) {
            switch ($_GET['error']) {
                case 'not_found':
                    echo "<div class='alert alert-danger'>⚠️ Annonce introuvable.</div>";
                    break;
                case 'delete_failed':
                    echo "<div class='alert alert-danger'>⚠️ Erreur lors de la suppression de l'annonce.</div>";
                    break;
                case 'delete_error':
                    echo "<div class='alert alert-danger'>⚠️ Une erreur est survenue lors de la suppression.</div>";
                    break;
                case 'archive_failed':
                    echo "<div class='alert alert-danger'>⚠️ Erreur lors de l'archivage de l'annonce.</div>";
                    break;
                case 'archive_error':
                    echo "<div class='alert alert-danger'>⚠️ Une erreur est survenue lors de l'archivage.</div>";
                    break;
                case 'activate_failed':
                    echo "<div class='alert alert-danger'>⚠️ Erreur lors de l'activation de l'annonce.</div>";
                    break;
                case 'activate_error':
                    echo "<div class='alert alert-danger'>⚠️ Une erreur est survenue lors de l'activation.</div>";
                    break;
            }
        }
    }

    /**
     * Point d'entrée principal - appelé depuis index.php
     */
    public function run() {
        // Afficher les messages de succès/erreur
        $this->displayMessages();
        
        // Gérer les actions spéciales
        if (isset($_GET['status'])) {
            $this->listByStatus($_GET['status']);
            return;
        }
        
        if (isset($_GET['stats'])) {
            $this->showStats();
            return;
        }
        
        // Traiter la requête normale
        $this->handleRequest();
    }

    public function creerEntretien(): void
{
    $this->redirectIfNotAdmin();

    $annonces = $this->annonceModel->getByAdmin($_SESSION['utilisateur']['id']);
    $candidats = $this->userModel->getAllCandidats();
    
    // Traitement de la date et heure depuis l'URL
    $dateTime = $_GET['date'] ?? '';
    $date = substr($dateTime, 0, 10);
    $heure = substr($dateTime, 11, 5);
    
    // Affichage du formulaire
    $this->calendarView->renderFormCreation($date, $heure, $annonces, $candidats);
}

}