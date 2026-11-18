<?php
namespace App\Model;

use App\Core\ValidacaoException;
use PDO;

/**
 * Pedido eletrônico: cliente, itens, pagamento e entrega.
 * O total é SEMPRE calculado no servidor a partir dos preços do banco.
 */
class Pedido
{
    public const STATUS = [
        'recebido'             => 'Recebido',
        'em_preparo'           => 'Em preparo',
        'saiu_para_entrega'    => 'Saiu para entrega',
        'pronto_para_retirada' => 'Pronto para retirada',
        'entregue'             => 'Entregue',
        'cancelado'            => 'Cancelado',
    ];
    public const STATUS_FINAIS = ['entregue', 'cancelado'];

    public const PAGAMENTOS = ['pix' => 'PIX', 'cartao' => 'Cartão na entrega', 'dinheiro' => 'Dinheiro'];
    public const ENTREGAS = ['entrega' => 'Entrega no endereço', 'retirada' => 'Retirar na loja'];

    public function __construct(private PDO $db, private float $taxaEntrega = 8.00)
    {
    }

    public function validarCheckout(array $dados): array
    {
        $erros = [];

        $nome = trim((string) ($dados['nome'] ?? ''));
        if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
            $erros['nome'] = 'Informe seu nome completo (3 a 100 caracteres).';
        }

        $email = trim(mb_strtolower((string) ($dados['email'] ?? '')));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $erros['email'] = 'Informe um e-mail válido.';
        }

        $telefone = preg_replace('/\D/', '', (string) ($dados['telefone'] ?? ''));
        if (strlen($telefone) < 10 || strlen($telefone) > 11) {
            $erros['telefone'] = 'Informe um telefone com DDD (10 ou 11 dígitos).';
        }

        $tipoEntrega = (string) ($dados['tipo_entrega'] ?? '');
        if (!array_key_exists($tipoEntrega, self::ENTREGAS)) {
            $erros['tipo_entrega'] = 'Escolha entrega ou retirada.';
        }

        $endereco = trim((string) ($dados['endereco'] ?? ''));
        if ($tipoEntrega === 'entrega' && (mb_strlen($endereco) < 10 || mb_strlen($endereco) > 255)) {
            $erros['endereco'] = 'Informe o endereço completo para entrega (rua, número e bairro).';
        }

        $pagamento = (string) ($dados['forma_pagamento'] ?? '');
        if (!array_key_exists($pagamento, self::PAGAMENTOS)) {
            $erros['forma_pagamento'] = 'Escolha uma forma de pagamento.';
        }

        $trocoPara = null;
        if ($pagamento === 'dinheiro') {
            $trocoValor = str_replace(',', '.', (string) ($dados['troco_para'] ?? ''));
            if ($trocoValor !== '' && is_numeric($trocoValor)) {
                $trocoPara = (float) $trocoValor;
            }
        }

        if ($erros) {
            throw new ValidacaoException($erros);
        }

        return [
            'nome'            => $nome,
            'email'           => $email,
            'telefone'        => $telefone,
            'tipo_entrega'    => $tipoEntrega,
            'endereco'        => $tipoEntrega === 'entrega' ? $endereco : null,
            'forma_pagamento' => $pagamento,
            'troco_para'      => $trocoPara,
        ];
    }

    /**
     * Cria o pedido em uma transação: valida, baixa estoque e grava itens.
     *
     * @param array $itens [cupcake_id => quantidade]
     */
    public function criar(array $dadosCheckout, array $itens): int
    {
        $d = $this->validarCheckout($dadosCheckout);
        if (!$itens) {
            throw new ValidacaoException(['carrinho' => 'Seu carrinho está vazio.']);
        }

        $this->db->beginTransaction();
        try {
            $subtotal = 0.0;
            $linhas = [];
            $buscar = $this->db->prepare('SELECT id, nome, preco, estoque, ativo FROM cupcakes WHERE id = ?');
            $baixar = $this->db->prepare(
                'UPDATE cupcakes SET estoque = estoque - ? WHERE id = ? AND ativo = 1 AND estoque >= ?'
            );

            foreach ($itens as $cupcakeId => $qtd) {
                $qtd = (int) $qtd;
                if ($qtd < 1) {
                    throw new ValidacaoException(['carrinho' => 'Quantidade inválida no carrinho.']);
                }
                $buscar->execute([(int) $cupcakeId]);
                $c = $buscar->fetch();
                if (!$c || !$c['ativo']) {
                    throw new ValidacaoException(['carrinho' => 'Um dos cupcakes do carrinho não está mais disponível.']);
                }
                $baixar->execute([$qtd, $c['id'], $qtd]);
                if ($baixar->rowCount() === 0) {
                    throw new ValidacaoException([
                        'carrinho' => "Estoque insuficiente para {$c['nome']} (disponível: {$c['estoque']}).",
                    ]);
                }
                $subtotal += $qtd * (float) $c['preco'];
                $linhas[] = [$c['id'], $qtd, (float) $c['preco']];
            }

            $taxa = $d['tipo_entrega'] === 'entrega' ? $this->taxaEntrega : 0.0;
            $total = round($subtotal + $taxa, 2);

            if ($d['troco_para'] !== null && $d['troco_para'] < $total) {
                throw new ValidacaoException([
                    'troco_para' => 'O valor para troco deve ser maior que o total do pedido (' . formataMoeda($total) . ').'
                ]);
            }

            $clienteId = $this->salvarCliente($d);

            $st = $this->db->prepare(
                'INSERT INTO pedidos (cliente_id, status, tipo_entrega, endereco_entrega, forma_pagamento, subtotal, taxa_entrega, total, troco_para)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $st->execute([
                $clienteId, 'recebido', $d['tipo_entrega'], $d['endereco'], $d['forma_pagamento'],
                round($subtotal, 2), $taxa, $total, $d['troco_para'],
            ]);
            $pedidoId = (int) $this->db->lastInsertId();

            $item = $this->db->prepare(
                'INSERT INTO itens_pedido (pedido_id, cupcake_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)'
            );
            foreach ($linhas as [$cupcakeId, $qtd, $preco]) {
                $item->execute([$pedidoId, $cupcakeId, $qtd, $preco]);
            }

            $this->db->commit();
            return $pedidoId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function salvarCliente(array $d): int
    {
        $st = $this->db->prepare('SELECT id FROM clientes WHERE email = ?');
        $st->execute([$d['email']]);
        $id = $st->fetchColumn();
        if ($id !== false) {
            $this->db->prepare('UPDATE clientes SET nome = ?, telefone = ? WHERE id = ?')
                ->execute([$d['nome'], $d['telefone'], $id]);
            return (int) $id;
        }
        $this->db->prepare('INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)')
            ->execute([$d['nome'], $d['email'], $d['telefone']]);
        return (int) $this->db->lastInsertId();
    }

    public function buscar(int $id): ?array
    {
        $st = $this->db->prepare(
            'SELECT p.*, c.nome AS cliente_nome, c.email AS cliente_email, c.telefone AS cliente_telefone
               FROM pedidos p JOIN clientes c ON c.id = p.cliente_id WHERE p.id = ?'
        );
        $st->execute([$id]);
        $pedido = $st->fetch();
        if (!$pedido) {
            return null;
        }
        $st = $this->db->prepare(
            'SELECT i.*, cp.nome FROM itens_pedido i JOIN cupcakes cp ON cp.id = i.cupcake_id WHERE i.pedido_id = ? ORDER BY i.id'
        );
        $st->execute([$id]);
        $pedido['itens'] = $st->fetchAll();
        return $pedido;
    }

    /** Busca para o cliente: exige o número do pedido E o e-mail usado na compra. */
    public function buscarDoCliente(int $id, string $email): ?array
    {
        $pedido = $this->buscar($id);
        if (!$pedido || $pedido['cliente_email'] !== trim(mb_strtolower($email))) {
            return null;
        }
        return $pedido;
    }

    public function listar(?string $status = null): array
    {
        $sql = 'SELECT p.*, c.nome AS cliente_nome FROM pedidos p JOIN clientes c ON c.id = p.cliente_id';
        $params = [];
        if ($status !== null && array_key_exists($status, self::STATUS)) {
            $sql .= ' WHERE p.status = ?';
            $params[] = $status;
        }
        $st = $this->db->prepare($sql . ' ORDER BY p.id DESC');
        $st->execute($params);
        return $st->fetchAll();
    }

    /** Status que fazem sentido para o tipo de entrega do pedido. */
    public static function statusPermitidos(array $pedido): array
    {
        $s = self::STATUS;
        unset($s[$pedido['tipo_entrega'] === 'entrega' ? 'pronto_para_retirada' : 'saiu_para_entrega']);
        return $s;
    }

    public function atualizarStatus(int $id, string $novoStatus): void
    {
        $pedido = $this->buscar($id);
        if (!$pedido) {
            throw new ValidacaoException(['pedido' => 'Pedido não encontrado.']);
        }
        if (!array_key_exists($novoStatus, self::statusPermitidos($pedido))) {
            throw new ValidacaoException(['status' => 'Status inválido para este pedido.']);
        }
        if (in_array($pedido['status'], self::STATUS_FINAIS, true)) {
            throw new ValidacaoException(['status' => 'Pedidos entregues ou cancelados não podem mudar de status.']);
        }
        if ($novoStatus === $pedido['status']) {
            return;
        }

        $this->db->beginTransaction();
        try {
            if ($novoStatus === 'cancelado') {
                // Devolve os itens ao estoque.
                $devolver = $this->db->prepare('UPDATE cupcakes SET estoque = estoque + ? WHERE id = ?');
                foreach ($pedido['itens'] as $item) {
                    $devolver->execute([$item['quantidade'], $item['cupcake_id']]);
                }
            }
            $this->db->prepare('UPDATE pedidos SET status = ? WHERE id = ?')->execute([$novoStatus, $id]);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
