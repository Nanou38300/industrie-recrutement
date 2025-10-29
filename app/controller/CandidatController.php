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

// CandidatController.php

public function profil(): void
{
    $this->redirectIfNotConnected();
    $profil = $this->model->getProfil((int)$_SESSION['utilisateur']['id']);
    $this->view->renderProfil($profil);
}

public function editProfil(): void
{
    $this->redirectIfNotConnected();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Enregistrement global (tous les champs)
        $ok = $this->model->updateProfil((int)$_SESSION['utilisateur']['id'], [
            'nom'       => $_POST['nom']       ?? '',
            'prenom'    => $_POST['prenom']    ?? '',
            'email'     => $_POST['email']     ?? '',
            'telephone' => $_POST['telephone'] ?? '',
            'poste'     => $_POST['poste']     ?? '',
            'ville'     => $_POST['ville']     ?? '',
            'linkedin'  => $_POST['linkedin']  ?? '',
        ]);

        $_SESSION['flash'] = $ok ? 'Profil mis à jour.' : 'Échec de la mise à jour.';
        header('Location: /candidat/profil');
        exit;
    }

    // GET → afficher le formulaire prérempli
    $profil = $this->model->getProfil((int)$_SESSION['utilisateur']['id']);
    $this->view->renderFormProfilCandidat($profil);
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
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /candidat/profil'); exit;
        }
    
        $userId = (int)$_SESSION['utilisateur']['id'];
        $ok = $this->model->deleteUtilisateur($userId);
    
        if ($ok) {
            session_destroy();
            header('Location: /');
        } else {
            $_SESSION['flash'] = "La suppression a échoué.";
            header('Location: /candidat/profil');
        }
        exit;
    }
    public function uploadCV(): void
    {
        $this->redirectIfNotConnected();
    
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['cv']['name'])) {
            header('Location: /candidat/profil'); exit;
        }
    
        $allowed = ['pdf','doc','docx'];
        $orig = $_FILES['cv']['name'];
        $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $_SESSION['flash'] = "Format non supporté (pdf, doc, docx).";
            header('Location: /candidat/profil'); exit;
        }
    
        $root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
        $dir  = $root . '/uploads';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
    
        $userId = (int)$_SESSION['utilisateur']['id'];
    
        // supprime l’ancien CV s’il existe
        $old = $this->model->getCurrentCV($userId); // ex: '1730...-cv.pdf'
        if ($old) {
            $oldAbs = $dir . '/' . basename($old);
            if (is_file($oldAbs)) @unlink($oldAbs);
        }
    
        $base = preg_replace('~[^a-zA-Z0-9._-]~', '_', pathinfo($orig, PATHINFO_FILENAME));
        $name = time() . '-' . trim($base, '_-') . '.' . $ext;
        $dest = $dir . '/' . $name;
    
        $ok = is_uploaded_file($_FILES['cv']['tmp_name']) && move_uploaded_file($_FILES['cv']['tmp_name'], $dest);
        if (!$ok) {
            $_SESSION['flash'] = "Échec de l’upload du CV.";
            header('Location: /candidat/profil'); exit;
        }
        @chmod($dest, 0644);
    
        // On stocke uniquement le NOM de fichier en BDD
        $this->model->updateCV($userId, $name);
    
        $_SESSION['flash'] = "CV mis à jour.";
        header('Location: /candidat/profil'); exit;
    }

public function uploadPhoto(): void
{
    $this->redirectIfNotConnected();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['photo']['name'])) {
        header('Location: /candidat/profil'); exit;
    }

    $allowed = ['jpg','jpeg','png','gif','webp'];
    $orig = $_FILES['photo']['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        $_SESSION['flash'] = "Format image non supporté (jpg, png, gif, webp).";
        header('Location: /candidat/profil'); exit;
    }

    $root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    $dir  = $root . '/uploads';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);

    // supprime l’ancienne photo si elle existe
    $userId = (int)$_SESSION['utilisateur']['id'];
    $oldRel = $this->model->getCurrentPhoto($userId); // ex: 'uploads/xxx.jpg'
    if ($oldRel) {
        $oldAbs = $root . '/' . ltrim($oldRel, '/');
        if (is_file($oldAbs)) @unlink($oldAbs);
    }

    $base = preg_replace('~[^a-zA-Z0-9._-]~', '_', pathinfo($orig, PATHINFO_FILENAME));
    $name = time() . '-' . trim($base, '_-') . '.' . $ext;
    $dest = $dir . '/' . $name;

    $ok = is_uploaded_file($_FILES['photo']['tmp_name']) && move_uploaded_file($_FILES['photo']['tmp_name'], $dest);
    if (!$ok) {
        $_SESSION['flash'] = "Échec de l’upload de la photo.";
        header('Location: /candidat/profil'); exit;
    }
    @chmod($dest, 0644);

    $this->model->updatePhoto($userId, 'uploads/' . $name);

    $_SESSION['flash'] = "Photo mise à jour.";
    header('Location: /candidat/profil'); exit;
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
