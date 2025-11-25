<?php
// Définit le namespace du contrôleur
namespace App\Controller;

// Importe les classes nécessaires depuis d'autres namespaces
use App\Model\UtilisateurModel;
use App\View\UtilisateurView;
use App\Security;

class UtilisateurController
{
    // Propriétés privées pour le modèle et la vue
    private UtilisateurModel $utilisateurModel;
    private UtilisateurView $utilisateurView;

    // Constructeur qui initialise les instances du modèle et de la vue
    public function __construct()
    {
        // ✅ Sessions gérées dans index.php
        $this->utilisateurModel = new UtilisateurModel();
        $this->utilisateurView  = new UtilisateurView();
    }

    // ✅ Plus besoin de checkCsrfToken(), on utilise Security::validateCSRFToken()

    // Méthode privée qui vérifie si un utilisateur est connecté
    private function isAuthenticated(): bool
    {
        return isset($_SESSION['utilisateur']);
    }

    // Méthode privée pour savoir si l'utilisateur est administrateur
    private function isAdmin(): bool
    {
        return $this->isAuthenticated() && (($_SESSION['utilisateur']['role'] ?? '') === 'administrateur');
    }

    // Méthode privée pour savoir si l'utilisateur est candidat
    private function isCandidat(): bool
    {
        return $this->isAuthenticated() && (($_SESSION['utilisateur']['role'] ?? '') === 'candidat');
    }

    private function isAdminEmail(string $email): bool
    {
        $list = $_ENV['ADMIN_EMAILS'] ?? '';
        if ($list === '') {
            return false;
        }

        $allowed = array_filter(array_map('trim', explode(';', $list)));
        $emailLower = strtolower($email);

        foreach ($allowed as $allowedEmail) {
            if ($emailLower === strtolower($allowedEmail)) {
                return true;
            }
        }

        return false;
    }

    // ─────────────────────────────────────
    // 👤 Création d'un nouvel utilisateur
    // ─────────────────────────────────────
    public function createUtilisateur(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 🔐 CSRF centralisé
            Security::validateCSRFToken();

            // ⚠️ Tu peux ajouter ici des validations supplémentaires si besoin
            $result = $this->utilisateurModel->insertUtilisateur(
                $_POST['nom']            ?? '',
                $_POST['prenom']         ?? '',
                $_POST['email']          ?? '',
                $_POST['mot_de_passe']   ?? '',
                $_POST['date_naissance'] ?? '',
                (int)($_POST['telephone'] ?? 0)
            );

            if (!$result) {
                $_SESSION['flash'] = "Un compte existe déjà avec cette adresse e-mail.";
                header('Location: /utilisateur/create');
                exit;
            }

            // Récupérer l'utilisateur pour le connecter
            $utilisateur = $this->utilisateurModel->loginUtilisateur($_POST['email'] ?? '');
            if ($utilisateur) {
                // Même logique de rôle que dans loginUtilisateur()
                $role = $this->isAdminEmail($utilisateur['email']) ? 'administrateur' : 'candidat';

                $_SESSION['utilisateur'] = [
                    'id'     => $utilisateur['id'],
                    'nom'    => $utilisateur['nom'],
                    'prenom' => $utilisateur['prenom'],
                    'email'  => $utilisateur['email'],
                    'role'   => $role,
                ];

                if ($role === 'administrateur') {
                    header('Location: /administrateur/dashboard');
                } else {
                    header('Location: /candidat/profil');
                }
                exit;
            }

            // Fallback si, pour une raison quelconque, la connexion auto échoue
            echo "<p>Utilisateur créé avec succès, mais la connexion automatique a échoué.</p>";
        } else {
            // GET : affiche le formulaire d'inscription
            $this->utilisateurView->displayInsertForm();
        }
    }

    // ─────────────────────────────────────
    // 📝 Modification d'un utilisateur
    // ─────────────────────────────────────
    public function editUtilisateur(int $id): void
    {
        if (!$this->isAuthenticated()) {
            echo '<h1>Vous devez être connecté pour modifier un utilisateur.</h1>';
            return;
        }

        // Seul l'admin ou l'utilisateur lui-même peut modifier ses infos
        if (!$this->isAdmin() && ($_SESSION['utilisateur']['id'] ?? 0) != $id) {
            echo '<h1>Accès refusé : vous ne pouvez modifier que votre propre profil.</h1>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 🔐 CSRF centralisé
            Security::validateCSRFToken();

            $this->utilisateurModel->updateUtilisateur(
                $_POST['id']        ?? $id,
                $_POST['nom']       ?? '',
                $_POST['prenom']    ?? '',
                $_POST['email']     ?? '',
                $_POST['telephone'] ?? ''
            );

            echo "<p>Modification réussie.</p>";
        } else {
            // GET : affiche le formulaire pré-rempli
            $utilisateur = $this->utilisateurModel->selectUtilisateur($id);
            if ($utilisateur) {
                $this->utilisateurView->displayUpdateForm($utilisateur);
            } else {
                echo "Utilisateur introuvable.";
            }
        }
    }

    // ─────────────────────────────────────
    // 🔑 Connexion utilisateur
    // ─────────────────────────────────────
    public function loginUtilisateur(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 🔐 CSRF centralisé
            Security::validateCSRFToken();

            // 🛑 Rate limiting : max 5 tentatives en 5 minutes
            if (!Security::rateLimitCheck('login', 5, 300)) {
                Security::logSecurityEvent('login_rate_limited', [
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'email' => $_POST['email'] ?? 'unknown'
                ]);
                echo "<p style='color:red;'>⚠️ Trop de tentatives de connexion. Réessayez dans 5 minutes.</p>";
                $this->utilisateurView->loginForm();
                return;
            }

            $email        = $_POST['email']        ?? '';
            $mot_de_passe = $_POST['mot_de_passe'] ?? '';

            $utilisateur = $this->utilisateurModel->loginUtilisateur($email);

            if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                // Vérification du domaine CTS pour le rôle admin
                $role = $this->isAdminEmail($utilisateur['email']) ? 'administrateur' : 'candidat';

                // Création de la session
                $_SESSION['utilisateur'] = [
                    'id'      => $utilisateur['id'],
                    'nom'     => $utilisateur['nom'],
                    'prenom'  => $utilisateur['prenom'],
                    'email'   => $utilisateur['email'],
                    'role'    => $role
                ];

                // ✅ Log de succès
                Security::logSecurityEvent('login_success', [
                    'user_id' => $utilisateur['id'],
                    'email' => $utilisateur['email'],
                    'role' => $role,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                // Régénérer l'ID de session pour sécurité
                session_regenerate_id(true);

                // Redirection selon le rôle
                if ($role === 'administrateur') {
                    header('Location: /administrateur/dashboard');
                } else {
                    header('Location: /candidat/profil');
                }
                exit;
            } else {
                // ❌ Identifiants invalides
                Security::logSecurityEvent('login_failed', [
                    'email' => $email,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
                echo "<p style='color:red;'>Email ou mot de passe incorrect.</p>";
                $this->utilisateurView->loginForm();
            }
        } else {
            // GET : affiche le formulaire de connexion
            $this->utilisateurView->loginForm();
        }
    }

    // ─────────────────────────────────────
    // 🚪 Déconnexion
    // ─────────────────────────────────────
    public function logoutUtilisateur(): void
    {
        session_unset();
        session_destroy();
        header("Location: /utilisateur/login");
        exit;
    }

    // ─────────────────────────────────────
    // 🗑️ Suppression d'un utilisateur (admin)
    // ─────────────────────────────────────
    public function deleteUtilisateur($id): void
    {
        if (!$this->isAdmin()) {
            echo '<h1>Accès refusé : seuls les administrateurs peuvent supprimer des utilisateurs.</h1>';
            return;
        }

        // On impose une requête POST + CSRF pour la suppression
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "<p>Requête invalide.</p>";
            return;
        }

        // 🔐 CSRF centralisé
        Security::validateCSRFToken();

        Security::logSecurityEvent('user_deleted', [
            'deleted_user_id' => $id,
            'admin_id' => $_SESSION['utilisateur']['id'] ?? 'unknown'
        ]);

        $this->utilisateurModel->deleteUtilisateur($id);
        echo "<p>Utilisateur supprimé.</p>";
    }
}
?>
