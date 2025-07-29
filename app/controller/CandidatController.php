<?php

namespace App\Controller;

use App\Model\CandidatModel;
use App\View\CandidatView;

class CandidatController
{
    private CandidatModel $model;
    private CandidatView $view;

    private function redirectIfNotConnected(): bool
    {
        if (!isset($_SESSION['utilisateur']) || !isset($_SESSION['utilisateur']['id'])) {
            echo "Vous allez être redirigé...";
            header("Location: utilisateur/login");
            exit;
        }
        return true;
    }
    
    public function __construct()
    {
        $this->model = new CandidatModel();
        $this->view = new CandidatView();
    }

    private function isConnected(): bool
    {
        return isset($_SESSION['utilisateur']) && isset($_SESSION['utilisateur']['id']);
    }


    public function dashboard(): void
    {
        $this->redirectIfNotConnected();
        $id = $_SESSION['utilisateur']['id'];
        $donnees = [
            'profil' => $this->model->getProfil($id),
            'annonces' => $this->model->getAnnoncesDisponibles(),
            'candidatures' => $this->model->getCandidatures($id),
        ];

        $this->view->renderDashboard($donnees);
    }

    public function profil(): void
    {
        $this->redirectIfNotConnected();
        $profil = $this->model->getProfil((int)$_SESSION['utilisateur']['id']);
        $this->view->renderProfil($profil);
        $this->view->renderEditForm($profil);
        $this->view->renderUploadForm();
        $this->view->renderDeleteButton();
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->createProfil($_POST);
            echo "<p>✅ Profil créé avec succès</p>";
        } else {
            echo "<h2>Créer mon profil</h2>";
            echo "<form method='POST'>
                <label>Nom : <input name='nom' /></label><br>
                <label>Email : <input name='email' /></label><br>
                <label>Mot de passe : <input name='mot_de_passe' type='password' /></label><br>
                <button type='submit'>Créer</button>
            </form>";
        }
    }

    public function delete(): void
    {
        $this->redirectIfNotConnected();
        $id = $_SESSION['utilisateur']['id'];
        $this->model->deleteProfil($id);
        session_destroy();
        echo "<p>🗑️ Profil supprimé</p>";
    }

    public function update(): void
    {
        $this->redirectIfNotConnected();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateProfil($_SESSION['utilisateur']['id'], $_POST);
            header('Location: /candidat/profil');
            exit;
        }
    }

    public function uploadCV(): void
    {
        $this->redirectIfNotConnected();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {
            $cvPath = 'uploads/' . basename($_FILES['cv']['name']);
            move_uploaded_file($_FILES['cv']['tmp_name'], $cvPath);
            echo "<p>📄 CV téléchargé avec succès !</p>";
        }
    }

    public function listAnnonces(): void
    {
        $annonces = $this->model->getAnnoncesDisponibles();
        $this->view->renderAnnonces($annonces);
    }

    public function viewAnnonce(int $id): void
    {
        $annonce = $this->model->getAnnonceById($id);
        $this->view->renderAnnonce($annonce);
    }

    public function postuler(int $id): void
    {
        $this->redirectIfNotConnected();
        $this->model->envoyerCandidature($_SESSION['utilisateur']['id'], $id);
        header("Location: /candidat/candidatures");
        exit;
    }

    public function suiviCandidatures(): void
    {
        $this->redirectIfNotConnected();
        $candidatures = $this->model->getCandidatures($_SESSION['utilisateur']['id']);
        $this->view->renderCandidatures($candidatures);
    }
}
