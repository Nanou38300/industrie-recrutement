<?php
namespace App\Controller;

use App\Model\AnnonceModel;
use App\View\AnnonceView;
use PDO;
use Exception;

class AnnonceController
{
    private AnnonceModel $model;
    private AnnonceView $view;

    // App/Controller/AnnonceController.php
    public function __construct(?AnnonceModel $model = null, ?AnnonceView $view = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // CAS TESTS : si on fournit un modèle et une vue (mocks),
        // on les utilise et on ne crée PAS de PDO
        if ($model && $view) {
            // Injection de dépendances (tests, etc.)
            $this->model = $model ?? new AnnonceModel();
            $this->view  = $view  ?? new AnnonceView();
            return;
        }

        $host   = $_ENV['DB_HOST_LOCAL']     ?? 'localhost';
        $dbname = $_ENV['DB_NAME_LOCAL']     ?? '';
        $user   = $_ENV['DB_USER_LOCAL']     ?? '';
        $pass   = $_ENV['DB_PASSWORD_LOCAL'] ?? '';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
        $pdo = new PDO($dsn, $user, $pass);

        $this->model = new AnnonceModel($pdo);
        $this->view  = new AnnonceView();
    }

    // 🔐 Vérifie le token CSRF pour les requêtes POST
    private function checkCsrfToken(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo "Requête invalide (CSRF).";
            exit;
        }
    }

    /**
     * Méthode par défaut qui s'exécute si aucune action n'est spécifiée
     * ou si l'action est 'annonce' sans step
     */
    public function index(): void
    {
        $this->listAnnonces();
    }


    /**
     * Affiche la liste des annonces
     */
    public function listAnnonces(): void
    {
        try {
            // $annonces = $this->model->getAll(); // non utilisé ici
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
    public function viewAnnonce(int $id): void
    {
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
    public function createAnnonce(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 🔐 CSRF
            $this->checkCsrfToken();

            try {
                // Validation basique des données requises
                $requiredFields = [
                    'titre',
                    'description',
                    'mission',
                    'profil_recherche',
                    'localisation',
                    'code_postale',
                    'secteur_activite',
                    'type_contrat'
                ];

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
    public function updateAnnonce(?int $id): void
    {
        // Si c'est une soumission POST, on traite la mise à jour
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            // 🔐 CSRF
            $this->checkCsrfToken();

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

        // Affichage du formulaire de modification (GET)
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
    public function deleteAnnonce(int $id): void
    {
        // On impose une requête POST + CSRF pour la suppression
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=annonce&error=delete_error");
            exit;
        }

        // 🔐 CSRF
        $this->checkCsrfToken();

        // Id prioritaire depuis POST (hidden)
        $id = isset($_POST['id']) ? (int)$_POST['id'] : $id;

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
     * Archive une annonce
     */
    public function archiveAnnonce(int $id): void
    {
        // On impose une requête POST + CSRF
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=annonce&error=archive_error");
            exit;
        }

        $this->checkCsrfToken();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : $id;

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
    public function activateAnnonce(int $id): void
    {
        // On impose une requête POST + CSRF
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=annonce&error=activate_error");
            exit;
        }

        $this->checkCsrfToken();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : $id;

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
     * Affiche les messages de succès/erreur
     */
    public function displayMessages(): void
    {
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
}