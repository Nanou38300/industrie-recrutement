<?php
namespace App\Controller;
use App\Model\AnnonceModel; // On importe le modèle
use App\View\AnnonceView;   // On importe la vue
use App\Database; // On importe la Bdd
class AnnonceController
{
    // Obligé de déclarer les attributs quand on les utilise avec $this
    private AnnonceModel $nAnnonceModel; // Attribut pour le model
    private AnnonceView $AnnonceView;   // Attribut pour la vue

    public function __construct()
    {
        $this->annonceModel = new annonceModel(); // On instancie le modèle
        $this->annonceView = new annonceView();  // On instancie la vue
    }

    public function selectAnnonces() // Méthode pour lister les annonces
    {
        $news = $this->annonceModel->listAnnonces(); // On récupère les annonces dans la Bdd
        $this->annonceView->displayAnnonce($annonce); // On les affiche
    }


    public function createAnnonce(): void // Méthode pour créer une annonce
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Si une variable post est soumise, on exécute le modèle
            // Si formulaire soumis : enregistrer
            $this->annonceModel->insertAnnonce(
                $_POST['title'],
                $_POST['article'],
                $_POST['author']
            );
            $Annonce = $this->annonceModel->listAnnonces(); // On affiche les utilisateurs après l'insertion (modèle + vue)
            $this->annonceView->displayAnnonce($annonce);
        } else if($_SESSION) { // Si l'utilisateur est connecté
            // Sinon : afficher le formulaire
            $this->annonceView->displayInsertForm(); // Si une variable n'est pas transmise on affiche le formulaire
        } else {
            echo '<h1>Vous devez être connecté pour créer une annonce.</h1>';
        }
    }

    public function editAnnonce(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de mise à jour
            $this->annonceModel->updateAnnonce(
                (int)$_POST['id'],
                $_POST['title'],
                $_POST['article'],
                $_POST['author']
            );
    
            // Après modification : récupérer toutes les news et afficher
            $annonce = $this->annonceModel->listAnnonces(); // c'est listNews(), pas selectNews()
            $this->annonceView->displayAnnonce($annonce);  // c'est displayNews(), pas listNews()
    
        } else if($_SESSION){
            // Sinon affichage du formulaire avec les données existantes
            if (isset($id)) {
                $annonce = $this->annonceModel->selectArticle((int)$id);
                if ($annonce) {
                    $this->annonceView->displayUpdateForm($annonce); // Ici on affiche bien ton formulaire
                } else {
                    echo "Article introuvable.";
                }
            } else {
                echo "ID manquant pour modifier.";
            }
        } else {
            echo '<h1>Vous devez être connecté pour editer une news.</h1>';
        }
    }
        
    public function deleteAnnonce($id) // Méthode pour surpprimer un user
    {
        if($_SESSION) {
            $this->annonceModel->deleteAnnonce($id);
        }else {
            echo '<h1>Vous devez être connecté pour supprimer une news.</h1>';
        }
        $annonce = $this->annonceModel->listAnnonces();
        $this->annonceView->displayAnnonce($annonce);
}
}