<?php

namespace App\Controller;

use App\Model\EntretienModel;
use App\View\CalendrierView;

class CalendrierController
{
    private EntretienModel $model;
    private CalendrierView $view;

    public function __construct()
    {
        $this->model = new EntretienModel();
        $this->view = new CalendrierView();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Vérifie si connecté
    private function redirectIfNotConnected(): void
    {
        if (!isset($_SESSION['utilisateur']['id'])) {
            header("Location: /utilisateur/login");
            exit;
        }
    }

    // Vue semaine
    public function vueSemaine(): void
    {
        $this->redirectIfNotConnected();
        $semaine = date('W');
        $annee = date('Y');
        $rendezVous = $this->model->getByWeek($semaine, $annee);
        $this->view->renderSemaine($rendezVous, $semaine, $annee);
    }

    // Vue jour
    public function vueJour(string $date): void
    {
        $this->redirectIfNotConnected();
        $rendezVous = $this->model->getByDate($date);
        $this->view->renderJour($rendezVous, $date);
    }

    // Rappel du jour
    public function rappelDuJour(): void
    {
        $this->redirectIfNotConnected();
        $aujourdHui = date('Y-m-d');
        $rappels = $this->model->getRemindersFor($aujourdHui);
        $this->view->renderRappels($rappels);
    }

    // Détail d’un rendez-vous
    public function infoRendezVous(int $id): void
    {
        $this->redirectIfNotConnected();
        $rdv = $this->model->findById($id);
        if ($rdv) {
            $this->view->renderDetails($rdv);
        } else {
            echo "<div class='alert alert-warning'>⚠️ Rendez-vous introuvable.</div>";
        }
    }

    // Planifier un entretien
    public function planifierEntretien(): void
    {
        $this->redirectIfNotConnected();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'id_utilisateur' => $_POST['id_utilisateur'],
                'date_entretien' => $_POST['date_entretien'],
                'heure'          => $_POST['heure'],
                'type'           => $_POST['type'],
                'lien_visio'     => $_POST['lien_visio'] ?? null
            ]);
            echo "<div class='alert alert-success'>✅ Entretien planifié.</div>";
        } else {
            $this->view->renderForm();
        }
    }

    // Liste des entretiens
    public function listEntretiens(): void
    {
        $this->redirectIfNotConnected();
        $entretiens = $this->model->findAll();
        $this->view->renderListe($entretiens);
    }

    // Envoyer un rappel
    public function envoyerRappel(int $id): void
    {
        $this->redirectIfNotConnected();
        $this->model->envoyerRappel($id);
        echo "<div class='alert alert-info'>📩 Rappel envoyé.</div>";
    }
}
