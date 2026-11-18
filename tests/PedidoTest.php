<?php
namespace Tests;

use App\Model\Pedido;
use PHPUnit\Framework\TestCase;

class PedidoTest extends TestCase
{
    use BancoDeTeste;

    private Pedido $pedidos;

    protected function setUp(): void
    {
        $this->criarBanco();
        $this->pedidos = new Pedido($this->db, 8.00);
    }

    public function testCriarPedidoComSucesso(): void
    {
        $cupcakeId = $this->inserirCupcake('Red Velvet', 10.00, 5);

        $dadosCliente = [
            'nome' => 'Maria Silva',
            'email' => 'maria@email.com',
            'telefone' => '11987654321',
            'tipo_entrega' => 'entrega',
            'endereco' => 'Rua das Flores, 123',
            'forma_pagamento' => 'pix'
        ];

        $pedidoId = $this->pedidos->criar($dadosCliente, [$cupcakeId => 2]);
        $pedido = $this->pedidos->buscar($pedidoId);

        $this->assertNotNull($pedido);
        $this->assertSame(28.0, (float) $pedido['total']); // 2x10 + 8 de entrega
    }

    public function testAtualizarStatusDoPedido(): void
    {
        $cupcakeId = $this->inserirCupcake('Red Velvet', 10.00, 5);

        $dadosCliente = [
            'nome' => 'Maria Silva',
            'email' => 'maria@email.com',
            'telefone' => '11987654321',
            'tipo_entrega' => 'retirada',
            'endereco' => '',
            'forma_pagamento' => 'pix'
        ];

        $pedidoId = $this->pedidos->criar($dadosCliente, [$cupcakeId => 1]);
        $this->pedidos->atualizarStatus($pedidoId, 'pronto_para_retirada');

        $pedido = $this->pedidos->buscar($pedidoId);
        $this->assertSame('pronto_para_retirada', $pedido['status']);
    }
}
