<?php
namespace App\Model;

use App\Core\ValidacaoException;

/**
 * Carrinho de compras guardado na sessão: [cupcake_id => quantidade].
 * Recebe o array por referência para ser testável sem sessão real.
 */
class Carrinho
{
    public const QTD_MAXIMA_POR_ITEM = 50;

    private array $itens;

    public function __construct(array &$armazenamento)
    {
        $this->itens = &$armazenamento;
    }

    public function adicionar(int $cupcakeId, mixed $quantidade = 1): void
    {
        $qtd = $this->validarQuantidade($quantidade, 1);
        $nova = ($this->itens[$cupcakeId] ?? 0) + $qtd;
        if ($nova > self::QTD_MAXIMA_POR_ITEM) {
            throw new ValidacaoException(['quantidade' => 'Máximo de ' . self::QTD_MAXIMA_POR_ITEM . ' unidades por sabor.']);
        }
        $this->itens[$cupcakeId] = $nova;
    }

    /** Quantidade 0 remove o item. */
    public function atualizar(int $cupcakeId, mixed $quantidade): void
    {
        $qtd = $this->validarQuantidade($quantidade, 0);
        if ($qtd === 0) {
            $this->remover($cupcakeId);
            return;
        }
        $this->itens[$cupcakeId] = $qtd;
    }

    public function remover(int $cupcakeId): void
    {
        unset($this->itens[$cupcakeId]);
    }

    public function itens(): array
    {
        return $this->itens;
    }

    public function totalUnidades(): int
    {
        return array_sum($this->itens);
    }

    public function vazio(): bool
    {
        return empty($this->itens);
    }

    public function limpar(): void
    {
        $this->itens = [];
    }

    private function validarQuantidade(mixed $quantidade, int $minimo): int
    {
        $qtd = filter_var($quantidade, FILTER_VALIDATE_INT);
        if ($qtd === false || $qtd < $minimo || $qtd > self::QTD_MAXIMA_POR_ITEM) {
            throw new ValidacaoException([
                'quantidade' => "A quantidade deve ser um número inteiro entre $minimo e " . self::QTD_MAXIMA_POR_ITEM . '.',
            ]);
        }
        return $qtd;
    }
}
