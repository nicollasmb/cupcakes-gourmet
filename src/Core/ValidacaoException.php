<?php
namespace App\Core;

/**
 * Lançada quando os dados enviados pelo usuário não passam nas regras de negócio.
 * Carrega a lista de erros por campo para exibir na tela.
 */
class ValidacaoException extends \RuntimeException
{
    public function __construct(private array $erros)
    {
        parent::__construct(implode(' ', $erros));
    }

    public function erros(): array
    {
        return $this->erros;
    }
}
