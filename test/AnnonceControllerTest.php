<?php
declare(strict_types=1); // Active le typage strict en PHP

use PHPUnit\Framework\TestCase;      // Classe de base pour les tests PHPUnit
use App\Controller\AnnonceController; // Le contrôleur qu’on veut tester
use App\Model\AnnonceModel;         
use App\View\AnnonceView;         

// Classe de test pour AnnonceController
final class AnnonceControllerTest extends TestCase
{
    // Nom du test : on vérifie que viewAnnonce()
    // appelle bien renderDetails() quand l’annonce existe.
    public function testViewAnnonceExistanteAppelleRenderDetails(): void
    {
        // On prépare une fausse annonce que le modèle renverra
        $annonce = ['id' => 1, 'titre' => 'Dev PHP'];

        // On crée un faux modèle (mock) et une fausse vue
        $model = $this->createMock(AnnonceModel::class);
        $view  = $this->createMock(AnnonceView::class);

        // On dit : quand le contrôleur demandera getById(1),
        // le modèle mock doit être appelé une fois et renvoyer $annonce
        $model->expects($this->once())
              ->method('getById')
              ->with($this->equalTo(1))
              ->willReturn($annonce);

        // On dit : la vue mock doit appeler une fois renderDetails()
        // avec exactement $annonce en paramètre
        $view->expects($this->once())
             ->method('renderDetails')
             ->with($this->equalTo($annonce));

        // On crée le contrôleur avec nos mocks (donc pas de vraie BDD)
        $controller = new AnnonceController($model, $view);

        // On exécute la méthode à tester
        // → le test vérifie que les attentes ci‑dessus sont respectées
        $controller->viewAnnonce(1);
    }
}
