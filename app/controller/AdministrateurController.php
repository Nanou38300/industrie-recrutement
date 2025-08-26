<?php

namespace App\Controller;

use App\Model\UtilisateurModel;
use App\Model\AnnonceModel;
use App\Model\CandidatureModel;
use App\Model\EntretienModel;
use App\View\AdministrateurView;
use App\View\CalendrierView;

class AdministrateurController
{
    private UtilisateurModel $userModel;
    private AnnonceModel $annonceModel;
    private CandidatureModel $candidatureModel;
    private EntretienModel $entretienModel;
    private AdministrateurView $view;
    private CalendrierView $calendarView;

    public function __construct()
    {
        $this->userModel        = new UtilisateurModel();
        $this->annonceModel     = new AnnonceModel();
        $this->candidatureModel = new CandidatureModel();
        $this->entretienModel   = new EntretienModel();
        $this->view             = new AdministrateurView();
        $this->calendarView     = new CalendrierView();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 🔐 Vérifie si l'utilisateur est admin
    private function redirectIfNotAdmin(): void
    {
        if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'administrateur') {
            header("Location: /utilisateur/login");
            exit;
        }
    }

    // 👤 Profil administrateur + calendrier
    public function profil(): void
    {
        $this->redirectIfNotAdmin();
        $idAdmin = $_SESSION['utilisateur']['id'];
    
        $infos = $this->userModel->getById($idAdmin);
        $statsAnnonces = $this->annonceModel->getStatsByAdmin($idAdmin);
        $annoncesStats = $this->annonceModel->getAnnoncesAvecStats($idAdmin);

        $rendezVous = $this->entretienModel->getByAdmin($idAdmin); // ← ici
        $jour = new \DateTimeImmutable();
        $debutSemaine = $jour->modify('monday this week')->format('Y-m-d');
        $finSemaine   = $jour->modify('sunday this week')->format('Y-m-d');
        
        $entretiensSemaine = $this->entretienModel->getEntretiensSemaine($idAdmin, $debutSemaine, $finSemaine);
        
        $this->view->renderProfil([
            'infos' => $infos,
            'statsAnnonces' => $statsAnnonces,
            'rendezVous' => $rendezVous
        ]);
    }
    
    
    public function editProfil(): void
    {
        $this->redirectIfNotAdmin();
        $id = $_SESSION['utilisateur']['id'];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->userModel->updateProfil($id, $_POST);
    
            if ($success) {
                echo "<div class='alert alert-success'>✅ Profil mis à jour avec succès.</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Échec de la mise à jour du profil.</div>";
            }
        }
    
        $profil = $this->userModel->getById($id);
        $this->view->renderFormProfil($profil);
    }
    public function deleteProfil(): void
{
    $this->redirectIfNotAdmin();
    $id = $_SESSION['utilisateur']['id'];

    $success = $this->userModel->deleteUtilisateur($id);

    if ($success) {
        session_destroy();
        echo "<div class='alert alert-info'>🗑️ Profil supprimé. Vous avez été déconnecté.</div>";
        header("Refresh: 2; URL=/utilisateur/login");
        exit;
    } else {
        echo "<div class='alert alert-danger'>❌ Échec de la suppression du profil.</div>";
    }
}

    // 📊 Tableau de bord
    public function dashboard(): void
    {
        $this->redirectIfNotAdmin();

        $stats = [
            'totalUtilisateurs' => count($this->userModel->selectUtilisateurs()),
            'totalAnnonces'     => $this->annonceModel->countAll(),
            'totalCandidatures' => $this->candidatureModel->countAll()
        ];

        $this->view->renderDashboard($stats);
    }

    // 📢 Liste des annonces
    public function viewAnnonces(): void
    {
        $this->redirectIfNotAdmin();
        $idAdmin = $_SESSION['utilisateur']['id'];
        $statut = $_GET['statut'] ?? null;
    
        $annonces = $this->annonceModel->getByAdministrateur($idAdmin, $statut);
        $this->view->renderAnnonces($annonces);
    }
    

    // ➕ Créer une annonce
    public function createAnnonce(): void
    {
        $this->redirectIfNotAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->annonceModel->create($_POST);

            if ($result !== false) {
                echo "<div class='alert alert-success'>✅ Annonce créée avec succès.</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Échec de la création. Vérifiez les champs obligatoires.</div>";
            }
        }

        $this->view->renderFormAnnonce();
    }

    // ✏️ Modifier une annonce
    public function editAnnonce(int $id): void
    {
        $this->redirectIfNotAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->annonceModel->update($id, $_POST);

            if ($success) {
                echo "<div class='alert alert-success'>✅ Annonce mise à jour.</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Échec de la mise à jour.</div>";
            }
        }

        $annonce = $this->annonceModel->getById($id);
        $this->view->renderFormAnnonce($annonce);
    }

    // 📦 Archiver une annonce
    public function archiveAnnonce(int $id): void
    {
        $this->redirectIfNotAdmin();
        $this->annonceModel->archive($id);
        echo "<div class='alert alert-info'>📦 Annonce archivée.</div>";
    }

    // 📋 Liste des candidatures
    public function listCandidatures(): void
    {
        $this->redirectIfNotAdmin();
        $candidatures = $this->candidatureModel->findAll();
        $this->view->renderListeCandidatures($candidatures);
    }

    // 👁️ Détail d’une candidature
    public function viewCandidature(int $id): void
    {
        $this->redirectIfNotAdmin();
        $candidature = $this->candidatureModel->findById($id);
        $this->view->renderDetailsCandidature($candidature);
    }

    public function calendrier(): void
{
    $this->redirectIfNotAdmin();
    $idAdmin = $_SESSION['utilisateur']['id'];

    // Récupération des entretiens du jour
    $aujourdHui = date('Y-m-d');
    $entretiensDuJour = $this->entretienModel->getByDateAdmin($idAdmin, $aujourdHui);

    // Récupération de l’entretien sélectionné (ex: via GET)
    $entretienId = $_GET['id'] ?? null;
    $entretien = $entretienId ? $this->entretienModel->findById($entretienId) : null;

    // Récupération du candidat lié à l’entretien
    $candidat = $entretien ? $this->userModel->getById($entretien['id_utilisateur']) : [];

    $this->view->renderCalendrier($candidat, $entretien, $entretiensDuJour);
}

    // 📅 Vue calendrier
    public function vueCalendrier(): void
    {
        $this->redirectIfNotAdmin();
        $mois = date('m');
        $annee = date('Y');
        $entretiens = $this->entretienModel->getByMonth((int)$mois, (int)$annee);
        $this->calendarView->renderCalendrier($entretiens, $mois, $annee);
    }

    // 🔔 Rappel du jour
    public function rappelDuJour(): void
    {
        $this->redirectIfNotAdmin();
        $aujourdHui = date('Y-m-d');
        $rappels = $this->entretienModel->getByDate($aujourdHui);
        foreach ($rappels as $entretien) {
            $this->calendarView->renderRappel($entretien);
        }
    }
}
