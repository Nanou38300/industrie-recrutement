<?php
namespace App\Controller;
use App\Model\NewsModel; // On importe le modèle
use App\View\NewsView;   // On importe la vue
use App\Database; // On importe la Bdd



class NewsController
{
    // Obligé de déclarer les attributs quand on les utilise avec $this
    private NewsModel $newsModel; // Attribut pour le model
    private NewsView $newsView;   // Attribut pour la vue

    public function __construct()
    {
        $this->newsModel = new newsModel(); // On instancie le modèle
        $this->newsView = new newsView();  // On instancie la vue
    }

    public function selectNews() // Méthode pour lister les news
    {
        $news = $this->newsModel->listNews(); // On récupère les utilisateurs dans la Bdd
        $this->newsView->displayNews($news); // On les affiche
    }

    public function createNews(): void // Méthode pour créer l'utilisateur
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Si une variable post est soumise, on exécute le modèle
            // Si formulaire soumis : enregistrer
            $this->newsModel->insertNews(
                $_POST['title'],
                $_POST['article'],
                $_POST['author']
            );
            $news = $this->newsModel->listNews(); // On affiche les utilisateurs après l'insertion (modèle + vue)
            $this->newsView->displayNews($news);
        } else if($_SESSION) { // Si l'utilisateur est connecté
            // Sinon : afficher le formulaire
            $this->newsView->displayInsertForm(); // Si une variable n'est pas transmise on affiche le formulaire
        } else {
            echo '<h1>Vous devez être connecté pour créer une news.</h1>';
        }
    }

    public function editNews(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de mise à jour
            $this->newsModel->updateNews(
                (int)$_POST['id'],
                $_POST['title'],
                $_POST['article'],
                $_POST['author']
            );
    
            // Après modification : récupérer toutes les news et afficher
            $news = $this->newsModel->listNews(); // c'est listNews(), pas selectNews()
            $this->newsView->displayNews($news);  // c'est displayNews(), pas listNews()
    
        } else if($_SESSION){
            // Sinon affichage du formulaire avec les données existantes
            if (isset($id)) {
                $news = $this->newsModel->selectArticle((int)$id);
                if ($news) {
                    $this->newsView->displayUpdateForm($news); // Ici on affiche bien ton formulaire
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
        
    public function deleteNews($id) // Méthode pour surpprimer un user
    {
        if($_SESSION) {
            $this->newsModel->deleteNews($id);
        }else {
            echo '<h1>Vous devez être connecté pour supprimer une news.</h1>';
        }
        $news = $this->newsModel->listNews();
        $this->newsView->displayNews($news);
}
}
?>
