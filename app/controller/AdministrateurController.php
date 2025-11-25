<?php

namespace App\Controller;

use App\Model\UtilisateurModel;
use App\Model\AnnonceModel;
use App\Model\CandidatureModel;
use App\Model\EntretienModel;
use App\View\AdministrateurView;
use App\View\CalendrierView;
use App\Security;
use App\Config\AppConstants;

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
        // ✅ Sessions gérées dans index.php
        $this->userModel        = new UtilisateurModel();
        $this->annonceModel     = new AnnonceModel();
        $this->candidatureModel = new CandidatureModel();
        $this->entretienModel   = new EntretienModel();
        $this->view             = new AdministrateurView();
        $this->calendarView     = new CalendrierView();
    }

    // ✅ Utiliser Security::requireRole() au lieu de redirectIfNotAdmin()
    private function redirectIfNotAdmin(): void
    {
        Security::requireRole(AppConstants::ROLE_ADMIN);
    }

    // ✅ Plus besoin de checkCsrfToken(), on utilise Security::validateCSRFToken()

    // 👤 Profil administrateur + calendrier
    public function profil(int $idAdmin): void
    {
        $this->redirectIfNotAdmin();
        $idAdmin = $_SESSION['utilisateur']['id'];
    
        $infos = $this->userModel->getById($idAdmin);

        $rendezVous = $this->entretienModel->getByAdmin($idAdmin); // ← ici
      
        $this->view->renderProfil([
            'infos' => $infos,
            'rendezVous' => $rendezVous
        ]);
    }

    
    public function editProfil(): void
    {
        $this->redirectIfNotAdmin();
    
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $id = (int)($_SESSION['utilisateur']['id'] ?? 0);
    
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->checkCsrfToken();
            // (Optionnel) whitelisting des champs autorisés
            $payload = [
                'nom'       => $_POST['nom']       ?? null,
                'prenom'    => $_POST['prenom']    ?? null,
                'email'     => $_POST['email']     ?? null,
                'telephone' => $_POST['telephone'] ?? null,
                'poste'     => $_POST['poste']     ?? null,
                'ville'     => $_POST['ville']     ?? null,
            ];
    
            $success = $this->userModel->updateProfil($id, $payload);
    
            if ($success) {
                // (Optionnel) refléter les changements dans la session si tu l’utilises côté header/menu
                $_SESSION['utilisateur']['nom']       = $payload['nom']       ?? $_SESSION['utilisateur']['nom']       ?? '';
                $_SESSION['utilisateur']['prenom']    = $payload['prenom']    ?? $_SESSION['utilisateur']['prenom']    ?? '';
                $_SESSION['utilisateur']['email']     = $payload['email']     ?? $_SESSION['utilisateur']['email']     ?? '';
                $_SESSION['utilisateur']['telephone'] = $payload['telephone'] ?? $_SESSION['utilisateur']['telephone'] ?? '';
                $_SESSION['utilisateur']['poste']     = $payload['poste']     ?? $_SESSION['utilisateur']['poste']     ?? '';
                $_SESSION['utilisateur']['ville']     = $payload['ville']     ?? $_SESSION['utilisateur']['ville']     ?? '';
    
                // ✅ Succès → flash + PRG vers la page profil
                $_SESSION['flash'] = "✅ Informations mises à jour avec succès.";
                header('Location: /administrateur/profil');
                exit;
            }
    
            // ❌ Échec → flash + retour sur le formulaire
            $_SESSION['flash'] = "❌ Échec de la mise à jour du profil.";
            header('Location: /administrateur/edit-profil');
            exit;
        }
    
        // GET : afficher le formulaire de modification avec les données fraîches
        $profil = $this->userModel->getById($id);
        $this->view->renderFormProfil($profil);
    }

    public function deleteProfil(): void
    {
    $this->redirectIfNotAdmin();
     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->checkCsrfToken();
    } else {
        header("Location: /administrateur/profil");
        exit;
    }
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
        session_unset();
        session_destroy();
        header("Location: /utilisateur/login");
        exit;
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
        $this->checkCsrfToken();
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

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->checkCsrfToken();
            // Ceinture et bretelles : privilégie l'id POST si présent
            $targetId = isset($_POST['id']) ? (int)$_POST['id'] : $id;

            $ok = $this->annonceModel->update($targetId, $_POST);

            if ($ok) {
                // ✅ Succès → flash + PRG vers la liste
                $_SESSION['flash'] = "✅ Annonce mise à jour avec succès.";
                header("Location: /administrateur/annonces");
                exit;
            }

            // ❌ Échec → flash + retour sur le formulaire d’édition
            $_SESSION['flash'] = "❌ Échec de la mise à jour de l’annonce.";
            header("Location: /administrateur/edit-annonce?id=" . $targetId);
            exit;
        }

        // GET : récupérer l’annonce et afficher le formulaire en mode "update"
        $annonce = $this->annonceModel->getById($id);
        if (!$annonce) {
            // ⚠️ Not found → flash + retour liste
            $_SESSION['flash'] = "⚠️ Annonce introuvable.";
            header("Location: /administrateur/annonces");
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

    // ✅ Vérification CSRF ici
    $this->checkCsrfToken();

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
    
        require 'App/View/Calendar.php';
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

    require 'App/View/rdv-detail.php';
}
    
public function creerEntretien(): void
{
    $this->redirectIfNotAdmin();

    $dateISO = $_GET['date'] ?? null;
    $date = $dateISO ? substr($dateISO, 0, 10) : '';
    $heure = $dateISO ? substr($dateISO, 11, 5) : '';

    $annonces = $this->annonceModel->getByAdmin($_SESSION['utilisateur']['id']);
    $candidats = $this->userModel->getAllCandidats();
  

    $this->calendarView->renderFormCreation($date, $heure, $annonces, $candidats);
}

public function validerEntretien(): void
{
    $this->redirectIfNotAdmin();
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    $this->checkCsrfToken();
    $data = [
        'id_utilisateur' => $_POST['id_utilisateur'] ?? null,
        'date_entretien' => $_POST['date_entretien'] ?? null,
        'heure'          => $_POST['heure'] ?? null,
        'type'           => $_POST['type'] ?? '',
        'lien_visio'     => $_POST['lien_visio'] ?? null,
        'commentaire'    => $_POST['commentaire'] ?? null
    ];

    // Vérifs minimales
    if ($data['id_utilisateur'] && $data['date_entretien'] && $data['heure'] && $data['type']) {
        $ok = $this->entretienModel->create($data);
        if ($ok) {
            $_SESSION['flash'] = "✅ Entretien planifié avec succès.";
            $_SESSION['flash_type'] = 'success';
            header("Location: /administrateur/calendrier");
            exit;
        }
        $_SESSION['flash'] = "❌ Échec lors de la planification de l’entretien.";
        $_SESSION['flash_type'] = 'error';
        header("Location: /administrateur/calendrier");
        exit;
    }

    $_SESSION['flash'] = "❌ Données manquantes pour planifier l’entretien.";
    $_SESSION['flash_type'] = 'error';
    header("Location: /administrateur/calendrier");
    exit;
}
public function editEntretien(): void
{
        $this->redirectIfNotAdmin();
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
    
        // Récupère l'id en GET (affichage) ou POST (soumission)
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['flash'] = "❌ Entretien introuvable (id manquant).";
            $_SESSION['flash_type'] = 'error';
            header("Location: /administrateur/calendrier");
            exit;
        }
        $id = (int)$id;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->checkCsrfToken();
            $data = [
                'date_entretien' => $_POST['date_entretien'] ?? null,
                'heure'          => $_POST['heure'] ?? null,
                'type'           => $_POST['type'] ?? '',
                'lien_visio'     => $_POST['lien_visio'] ?? null,
                'commentaire'    => $_POST['commentaire'] ?? null,
            ];
    
            if ($data['date_entretien'] && $data['heure'] && $data['type']) {
                $success = $this->entretienModel->update($id, $data);
                $_SESSION['flash'] = $success
                    ? "✅ Rendez-vous mis à jour avec succès."
                    : "❌ Échec de la mise à jour du rendez-vous.";
                $_SESSION['flash_type'] = $success ? 'success' : 'error';
                header("Location: /administrateur/calendrier");
                exit;
            }
    
            $_SESSION['flash'] = "⚠️ Champs obligatoires manquants.";
            $_SESSION['flash_type'] = 'warning';
            header("Location: /administrateur/view-rdv?id=" . $id);
            exit;
        }
    
        $entretien = $this->entretienModel->findById($id);
        if (!$entretien) {
            $_SESSION['flash'] = "❌ Entretien introuvable.";
            $_SESSION['flash_type'] = 'error';
            header("Location: /administrateur/calendrier");
            exit;
        }
    
        $this->calendarView->renderFormModification($entretien);
    }


    public function updateEntretien(int $id = 0): void
{
    $this->redirectIfNotAdmin();
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /administrateur/calendrier");
        exit;
    }
    $this->checkCsrfToken();
    // Id prioritaire depuis POST
    $targetId = isset($_POST['id']) ? (int)$_POST['id'] : (int)$id;

    $payload = [
        'date_entretien' => $_POST['date_entretien'] ?? null,
        'heure'          => $_POST['heure'] ?? null,
        'type'           => $_POST['type'] ?? null,
        'lien_visio'     => $_POST['lien_visio'] ?? null,
        'commentaire'    => $_POST['commentaire'] ?? null,
    ];

    // (optionnel) filtrer nulls si ton model ne gère pas bien
    $payload = array_filter($payload, static fn($v) => $v !== null);

    $ok = $this->entretienModel->update($targetId, $payload);
    if ($ok) {
        $_SESSION['flash'] = "✅ Rendez-vous mis à jour avec succès.";
        $_SESSION['flash_type'] = 'success';
        header("Location: /administrateur/calendrier");
        exit;
    }

    $_SESSION['flash'] = "❌ Échec de la mise à jour du rendez-vous.";
    $_SESSION['flash_type'] = 'error';
    header("Location: /administrateur/view-rdv?id=" . $targetId);
    exit;
}
public function deleteEntretien(): void
{
    $this->redirectIfNotAdmin();
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /administrateur/calendrier");
        exit;
    }
    $this->checkCsrfToken();
    
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        $_SESSION['flash'] = "❌ Identifiant de rendez-vous invalide.";
        $_SESSION['flash_type'] = 'error';
        header("Location: /administrateur/calendrier");
        exit;
    }

    $ok = $this->entretienModel->delete($id);
    if ($ok) {
        $_SESSION['flash'] = "🗑️ Rendez-vous supprimé avec succès.";
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash'] = "❌ Échec de la suppression du rendez-vous.";
        $_SESSION['flash_type'] = 'error';
    }

    header("Location: /administrateur/calendrier");
    exit;
}


    

}
