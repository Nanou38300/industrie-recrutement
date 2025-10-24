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
public function logout(): void
{
    session_destroy();
    header("Location: /");
    exit;
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
    
        $aujourdHui = date('Y-m-d');
        $entretiensDuJour = $this->entretienModel->getByDateAdmin($idAdmin, $aujourdHui);
    
        $entretienId = $_GET['id'] ?? null;
        $entretien = $entretienId ? $this->entretienModel->findById($entretienId) : null;
        $candidat = $entretien ? $this->userModel->getById($entretien['id_utilisateur']) : [];
    
        require 'app/View/calendar.php';
    }
    
    public function vueCalendrier(): void
    {
        $this->redirectIfNotAdmin();
        $mois = date('m');
        $annee = date('Y');
        $entretiens = $this->entretienModel->getByMonth((int)$mois, (int)$annee);
        $this->calendarView->renderCalendrier($entretiens, $mois, $annee);
    }
    
    public function apiRdv(): void {
        $model = new \App\Model\EntretienModel();
        $events = $model->getAllRdv();
    
        foreach ($events as &$event) {
            if (strlen($event['start']) === 16) {
                $event['start'] .= ':00';
            }
        }
    
        header('Content-Type: application/json');
        echo json_encode($events);
        exit; // ← Ajoute ceci pour stopper tout traitement après
    }
    
    
    public function viewRdv(int $id): void
    {
        $this->redirectIfNotAdmin();
        $entretien = $this->entretienModel->findById($id);
        $candidat = $this->userModel->getById($entretien['id_utilisateur']);
        require 'app/View/rdv-detail.php';
    }
    
    public function creerEntretien(): void
{
    $this->redirectIfNotAdmin();

    $dateISO = $_GET['date'] ?? null;
    $date = $dateISO ? substr($dateISO, 0, 10) : '';
    $heure = $dateISO ? substr($dateISO, 11, 5) : '';

    $annonces = $this->annonceModel->getByAdmin($_SESSION['utilisateur']['id']);
    $candidats = $this->userModel->getAllCandidats();
    echo "<h1>🧪 Test : chargement du formulaire</h1>";

    $this->calendarView->renderFormCreation($date, $heure, $annonces, $candidats);
}

public function validerEntretien(): void
{
    $this->redirectIfNotAdmin();

    $data = [
        'id_utilisateur'   => $_POST['id_utilisateur'] ?? null,
        'date_entretien'   => $_POST['date_entretien'] ?? null,
        'heure'            => $_POST['heure'] ?? null,
        'type'             => $_POST['type'] ?? '',
        'lien_visio'       => $_POST['lien_visio'] ?? null,
        'commentaire'      => $_POST['commentaire'] ?? null
    ];
    if ($data['id_utilisateur'] && $data['date_entretien'] && $data['heure'] && $data['type']) {
        $this->entretienModel->create($data);
        echo "<div class='alert alert-success'>✅ Entretien planifié avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Données manquantes pour planifier l’entretien.</div>";
    }
    

    header("Refresh: 2; URL=/administrateur/calendrier");
    exit;
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


    public function updateStatut(): void
    {
        $id = $_POST['id_candidature'] ?? null;
        $statut = $_POST['statut'] ?? null;
    
        if ($id && $statut) {
            $this->model->updateStatutCandidature((int)$id, $statut);
            $_SESSION['message'] = "✅ Statut mis à jour.";
        }
    
        header("Location: /administrateur/candidatures");
        exit;
    }
    
    public function editEntretien(): void
{
    $this->redirectIfNotAdmin();

    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo "<div class='alert alert-danger'>❌ Entretien introuvable.</div>";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'date_entretien' => $_POST['date_entretien'] ?? null,
            'heure'          => $_POST['heure'] ?? null,
            'type'           => $_POST['type'] ?? '',
            'lien_visio'     => $_POST['lien_visio'] ?? null,
            'commentaire'    => $_POST['commentaire'] ?? null
        ];

        if ($data['date_entretien'] && $data['heure'] && $data['type']) {
            $success = $this->entretienModel->update((int)$id, $data);

            if ($success) {
                echo "<div class='alert alert-success'>✅ Entretien mis à jour.</div>";
                header("Refresh: 2; URL=/administrateur/vue-calendrier");
                exit;
            } else {
                echo "<div class='alert alert-danger'>❌ Échec de la mise à jour.</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>⚠️ Champs obligatoires manquants.</div>";
        }
    }

    $entretien = $this->entretienModel->findById((int)$id);
    $this->calendarView->renderFormModification($entretien);
}


}
