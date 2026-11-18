<?php
namespace Tests;

use App\Model\Cupcake;
use PHPUnit\Framework\TestCase;

class CupcakeTest extends TestCase
{
    use BancoDeTeste;

    private Cupcake $cupcakes;

    protected function setUp(): void
    {
        $this->criarBanco();
        $this->cupcakes = new Cupcake($this->db);
    }

    public function testCadastrarCupcakeComSucesso(): void
    {
        $dados = [
            'nome' => 'Pistache',
            'descricao' => 'Creme de pistache',
            'preco' => '12,00',
            'estoque' => '15',
            'ativo' => '1'
        ];

        $id = $this->cupcakes->criar($dados);
        $cupcake = $this->cupcakes->buscar($id);

        $this->assertNotNull($cupcake);
        $this->assertSame('Pistache', $cupcake['nome']);
    }

    public function testListarApenasCupcakesAtivos(): void
    {
        $this->inserirCupcake('Ativo', 10, 5, 1);
        $this->inserirCupcake('Inativo', 10, 5, 0);

        $lista = $this->cupcakes->listarAtivos();
        $this->assertCount(1, $lista);
        $this->assertSame('Ativo', $lista[0]['nome']);
    }
}
