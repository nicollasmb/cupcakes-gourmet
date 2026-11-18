<?php
namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Core\NaoEncontradoException;
use App\Core\ValidacaoException;
use App\Model\Carrinho;
use App\Model\Pedido;

class PedidoController extends Controller
{
    private Pedido $pedidos;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->pedidos = new Pedido(Database::conexao(), (float) $config['taxa_entrega']);
    }

    public function checkout(): void
    {
        $_SESSION['carrinho'] ??= [];
        $carrinho = new Carrinho($_SESSION['carrinho']);
        if ($carrinho->vazio()) {
            $this->flash('warning', 'Adicione pelo menos um cupcake antes de finalizar.');
            $this->redirecionar('vitrine/index');
        }

        $erros = [];
        $dados = $_SESSION['ultimo_checkout'] ?? ['tipo_entrega' => 'entrega', 'forma_pagamento' => 'pix'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->exigirPost();
            $dados = $_POST;
            try {
                $id = $this->pedidos->criar($_POST, $carrinho->itens());
                $carrinho->limpar();
                $_SESSION['pedidos_feitos'][] = $id;
                $_SESSION['ultimo_checkout'] = array_intersect_key($_POST, array_flip(['nome', 'email', 'telefone', 'endereco']));
                $this->redirecionar('pedido/confirmado', ['id' => $id]);
            } catch (ValidacaoException $e) {
                $erros = $e->erros();
            }
        }

        $this->render('pedido/checkout', [
            'dados' => $dados, 'erros' => $erros, 'titulo' => 'Finalizar pedido',
        ]);
    }

    public function confirmado(): void
    {
        $id = $this->idDaRequisicao();
        // Só quem fez o pedido nesta sessão vê a confirmação direta.
        if (!in_array($id, $_SESSION['pedidos_feitos'] ?? [], true)) {
            throw new NaoEncontradoException();
        }
        $pedido = $this->pedidos->buscar($id) ?? throw new NaoEncontradoException();
        $this->render('pedido/detalhe', ['pedido' => $pedido, 'confirmacao' => true, 'titulo' => 'Pedido confirmado']);
    }

    public function acompanhar(): void
    {
        $erro = null;
        $dados = ['numero' => $_GET['numero'] ?? '', 'email' => ''];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->exigirPost();
            $dados = ['numero' => trim($_POST['numero'] ?? ''), 'email' => trim($_POST['email'] ?? '')];
            $numero = filter_var($dados['numero'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $pedido = $numero ? $this->pedidos->buscarDoCliente($numero, $dados['email']) : null;
            if ($pedido) {
                $this->render('pedido/detalhe', ['pedido' => $pedido, 'confirmacao' => false, 'titulo' => 'Pedido #' . $numero]);
                return;
            }
            $erro = 'Não encontramos um pedido com esse número e e-mail.';
        }
        $this->render('pedido/acompanhar', ['dados' => $dados, 'erro' => $erro, 'titulo' => 'Acompanhar pedido']);
    }
}
