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
                // (Optionnel) si ta vue profil lit la session quelque part :
                $_SESSION['utilisateur']['email']      = $_POST['email']      ?? $_SESSION['utilisateur']['email'];
                $_SESSION['utilisateur']['prenom']     = $_POST['prenom']     ?? $_SESSION['utilisateur']['prenom'];
                $_SESSION['utilisateur']['nom']        = $_POST['nom']        ?? $_SESSION['utilisateur']['nom'];
                $_SESSION['utilisateur']['telephone']  = $_POST['telephone']  ?? $_SESSION['utilisateur']['telephone'];
    
                // PRG : on redirige vers la page profil (GET)
                header('Location: /administrateur/profil');
                exit;
            } else {
                echo "<div class='alert alert-danger'>❌ Échec de la mise à jour du profil.</div>";
            }
        }
    
        // GET : afficher le formulaire avec les données fraiches
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

        // Petit gestionnaire de flash via query-string
        if (!empty($_GET['flash'])) {
            switch ($_GET['flash']) {
                case 'created':      echo "<div class='alert alert-success'>✅ Annonce créée.</div>"; break;
                case 'updated':      echo "<div class='alert alert-success'>✅ Annonce mise à jour.</div>"; break;
                case 'deleted':      echo "<div class='alert alert-success'>✅ Annonce supprimée.</div>"; break;
                case 'delete_failed':echo "<div class='alert alert-danger'>❌ Échec de la suppression.</div>"; break;
                case 'not_found':    echo "<div class='alert alert-warning'>⚠️ Annonce introuvable.</div>"; break;
                case 'bad_request':  echo "<div class='alert alert-warning'>⚠️ Requête invalide.</div>"; break;
            }
        }

        $annonces = $this->annonceModel->getByAdministrateur($idAdmin, $statut);
        $this->view->renderAnnonces($annonces);
    }
    

    // ➕ Créer une annonce
    public function createAnnonce(): void
    {
        $this->redirectIfNotAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ok = $this->annonceModel->create($_POST);

            if ($ok) {
                // PRG : redirection vers la liste avec message flash
                header("Location: /administrateur/annonces?flash=created");
                exit;
            }

            echo "<div class='alert alert-danger'>❌ Échec de la création. Vérifiez les champs obligatoires.</div>";
        }

        // GET : affichage du formulaire en mode "create"
        $this->view->renderFormAnnonce([], 'create');
    }


    // ✏️ Modifier une annonce
    public function editAnnonce(int $id): void
    {
        $this->redirectIfNotAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ceinture et bretelles : privilégie l'id POST si présent
            $targetId = isset($_POST['id']) ? (int)$_POST['id'] : $id;

            $ok = $this->annonceModel->update($targetId, $_POST);
            if ($ok) {
                // PRG
                header("Location: /administrateur/annonces?flash=updated");
                exit;
            }

            echo "<div class='alert alert-danger'>❌ Échec de la mise à jour.</div>";
        }

        // GET : récupérer l’annonce et afficher le formulaire en mode "update"
        $annonce = $this->annonceModel->getById($id);
        if (!$annonce) {
            header("Location: /administrateur/annonces?flash=not_found");
            exit;
        }

        $this->view->renderFormAnnonce($annonce, 'update');
    }

    // App/Controller/AdministrateurController.php
    public function deleteAnnonce(): void
    {
        $this->redirectIfNotAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
            header("Location: /administrateur/annonces?flash=bad_request");
            exit;
        }

        $id = (int)$_POST['id'];
        $annonce = $this->annonceModel->getById($id);

        if (!$annonce) {
            header("Location: /administrateur/annonces?flash=not_found");
            exit;
        }

        $ok = $this->annonceModel->delete($id);
        header("Location: /administrateur/annonces?flash=" . ($ok ? "deleted" : "delete_failed"));
        exit;
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
    if (!$entretien) {
        echo "<div class='alert alert-danger'>❌ Entretien introuvable (id={$id}).</div>";
        return;
    }

    // 🔐 Récupération robuste de l'id utilisateur
    $utilisateurId = (int)($entretien['id_utilisateur'] ?? 0);
    if ($utilisateurId <= 0) {
        echo "<div class='alert alert-danger'>❌ Entretien trouvé, mais aucun candidat associé (id_utilisateur manquant).</div>";
        // Tu peux sortir ici OU afficher une page réduite sans fiche-candidat.
        return;
    }

    $candidat = $this->userModel->getById($utilisateurId);
    if (!$candidat) {
        echo "<div class='alert alert-danger'>❌ Candidat introuvable (id={$utilisateurId}).</div>";
        return;
    }

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
        $this->redirectIfNotAdmin();
    
        $id = $_POST['id_candidature'] ?? $_POST['id'] ?? null;
        $statut = $_POST['statut'] ?? null;
        $commentaire = $_POST['commentaire_admin'] ?? '';
    
        if ($id && $statut) {
            $ok = $this->candidatureModel->update((int)$id, [
                'statut' => $statut,
                'commentaire_admin' => $commentaire
            ]);
            $_SESSION['message'] = $ok ? "✅ Statut mis à jour." : "❌ Échec de la mise à jour.";
        }
    
        header("Location: /administrateur/candidatures");
        exit;
    }
    
    public function editEntretien(): void
    {
        $this->redirectIfNotAdmin();
    
        // Récupère l'id en GET (affichage) ou POST (soumission)
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            echo "<div class='alert alert-danger'>❌ Entretien introuvable (id manquant).</div>";
            return;
        }
        $id = (int)$id;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'date_entretien' => $_POST['date_entretien'] ?? null,
                'heure'          => $_POST['heure'] ?? null,
                'type'           => $_POST['type'] ?? '',
                'lien_visio'     => $_POST['lien_visio'] ?? null,
                'commentaire'    => $_POST['commentaire'] ?? null
            ];
    
            if ($data['date_entretien'] && $data['heure'] && $data['type']) {
                $success = $this->entretienModel->update($id, $data);
                header("Location: /administrateur/calendrier?flash=entretien_updated");
exit;
            } else {
                echo "<div class='alert alert-warning'>⚠️ Champs obligatoires manquants.</div>";
            }
        }
    
        $entretien = $this->entretienModel->findById($id);
        if (!$entretien) {
            echo "<div class='alert alert-danger'>❌ Entretien introuvable.</div>";
            return;
        }
        $this->calendarView->renderFormModification($entretien);
    }
    
    public function deleteEntretien(): void
    {
        $this->redirectIfNotAdmin();
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /administrateur/vue-calendrier?flash=bad_request");
            exit;
        }
    
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            header("Location: /administrateur/vue-calendrier?flash=bad_request");
            exit;
        }
    
        $ok = $this->entretienModel->delete($id);
        header("Location: /administrateur/calendrier?flash=" . ($ok ? "entretien_deleted" : "entretien_delete_failed"));
exit;
    }

}
