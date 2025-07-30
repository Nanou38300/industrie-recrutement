<?php
// Déclare le namespace de ce contrôleur
namespace App\Controller;

// Importe les classes nécessaires pour le modèle et la vue
use App\Model\AnnonceModel;
use App\View\AnnonceView;

// Déclaration de la classe principale du contrôleur
class AnnoncesController {
    // Propriétés du contrôleur : une instance du modèle et de la vue
    public AnnonceModel $model;
    public AnnonceView $view;

    // Constructeur : initialise les propriétés avec les bonnes instances
    public function __construct() {
        $this->model = new AnnonceModel(); // Crée le modèle
        $this->view = new AnnonceView();   // Crée la vue
    }

    // Méthode pour afficher toutes les annonces
    public function afficherAnnonces() {
        $annonces = $this->model->getAll();            // Récupère toutes les annonces depuis le modèle
        $this->view->renderListe($annonces);           // Affiche les annonces via la vue
    }

    // Méthode pour afficher les détails d'une annonce spécifique
    public function afficherDetails($ref) {
        $annonce = $this->model->getByRef($ref);       // Récupère l’annonce selon sa référence
        if ($annonce) {
            $this->view->renderDetails($annonce);      // Si trouvée, affiche les détails
        } else {
            echo "Annonce introuvable.";               // Sinon, message d'erreur
        }
    }

    // Méthode pour postuler à une annonce avec envoi de CV
    public function postuler() {
        // Vérifie que la requête est POST et qu’un fichier CV est présent
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {
            $ref = $_POST['ref'];                                // Référence de l'annonce envoyée en POST
            $filename = $_FILES['cv']['name'];                   // Nom du fichier
            $tmp = $_FILES['cv']['tmp_name'];                    // Chemin temporaire du fichier
            $destination = 'cvs/' . time() . '_' . basename($filename); // Chemin final avec timestamp

            // Déplace le fichier vers le dossier définitif
            if (move_uploaded_file($tmp, $destination)) {
                $this->model->enregistrerCandidature($ref, $destination); // Enregistre la candidature
                echo "Votre candidature a été enregistrée ! 🎉";          // Confirmation
            } else {
                echo "Erreur lors de l'upload du CV.";                    // Message d'erreur
            }
        }
    }
}
