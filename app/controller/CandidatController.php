<?php

namespace App\Controller;

use App\Model\CandidatModel;
use App\View\CandidatView;

class CandidatController
{
    private CandidatModel $model;
    private CandidatView $view;

    public function __construct()
    {
        $this->model = new CandidatModel();
        $this->view = new CandidatView();
    }

    private function redirectIfNotConnected(): void
    {
        if (!isset($_SESSION['utilisateur']['id'])) {
            header("Location: /utilisateur/login");
            exit;
        }
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
        $profil = $this->model->getProfil($_SESSION['utilisateur']['id']);
        $this->view->renderProfil($profil);
        $this->view->renderEditForm($profil);
        $this->view->renderUploadForm();
        $this->view->renderDeleteButton();
    }

    public function update(): void
    {
        $this->redirectIfNotConnected();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateProfil($_SESSION['utilisateur']['id'], $_POST);
            header("Location: /candidat/profil");
            exit;
        }
    }

    public function delete(): void
    {
        $this->redirectIfNotConnected();
        $this->model->deleteProfil($_SESSION['utilisateur']['id']);
        session_destroy();
        header("Location: /utilisateur/login");
        exit;
    }

    public function uploadCV(): void
    {
        $this->redirectIfNotConnected();
        if (isset($_FILES['cv'])) {
            $filename = time() . '-' . basename($_FILES['cv']['name']);
            $destination = 'uploads/' . $filename;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            if (move_uploaded_file($_FILES['cv']['tmp_name'], $destination)) {
                $this->model->updateCV($_SESSION['utilisateur']['id'], $filename);
                header("Location: /candidat/profil");
                exit;
            } else {
                echo "<p>❌ Échec du téléchargement du CV.</p>";
            }
        }
    }

    public function uploadPhoto(): void
    {
        $this->redirectIfNotConnected();
        if (isset($_FILES['photo'])) {
            $filename = time() . '-' . basename($_FILES['photo']['name']);
            $destination = 'uploads/' . $filename;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $this->model->updatePhoto($_SESSION['utilisateur']['id'], $destination);
                header("Location: /candidat/profil");
                exit;
            } else {
                echo "<p>❌ Échec de l’envoi de la photo.</p>";
            }
        }
    }


        public function listAnnonces(): void
    {
        $annonces = $this->model->getAnnoncesDisponibles();
        $this->view->renderAnnonces($annonces);
    }


    public function postuler(int $id): void
    {
        $this->redirectIfNotConnected();
        $this->model->envoyerCandidature($_SESSION['utilisateur']['id'], $id);
        header("Location: /candidat/candidatures");
        exit;
    }

    public function renderSuiviCandidatures(): void
{
    $this->redirectIfNotConnected();
    $id = $_SESSION['utilisateur']['id'];
    $candidatures = $this->model->getCandidatures($id);
    $this->view->renderSuiviCandidatures($candidatures);
}


}
