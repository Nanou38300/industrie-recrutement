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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->model = new CandidatModel();
        $this->view  = new CandidatView();
    }

    // 🔐 Vérifie le token CSRF pour les requêtes POST
    private function checkCsrfToken(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo "Requête invalide (CSRF).";
            exit;
        }
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
        $id = (int)$_SESSION['utilisateur']['id'];

        $donnees = [
            'profil'       => $this->model->getProfil($id),
            'annonces'     => $this->model->getAnnoncesDisponibles(),
            'candidatures' => $this->model->getCandidatures($id),
        ];

        $this->view->renderDashboard($donnees);
    }

    // ─────────────────────────────────────
    // 👤 Profil
    // ─────────────────────────────────────

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
            // 🔐 CSRF
            $this->checkCsrfToken();

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
            // 🔐 CSRF
            $this->checkCsrfToken();

            $this->model->updateProfil((int)$_SESSION['utilisateur']['id'], $_POST);
            header("Location: /candidat/profil");
            exit;
        }
    }

    // ─────────────────────────────────────
    // 🗑️ Suppression du compte candidat
    // ─────────────────────────────────────
    public function delete(): void
    {
        $this->redirectIfNotConnected();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /candidat/profil');
            exit;
        }

        // 🔐 CSRF
        $this->checkCsrfToken();

        $userId = (int)$_SESSION['utilisateur']['id'];
        $ok     = $this->model->deleteUtilisateur($userId);

        if ($ok) {
            session_destroy();
            header('Location: /');
        } else {
            $_SESSION['flash'] = "La suppression a échoué.";
            header('Location: /candidat/profil');
        }
        exit;
    }

    // ─────────────────────────────────────
    // 📎 Upload CV
    // ─────────────────────────────────────
    public function uploadCV(): void
    {
        $this->redirectIfNotConnected();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['cv']['name'])) {
            header('Location: /candidat/profil');
            exit;
        }

        // 🔐 CSRF
        $this->checkCsrfToken();

        $allowedExt  = ['pdf', 'doc', 'docx'];
        $allowedMime = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $file = $_FILES['cv'];
        $orig = $file['name'];
        $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt, true)) {
            $_SESSION['flash'] = "Format non supporté (pdf, doc, docx).";
            header('Location: /candidat/profil');
            exit;
        }
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            $_SESSION['flash'] = "Échec de l’upload du CV.";
            header('Location: /candidat/profil');
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $file['tmp_name']) ?: '';
        finfo_close($finfo);
        if (!in_array($mime, $allowedMime, true)) {
            $_SESSION['flash'] = "Le fichier n'est pas un document valide.";
            header('Location: /candidat/profil');
            exit;
        }

        $root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
        $dir  = $root . '/uploads';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $userId = (int)$_SESSION['utilisateur']['id'];

        // supprime l’ancien CV s’il existe
        $old = $this->model->getCurrentCV($userId); // ex: '1730...-cv.pdf'
        if ($old) {
            $oldAbs = $dir . '/' . basename($old);
            if (is_file($oldAbs)) {
                @unlink($oldAbs);
            }
        }

        $base = preg_replace('~[^a-zA-Z0-9._-]~', '_', pathinfo($orig, PATHINFO_FILENAME));
        $name = time() . '-' . trim($base, '_-') . '.' . $ext;
        $dest = $dir . '/' . $name;

        $ok = move_uploaded_file($file['tmp_name'], $dest);

        if (!$ok) {
            $_SESSION['flash'] = "Échec de l’upload du CV.";
            header('Location: /candidat/profil');
            exit;
        }
        @chmod($dest, 0644);

        // On stocke uniquement le NOM de fichier en BDD
        $this->model->updateCV($userId, $name);

        $_SESSION['flash'] = "CV mis à jour.";
        header('Location: /candidat/profil');
        exit;
    }

    // ─────────────────────────────────────
    // 🖼️ Upload photo de profil
    // ─────────────────────────────────────
public function uploadPhoto(): void
{
    $this->redirectIfNotConnected();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['photo']['name'])) {
        header('Location: /candidat/profil');
        exit;
    }

    // 🔐 CSRF
    $this->checkCsrfToken();

    // === Config ===
    $maxBytes    = 10 * 1024 * 1024; // 10 Mo
    $allowedExt  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxWidth    = 4000; // largeur max en px
    $maxHeight   = 4000; // hauteur max en px

    $file = $_FILES['photo'];
    $tmp  = $file['tmp_name'];
    $orig = $file['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $size = (int) $file['size'];

    // Vérif extension
    if (!in_array($ext, $allowedExt, true)) {
        $_SESSION['flash'] = "Format image non supporté (jpg, png, gif, webp).";
        header('Location: /candidat/profil');
        exit;
    }

    // Vérif erreurs upload
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
        $_SESSION['flash'] = "Échec de l’upload de la photo.";
        header('Location: /candidat/profil');
        exit;
    }

    // Vérif taille (10 Mo max)
    if ($size > $maxBytes) {
        $_SESSION['flash'] = "Fichier trop lourd (max 10 Mo).";
        header('Location: /candidat/profil');
        exit;
    }

    // Vérif MIME réel
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmp) ?: '';
    finfo_close($finfo);
    if (!in_array($mime, $allowedMime, true)) {
        $_SESSION['flash'] = "Le fichier n'est pas une image valide.";
        header('Location: /candidat/profil');
        exit;
    }

    // Vérif dimensions
    $info = getimagesize($tmp);
    if ($info === false) {
        $_SESSION['flash'] = "Le fichier n'est pas une image valide.";
        header('Location: /candidat/profil');
        exit;
    }
    if ($info[0] > $maxWidth || $info[1] > $maxHeight) {
        $_SESSION['flash'] = "Image trop grande (max {$maxWidth}x{$maxHeight}px).";
        header('Location: /candidat/profil');
        exit;
    }

    // Dossiers et suppression ancienne photo
    $root = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    $dir  = $root . '/uploads';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $userId = (int) $_SESSION['utilisateur']['id'];
    $oldRel = $this->model->getCurrentPhoto($userId);
    if ($oldRel) {
        $oldAbs = $root . '/' . ltrim($oldRel, '/');
        if (is_file($oldAbs)) {
            @unlink($oldAbs);
        }
    }

    // Nom de fichier unique
    $base = preg_replace('~[^a-zA-Z0-9._-]~', '_', pathinfo($orig, PATHINFO_FILENAME));
    $name = time() . '-' . trim($base, '_-') . '.' . $ext;
    $dest = $dir . '/' . $name;

    // Déplacement du fichier tel quel
    $ok = move_uploaded_file($tmp, $dest);
    if (!$ok) {
        $_SESSION['flash'] = "Erreur lors du téléchargement de la photo.";
        header('Location: /candidat/profil');
        exit;
    }
    @chmod($dest, 0644);

    // Sauvegarde en base (chemin relatif)
    $this->model->updatePhoto($userId, 'uploads/' . $name);

    $_SESSION['flash'] = "Photo mise à jour.";
    header('Location: /candidat/profil');
    exit;
}


    // ─────────────────────────────────────
    // 📩 Candidature à une annonce
    // ─────────────────────────────────────
    public function postuler(int $id): void
    {
        $this->redirectIfNotConnected();

        // On impose un POST + CSRF pour une action qui crée une candidature
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /candidat/annonces");
            exit;
        }

        // 🔐 CSRF
        $this->checkCsrfToken();

        $idUtilisateur = (int)$_SESSION['utilisateur']['id'];

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

    // ─────────────────────────────────────
    // 🔎 Suivi des candidatures
    // ─────────────────────────────────────
    public function renderSuiviCandidatures(): void
    {
        $this->redirectIfNotConnected();
        $id          = (int)$_SESSION['utilisateur']['id'];
        $candidatures = $this->model->getCandidatures($id);
        $this->view->renderSuiviCandidatures($candidatures);
    }

    // ─────────────────────────────────────
    // 📋 Liste des annonces visibles par le candidat
    // ─────────────────────────────────────
    public function listAnnonces(): void
    {
        $this->redirectIfNotConnected();
        $annonces = $this->model->getAnnoncesDisponibles();
        $this->view->renderAnnonces($annonces);
    }
}
