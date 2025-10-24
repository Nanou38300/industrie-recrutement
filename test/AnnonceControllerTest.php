<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Controller\AnnonceController;
use App\Model\AnnonceModel;
use App\View\AnnonceView;

final class AnnonceControllerTest extends TestCase
{
    public function testListAnnoncesAppelleRenderListeAvecDonneesDisponibles(): void
    {
        // Données simulées
        $annonces = [
            ['id' => 1, 'titre' => 'Développeur PHP'],
            ['id' => 2, 'titre' => 'Chef de projet'],
        ];

        // --- Mock du modèle ---
        // disableOriginalConstructor = pas de PDO
        $modelBuilder = $this->getMockBuilder(AnnonceModel::class)
                             ->disableOriginalConstructor();

        // Si ta classe a bien la méthode getAnnoncesDisponibles(), on la mocke.
        // Sinon, on ne la déclare pas et on se rabattra sur getAll().
        $hasGetDisponibles = method_exists(AnnonceModel::class, 'getAnnoncesDisponibles');

        if ($hasGetDisponibles) {
            $model = $modelBuilder->onlyMethods(['getAll','getAnnoncesDisponibles'])->getMock();
            $model->method('getAll')->willReturn($annonces);
            $model->method('getAnnoncesDisponibles')->willReturn($annonces);
        } else {
            $model = $modelBuilder->onlyMethods(['getAll'])->getMock();
            $model->method('getAll')->willReturn($annonces);
        }

        // --- Mock de la vue ---
        $view = $this->getMockBuilder(AnnonceView::class)
                     ->onlyMethods(['renderListe'])
                     ->getMock();

        $view->expects($this->once())
             ->method('renderListe')
             ->with($this->equalTo($annonces));

        // --- Contrôleur (injection pour éviter la BDD) ---
        $controller = new AnnonceController($model, $view);

        // --- Action ---
        // Si getAnnoncesDisponibles n'existe pas dans le modèle réel mais que
        // le contrôleur l'appelle, tu auras une erreur "undefined method".
        // Corrige alors soit le contrôleur pour appeler getAll(), soit ajoute
        // bien la méthode au modèle.
        $controller->listAnnonces();
    }
}
