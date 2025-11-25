<?php

namespace App\Controller;

use App\Model\CandidatureModel;
use App\View\CandidatureView;

class CandidatureController
{
    private CandidatureModel $model;
    private CandidatureView $view;

    public function __construct(
    ?CandidatureModel $model = null,
    ?CandidatureView $view = null
) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $this->model = $model ?? new CandidatureModel();
    $this->view  = $view  ?? new CandidatureView();
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

    // Vérifie si l'utilisateur est connecté
    private function redirectIfNotConnected(): void
    {
        if (!isset($_SESSION['utilisateur']['id'])) {
            header("Location: /utilisateur/login");
            exit;
        }
    }

    // Soumission d'une candidature (candidat)
    public function submitCandidature(): void
    {
        $this->redirectIfNotConnected();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_annonce'])) {
            // 🔐 CSRF
            $this->checkCsrfToken();

            $this->model->create([
                'id_utilisateur'    => (int)$_SESSION['utilisateur']['id'],
                'id_annonce'        => (int)$_POST['id_annonce'],
                'commentaire_admin' => ''
            ]);

            header("Location: /candidat/candidatures");
            exit;
        }

        // Appel incorrect → retour au suivi
        header("Location: /candidat/candidatures");
        exit;
    }

    // Vue détaillée d'une candidature (admin ou candidat selon tes routes)
    public function viewCandidature(int $id): void
    {
        $this->redirectIfNotConnected();

        $candidature = $this->model->findById($id);
        if ($candidature) {
            $this->view->renderDetails($candidature);
        } else {
            echo "<div class='alert alert-warning'>⚠️ Candidature introuvable.</div>";
        }
    }

    // Suppression d'une candidature
    public function deleteCandidature(int $id): void
    {
        $this->redirectIfNotConnected();

        // Suppression uniquement en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /administrateur/candidatures");
            exit;
        }

        // 🔐 CSRF
        $this->checkCsrfToken();

        // Ici tu peux ajouter une vérification : admin ou propriétaire
        $this->model->delete($id);

        $_SESSION['flash'] = "✅ Candidature supprimée.";
        $_SESSION['flash_type'] = 'success';
        header("Location: /administrateur/candidatures");
        exit;
    }

    // Suivi des candidatures (candidat)
    public function suivi(): void
    {
        $this->redirectIfNotConnected();
        $candidatures = $this->model->findByUtilisateur((int)$_SESSION['utilisateur']['id']);
        $this->view->renderSuivi($candidatures);
    }

    // Liste des candidatures (admin)
    public function listCandidatures(): void
    {
        $this->redirectIfNotConnected();

        if (($_SESSION['utilisateur']['role'] ?? '') !== 'administrateur') {
            echo "<div class='alert alert-danger'>⛔ Accès réservé aux administrateurs.</div>";
            return;
        }

        $candidatures = $this->model->findAll();
        $this->view->renderListe($candidatures);
    }

    // ✏️ Mise à jour du statut (admin)
    public function updateStatut(): void
    {
        $this->redirectIfNotConnected();

        // Admin uniquement
        if (($_SESSION['utilisateur']['role'] ?? '') !== 'administrateur') {
            header('Location: /administrateur/candidatures');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['statut'])) {
            // 🔐 CSRF
            $this->checkCsrfToken();

            $id      = (int)$_POST['id'];
            $statut  = mb_strtolower(trim((string)$_POST['statut'])); // normalisation
            $comment = $_POST['commentaire_admin'] ?? '';

            $ok = $this->model->update($id, [
                'statut'            => $statut,
                'commentaire_admin' => $comment
            ]);

            $_SESSION['flash'] = $ok
                ? "✅ Statut de la candidature mis à jour."
                : "❌ Statut invalide.";
            $_SESSION['flash_type'] = $ok ? 'success' : 'error';

            header('Location: /administrateur/candidatures');
            exit;
        }

        // Optionnel : fallback si appel incorrect
        header('Location: /administrateur/candidatures');
        exit;
    }
}