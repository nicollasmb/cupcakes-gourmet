<?php
namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Model\Cupcake;

class VitrineController extends Controller
{
    public function index(): void
    {
        $cupcakes = (new Cupcake(Database::conexao()))->listarAtivos();
        $this->render('vitrine/index', ['cupcakes' => $cupcakes, 'titulo' => 'Vitrine']);
    }
}
