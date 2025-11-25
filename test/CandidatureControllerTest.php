<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Controller\CandidatureController;
use App\Model\CandidatureModel;
use App\View\CandidatureView;

final class CandidatureControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = [];
    }

    private function createController(
        ?CandidatureModel &$model = null,
        ?CandidatureView &$view = null,
        string $role = 'administrateur'
    ): CandidatureController {
        $_SESSION['utilisateur'] = [
            'id'   => 42,
            'role' => $role,
        ];

        $model = $this->createMock(CandidatureModel::class);
        $view  = $this->createMock(CandidatureView::class);

        return new CandidatureController($model, $view);
    }

    public function testSuiviAppelleRenderSuiviAvecLesCandidaturesDeLUtilisateur(): void
    {
        $candidatures = [
            ['id' => 1, 'statut' => 'en cours'],
            ['id' => 2, 'statut' => 'acceptee'],
        ];

        $controller = $this->createController($model, $view);

        $model->expects($this->once())
              ->method('findByUtilisateur')
              ->with($this->equalTo(42))
              ->willReturn($candidatures);

        $view->expects($this->once())
             ->method('renderSuivi')
             ->with($this->equalTo($candidatures));

        $controller->suivi();
    }
}
