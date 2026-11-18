<?php
namespace Tests;

use App\Model\Carrinho;
use PHPUnit\Framework\TestCase;

class CarrinhoTest extends TestCase
{
    private array $sessao = [];
    private Carrinho $carrinho;

    protected function setUp(): void
    {
        $this->sessao = [];
        $this->carrinho = new Carrinho($this->sessao);
    }

    public function testAdicionarItemAoCarrinho(): void
    {
        $this->carrinho->adicionar(1, 2);
        $this->carrinho->adicionar(1, 3);
        $this->assertSame([1 => 5], $this->carrinho->itens());
    }

    public function testRemoverItemDoCarrinho(): void
    {
        $this->carrinho->adicionar(1, 2);
        $this->carrinho->remover(1);
        $this->assertTrue($this->carrinho->vazio());
    }
}
