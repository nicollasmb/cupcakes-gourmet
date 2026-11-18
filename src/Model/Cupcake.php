<?php
namespace App\Model;

use App\Core\ValidacaoException;
use PDO;

/**
 * Produto da vitrine. Regras: nome obrigatório e único, preço > 0, estoque >= 0.
 */
class Cupcake
{
    public const PRECO_MAXIMO = 500.00;
    public const ESTOQUE_MAXIMO = 10000;

    public function __construct(private PDO $db)
    {
    }

    /** Valida e normaliza os dados do formulário. Lança ValidacaoException. */
    public function validar(array $dados, ?int $idAtual = null): array
    {
        $erros = [];

        $nome = trim((string) ($dados['nome'] ?? ''));
        if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
            $erros['nome'] = 'O nome deve ter entre 3 e 100 caracteres.';
        } elseif ($this->nomeEmUso($nome, $idAtual)) {
            $erros['nome'] = 'Já existe um cupcake com esse nome.';
        }

        $descricao = trim((string) ($dados['descricao'] ?? ''));
        if (mb_strlen($descricao) > 500) {
            $erros['descricao'] = 'A descrição pode ter no máximo 500 caracteres.';
        }

        $precoBruto = str_replace(',', '.', (string) ($dados['preco'] ?? ''));
        $preco = is_numeric($precoBruto) ? (float) $precoBruto : null;
        if ($preco === null) {
            $erros['preco'] = 'Informe um preço válido (ex.: 9,50).';
        } elseif ($preco <= 0) {
            $erros['preco'] = 'O preço deve ser maior que zero.';
        } elseif ($preco > self::PRECO_MAXIMO) {
            $erros['preco'] = 'O preço máximo permitido é R$ 500,00.';
        } elseif (round($preco, 2) != $preco) {
            $erros['preco'] = 'O preço pode ter no máximo 2 casas decimais.';
        }

        $estoque = filter_var($dados['estoque'] ?? '', FILTER_VALIDATE_INT);
        if ($estoque === false || $estoque < 0 || $estoque > self::ESTOQUE_MAXIMO) {
            $erros['estoque'] = 'O estoque deve ser um número inteiro entre 0 e 10000.';
        }

        if ($erros) {
            throw new ValidacaoException($erros);
        }

        return [
            'nome'      => $nome,
            'descricao' => $descricao,
            'preco'     => $preco,
            'estoque'   => $estoque,
            'ativo'     => !empty($dados['ativo']) ? 1 : 0,
        ];
    }

    private function nomeEmUso(string $nome, ?int $idAtual): bool
    {
        $st = $this->db->prepare('SELECT id FROM cupcakes WHERE LOWER(nome) = LOWER(?)');
        $st->execute([$nome]);
        $id = $st->fetchColumn();
        return $id !== false && (int) $id !== $idAtual;
    }

    public function listarAtivos(): array
    {
        return $this->db->query('SELECT * FROM cupcakes WHERE ativo = 1 ORDER BY nome')->fetchAll();
    }

    public function listarTodos(): array
    {
        return $this->db->query('SELECT * FROM cupcakes ORDER BY ativo DESC, nome')->fetchAll();
    }

    public function buscar(int $id): ?array
    {
        $st = $this->db->prepare('SELECT * FROM cupcakes WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /** @return array<int, array> cupcakes indexados por id */
    public function buscarVarios(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (!$ids) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $st = $this->db->prepare("SELECT * FROM cupcakes WHERE id IN ($marcadores)");
        $st->execute($ids);
        $res = [];
        foreach ($st->fetchAll() as $linha) {
            $res[(int) $linha['id']] = $linha;
        }
        return $res;
    }

    public function criar(array $dados): int
    {
        $d = $this->validar($dados);
        $st = $this->db->prepare(
            'INSERT INTO cupcakes (nome, descricao, preco, estoque, ativo) VALUES (?, ?, ?, ?, ?)'
        );
        $st->execute([$d['nome'], $d['descricao'], $d['preco'], $d['estoque'], $d['ativo']]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): void
    {
        if (!$this->buscar($id)) {
            throw new ValidacaoException(['id' => 'Cupcake não encontrado.']);
        }
        $d = $this->validar($dados, $id);
        $st = $this->db->prepare(
            'UPDATE cupcakes SET nome = ?, descricao = ?, preco = ?, estoque = ?, ativo = ? WHERE id = ?'
        );
        $st->execute([$d['nome'], $d['descricao'], $d['preco'], $d['estoque'], $d['ativo'], $id]);
    }

    /**
     * Exclui o cupcake. Se ele já aparece em algum pedido, apenas desativa
     * (para não quebrar o histórico de pedidos).
     *
     * @return string 'excluido' ou 'desativado'
     */
    public function excluir(int $id): string
    {
        if (!$this->buscar($id)) {
            throw new ValidacaoException(['id' => 'Cupcake não encontrado.']);
        }
        $st = $this->db->prepare('SELECT COUNT(*) FROM itens_pedido WHERE cupcake_id = ?');
        $st->execute([$id]);
        if ((int) $st->fetchColumn() > 0) {
            $this->db->prepare('UPDATE cupcakes SET ativo = 0 WHERE id = ?')->execute([$id]);
            return 'desativado';
        }
        $this->db->prepare('DELETE FROM cupcakes WHERE id = ?')->execute([$id]);
        return 'excluido';
    }
}
