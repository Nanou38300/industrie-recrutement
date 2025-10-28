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
        // $this->view->renderEditForm($profil);
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
    
        if (!isset($_FILES['cv']) || ($_FILES['cv']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            echo "<p>❌ Aucun fichier reçu.</p>";
            return;
        }
    
        // 1) Validation basique du type/extension
        $allowedExt = ['pdf','doc','docx'];
        $original   = $_FILES['cv']['name'] ?? 'cv';
        $ext        = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            echo "<p>❌ Format non autorisé. Extensions acceptées : .pdf, .doc, .docx</p>";
            return;
        }
    
        // 2) Sanitize + nom unique (on ne stocke QUE le nom dans la BDD)
        $base      = pathinfo($original, PATHINFO_FILENAME);
        $slugBase  = preg_replace('~[^a-z0-9_-]+~i', '-', $base);
        $filename  = time() . '-' . trim($slugBase, '-') . '.' . $ext;
    
        // 3) Dossier de destination public
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads';
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            echo "<p>❌ Impossible de créer le dossier d’upload.</p>";
            return;
        }
    
        $tmp         = $_FILES['cv']['tmp_name'];
        $destination = $dir . '/' . $filename;
    
        // 4) Déplacement du fichier
        if (!is_uploaded_file($tmp) || !move_uploaded_file($tmp, $destination)) {
            echo "<p>❌ Échec du déplacement du fichier.</p>";
            return;
        }
    
        // 5) Permissions raisonnables (lecture web)
        @chmod($destination, 0644);
    
        // 6) On enregistre UNIQUEMENT le nom du fichier en BDD (pas le chemin)
        $ok = $this->model->updateCV($_SESSION['utilisateur']['id'], $filename);
        if (!$ok) {
            echo "<p>❌ Échec de l’enregistrement du CV en base.</p>";
            return;
        }
    
        // 7) PRG : retour page profil
        header("Location: /candidat/profil");
        exit;
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


    public function postuler(int $id): void
{
    $idUtilisateur = $_SESSION['utilisateur']['id'];

    $resultat = $this->model->postuler($idUtilisateur, $id);

    $_SESSION['popup'] = [
        'message' => $resultat
            ? "✅ Votre candidature a bien été envoyée."
            : "⚠️ Vous avez déjà postulé à cette annonce.",
        'retour'  => '/candidat/annonces'
    ];

    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/candidat/annonces'));

    exit;
}

    
    

    public function renderSuiviCandidatures(): void
    {
        $this->redirectIfNotConnected();
        $id = $_SESSION['utilisateur']['id'];
        $candidatures = $this->model->getCandidatures($id);
        $this->view->renderSuiviCandidatures($candidatures);
    }

    public function listAnnonces(): void
{
    $this->redirectIfNotConnected();
    $annonces = $this->model->getAnnoncesDisponibles();
    $this->view->renderAnnonces($annonces);
}

}
