<?php
// Définit le namespace du contrôleur
namespace App\Controller;

// Importe les classes nécessaires depuis d'autres namespaces
use App\Model\UtilisateurModel;
use App\View\UtilisateurView;
use App\Database;

// Déclare la classe contrôleur UtilisateurController
class UtilisateurController
{
    // Déclare les propriétés privées pour le modèle et la vue
    private UtilisateurModel $utilisateurModel;
    private UtilisateurView $utilisateurView;

    // Constructeur qui initialise les instances du modèle et de la vue
    public function __construct()
    {
        $this->utilisateurModel = new UtilisateurModel();
        $this->utilisateurView = new UtilisateurView();
    }

    // Méthode privée qui vérifie si un utilisateur est connecté
    private function isAuthenticated(): bool
    {
        return isset($_SESSION['utilisateur']);
    }

    // Méthode privée pour savoir si l'utilisateur est administrateur
    private function isAdmin(): bool
    {
        return $this->isAuthenticated() && ($_SESSION['utilisateur']['role'] ?? '') === 'administrateur';
    }

    // Méthode privée pour savoir si l'utilisateur est candidat
    private function isCandidat(): bool
    {
        return $this->isAuthenticated() && ($_SESSION['utilisateur']['role'] ?? '') === 'candidat';
    }

    // Méthode pour afficher la liste des utilisateurs (admin uniquement)
    public function listUtilisateur(): void
    {
        if (!$this->isAdmin()) {
            echo '<h1>Accès refusé : seuls les administrateurs peuvent voir la liste des utilisateurs.</h1>';
            return;
        }

        $utilisateurs = $this->utilisateurModel->selectUtilisateurs();
        $this->utilisateurView->displayUtilisateurs($utilisateurs);
    }

    // Méthode pour créer un nouvel utilisateur
    public function createUtilisateur(): void
    {
        // Si le formulaire a été soumis (méthode POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->utilisateurModel->insertUtilisateur(
                $_POST['nom'],                // Nom saisi
                $_POST['prenom'],             // Prénom saisi
                $_POST['email'],              // Email saisi
                $_POST['mot_de_passe'],       // Mot de passe saisi
                $_POST['date_naissance'],     // Date de naissance
                (int) $_POST['telephone'],    // Téléphone converti en entier
                $_POST['role'] ?? 'candidat'  // Rôle, par défaut 'candidat'
            );

            echo "<p>Utilisateur créé avec succès.</p>";
        } else {
            // Affiche le formulaire si pas encore soumis
            $this->utilisateurView->displayInsertForm();
        }
    }

    // Méthode pour modifier un utilisateur existant
    public function editUtilisateur(int $id): void
    {
        if (!$this->isAuthenticated()) {
            echo '<h1>Vous devez être connecté pour modifier un utilisateur.</h1>';
            return;
        }

        // Seul l'admin ou l'utilisateur lui-même peut modifier ses infos
        if (!$this->isAdmin() && $_SESSION['utilisateur']['id'] != $id) {
            echo '<h1>Accès refusé : vous ne pouvez modifier que votre propre profil.</h1>';
            return;
        }

        // Traitement du formulaire (modification)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->utilisateurModel->updateUtilisateur(
                $_POST['id'],         // ID de l'utilisateur
                $_POST['nom'],        // Nouveau nom
                $_POST['prenom'],     // Nouveau prénom
                $_POST['email'],      // Nouvel email
                $_POST['telephone']   // Nouveau téléphone
            );

            echo "<p>Modification réussie.</p>";
        } else {
            // Affiche le formulaire pré-rempli avec les données existantes
            $utilisateur = $this->utilisateurModel->selectUtilisateur($id);
            if ($utilisateur) {
                $this->utilisateurView->displayUpdateForm($utilisateur);
            } else {
                echo "Utilisateur introuvable.";
            }
        }
    }

    // Méthode pour connecter un utilisateur
    public function loginUtilisateur(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';                // Email saisi
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';  // Mot de passe saisi

            $utilisateur = $this->utilisateurModel->loginUtilisateur($email);

            // Vérifie que l'utilisateur existe et que le mot de passe est valide
            if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                // Crée la session utilisateur avec les infos récupérées
                $_SESSION['utilisateur'] = [
                    'id' => $utilisateur['id'],
                    'nom' => $utilisateur['nom'],
                    'prenom' => $utilisateur['prenom'],
                    'email' => $utilisateur['email'],
                    'role' => $utilisateur['role'] ?? 'candidat'
                ];

                // Redirection vers la page d'accueil
                echo '<script>window.location.href = "/";</script>';
                exit;
            } else {
                // Affiche un message d'erreur si les identifiants sont invalides
                echo "<p style='color:red;'>Email ou mot de passe incorrect.</p>";
                $this->utilisateurView->loginForm();
            }
        } else {
            // Affiche le formulaire de connexion si pas encore soumis
            $this->utilisateurView->loginForm();
        }
    }

    // Méthode pour déconnecter un utilisateur
    public function logoutUtilisateur(): void
    {
        $this->utilisateurModel->logoutUtilisateur();  // Détruit la session
        echo '<script>window.location.href = "/";</script>';  // Redirige vers l'accueil
        exit;
    }

    // Méthode pour supprimer un utilisateur (admin uniquement)
    public function deleteUtilisateur($id): void
    {
        if (!$this->isAdmin()) {
            echo '<h1>Accès refusé : seuls les administrateurs peuvent supprimer des utilisateurs.</h1>';
            return;
        }

        $this->utilisateurModel->deleteUtilisateur($id);
        echo "<p>Utilisateur supprimé.</p>";
    }
}
?>
