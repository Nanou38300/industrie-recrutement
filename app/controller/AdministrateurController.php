<?php
namespace App\Controller;

use App\Model\AdministrateurModel;
use App\View\AdministrateurView;
use App\Model\CandidatureModel;
use App\Model\UtilisateurModel;
use App\Model\AnnonceModel;

class AdministrateurController 
{
    public AdministrateurModel $administrateurModel;
    public AdministrateurView $administrateurView;
    public UtilisateurModel $utilisateurModel;
    public CandidatureModel $candidatureModel;
    public AnnonceModel $annonceModel;

    public function __construct()
    {
        $this->administrateurModel = new AdministrateurModel();
        $this->administrateurView = new AdministrateurView();
        $this->utilisateurModel = new UtilisateurModel();
        $this->candidatureModel = new CandidatureModel();
        $this->annonceModel = new AnnonceModel();
    }

    // public function dashboard(int $adminId): void
    // {
    //     $stats = [
    //         'total_utilisateurs' => $this->utilisateurModel->countUtilisateurs(),
    //         'total_annonces' => $this->annonceModel->countAnnonces(),
    //         'total_candidatures' => $this->candidatureModel->countCandidatures(),
    //     ];

    //     $rendezVous = $this->administrateurModel->getRendezVousHebdo();
    //     $this->administrateurView->renderDashboard($stats);
    //     $this->administrateurView->renderCalendrierHebdo($rendezVous);
    // }

    public function profil(int $adminId): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->administrateurModel->updateProfil($_POST);
            header('Location: /administrateur/profil?id=' . $adminId);
            exit;
        }

        $profil = $this->administrateurModel->getProfil($adminId);
        $this->administrateurView->renderProfilForm($profil);



    }

    public function editProfil(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->utilisateurModel->updateProfil($_POST);
            header('Location: /administrateur/profil');
            exit;
        }

        $profil = $this->utilisateurModel->getProfil($_SESSION['user_id']);
        $this->administrateurView->render('admin/profil.php', ['profil' => $profil]);
    }

    public function viewAnnonces(): void
    {
        $annonces = $this->annonceModel->getAll();
        $this->administrateurView->render('admin/annonces/liste.php', ['annonces' => $annonces]);
    }

    public function createAnnonce(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->annonceModel->create($_POST);
            header('Location: /administrateur/annonces');
            exit;
        }

        $this->administrateurView->render('administrateur/annonces/formulaire.php');
    }

    public function editAnnonce(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->annonceModel->update($id, $_POST);
            header('Location: /administrateur/annonces');
            exit;
        }

        $annonce = $this->annonceModel->find($id);
        $this->administrateurView->render('administrateur/annonces/formulaire', ['annonce' => $annonce]);
    }

    public function archiveAnnonce(int $id): void
    {
        $this->annonceModel->archive($id);
        header('Location: /administrateur/annonces');
        exit;
    }

    public function listCandidatures(): void
    {
        $candidatures = $this->candidatureModel->getAll();
        $this->administrateurView->render('administrateur/candidatures/liste', ['candidatures' => $candidatures]);
    }

    public function viewCandidature(int $id): void
    {
        $candidature = $this->candidatureModel->find($id);
        $this->administrateurView->render('administrateur/candidatures/detail', ['candidature' => $candidature]);
    }
}
