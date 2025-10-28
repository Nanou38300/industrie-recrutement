<?php

namespace App\Controller;

use App\Model\CandidatureModel;
use App\View\CandidatureView;

class CandidatureController
{
    private CandidatureModel $model;
    private CandidatureView $view;

    public function __construct()
    {
        $this->model = new CandidatureModel();
        $this->view = new CandidatureView();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 🔐 Vérifie si l'utilisateur est connecté
    private function redirectIfNotConnected(): void
    {
        if (!isset($_SESSION['utilisateur']['id'])) {
            header("Location: /utilisateur/login");
            exit;
        }
    }

    // 📥 Soumission d'une candidature (candidat)
    public function submitCandidature(): void
    {
        $this->redirectIfNotConnected();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_annonce'])) {
            $this->model->create([
                'id_utilisateur' => $_SESSION['utilisateur']['id'],
                'id_annonce'     => (int)$_POST['id_annonce'],
                'commentaire_admin' => ''
            ]);
            header("Location: /candidat/candidatures");
            exit;
        }
    }

    // 👁️ Vue détaillée d'une candidature (admin)
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

    // 🗑️ Suppression d'une candidature
    public function deleteCandidature(int $id): void
    {
        $this->redirectIfNotConnected();
        $this->model->delete($id);
        echo "<div class='alert alert-success'>✅ Candidature supprimée.</div>";
    }

    // 📊 Suivi des candidatures (candidat)
    public function suivi(): void
    {
        $this->redirectIfNotConnected();
        $candidatures = $this->model->findByUtilisateur($_SESSION['utilisateur']['id']);
        $this->view->renderSuivi($candidatures);
    }

    // 📋 Liste des candidatures (admin)
    public function listCandidatures(): void
    {
        $this->redirectIfNotConnected();
        if ($_SESSION['utilisateur']['role'] !== 'administrateur') {
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
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['statut'])) {
            $id      = (int) $_POST['id'];
            $statut  = mb_strtolower(trim((string)$_POST['statut'])); // ← normalisation
            $comment = $_POST['commentaire_admin'] ?? '';
    
            $ok = $this->model->update($id, [
                'statut'            => $statut,
                'commentaire_admin' => $comment
            ]);
    
            echo $ok
                ? "<div class='alert alert-success'>✅ Statut mis à jour.</div>"
                : "<div class='alert alert-danger'>❌ Statut invalide.</div>";
        }
    }
}
