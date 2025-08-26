<?php

namespace App\Controller;

use App\Model\EntretienModel;
use App\Model\UtilisateurModel;
use App\View\CalendrierView;

class EntretienController
{
    private EntretienModel $entretienModel;
    private UtilisateurModel $utilisateurModel;
    private CalendrierView $view;

    public function __construct()
    {
        $this->entretienModel = new EntretienModel();
        $this->utilisateurModel = new UtilisateurModel();
        $this->view = new CalendrierView();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 🔐 Vérifie si connecté
    private function redirectIfNotConnected(): void
    {
        if (!isset($_SESSION['utilisateur'])) {
            header("Location: /utilisateur/login");
            exit;
        }
    }

    // 📅 Vue mensuelle
    public function vueMensuelle(): void
    {
        $this->redirectIfNotConnected();
        $mois = date('m');
        $annee = date('Y');
        $entretiens = $this->entretienModel->getByMonth((int)$mois, (int)$annee);
        $this->view->renderCalendrier($entretiens, $mois, $annee);
    }

    // 📆 Vue jour
    public function vueJour(string $date): void
    {
        $this->redirectIfNotConnected();
        $entretiens = $this->entretienModel->getByDate($date);
        $this->view->renderJour($entretiens, $date);
    }

    // 🔔 Rappel du jour
    public function rappelDuJour(): void
    {
        $this->redirectIfNotConnected();
        $date = date('Y-m-d');
        $rappels = $this->entretienModel->getRemindersFor($date);

        foreach ($rappels as $entretien) {
            $this->view->renderRappel($entretien);
        }
    }

    // ➕ Planifier un entretien
    public function planifier(): void
    {
        $this->redirectIfNotConnected();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->entretienModel->create($_POST);

            if ($success) {
                echo "<div class='alert alert-success'>✅ Entretien planifié.</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Échec de la planification.</div>";
            }
        }

        $utilisateurs = $this->utilisateurModel->selectUtilisateurs();
        $this->view->renderForm($utilisateurs);
    }

    // 👁️ Détail d’un entretien
    public function detail(int $id): void
    {
        $this->redirectIfNotConnected();
        $entretien = $this->entretienModel->findById($id);

        if ($entretien) {
            $candidat = $this->utilisateurModel->getById($entretien['id_utilisateur']);
            $this->view->renderFicheEntretien($entretien, $candidat);
        } else {
            echo "<div class='alert alert-warning'>⚠️ Entretien introuvable.</div>";
        }
    }

    // 📧 Marquer rappel comme envoyé
    public function envoyerRappel(int $id): void
    {
        $this->redirectIfNotConnected();
        $this->entretienModel->envoyerRappel($id);
        echo "<div class='alert alert-info'>📩 Rappel marqué comme envoyé.</div>";
    }

    public function getByAdmin(int $idAdmin): array
{
    $stmt = $this->db->prepare("
        SELECT e.date_entretien, e.heure, u.nom, u.prenom, a.titre AS poste
        FROM entretien e
        JOIN utilisateur u ON u.id = e.id_utilisateur
        JOIN annonce a ON a.id = e.id_annonce
        WHERE a.id_administrateur = ?
        ORDER BY e.date_entretien ASC, e.heure ASC
    ");
    $stmt->execute([$idAdmin]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
