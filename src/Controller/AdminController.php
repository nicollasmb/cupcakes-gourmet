<?php
namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Core\NaoEncontradoException;
use App\Core\ValidacaoException;
use App\Model\Cupcake;
use App\Model\Pedido;

/**
 * Área do lojista: cadastro de cupcakes e gestão de pedidos.
 */
class AdminController extends Controller
{
    private const ACOES_PUBLICAS = ['login'];

    private Cupcake $cupcakes;
    private Pedido $pedidos;

    public function __construct(string $acao)
    {
        if (!in_array($acao, self::ACOES_PUBLICAS, true) && empty($_SESSION['admin'])) {
            $this->flash('warning', 'Faça login para acessar a área do lojista.');
            $this->redirecionar('admin/login');
        }
        $this->cupcakes = new Cupcake(Database::conexao());
        $this->pedidos = new Pedido(Database::conexao());
    }

    public function login(): void
    {
        $erro = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->exigirPost();
            $config = require __DIR__ . '/../../config/config.php';
            if (hash_equals((string) $config['admin_senha'], (string) ($_POST['senha'] ?? ''))) {
                session_regenerate_id(true);
                $_SESSION['admin'] = true;
                $this->redirecionar('admin/pedidos');
            }
            $erro = 'Senha incorreta.';
        }
        $this->render('admin/login', ['erro' => $erro, 'titulo' => 'Área do lojista']);
    }

    public function logout(): void
    {
        $this->exigirPost();
        unset($_SESSION['admin']);
        $this->flash('success', 'Você saiu da área do lojista.');
        $this->redirecionar('vitrine/index');
    }

    public function cupcakes(): void
    {
        $this->render('admin/cupcakes', ['cupcakes' => $this->cupcakes->listarTodos(), 'titulo' => 'Cupcakes']);
    }

    public function cupcakeForm(): void
    {
        $cupcake = ['nome' => '', 'descricao' => '', 'preco' => '', 'estoque' => '0', 'ativo' => 1];
        $id = null;
        if (isset($_GET['id'])) {
            $id = $this->idDaRequisicao();
            $cupcake = $this->cupcakes->buscar($id) ?? throw new NaoEncontradoException();
            $cupcake['preco'] = number_format((float) $cupcake['preco'], 2, ',', '');
        }
        $this->render('admin/cupcake_form', ['cupcake' => $cupcake, 'id' => $id, 'erros' => [], 'titulo' => 'Cupcake']);
    }

    public function cupcakeSalvar(): void
    {
        $this->exigirPost();
        $id = !empty($_POST['id']) ? $this->idDaRequisicao('id', $_POST) : null;
        try {
            if ($id) {
                $this->cupcakes->atualizar($id, $_POST);
                $this->flash('success', 'Cupcake atualizado.');
            } else {
                $this->cupcakes->criar($_POST);
                $this->flash('success', 'Cupcake cadastrado.');
            }
            $this->redirecionar('admin/cupcakes');
        } catch (ValidacaoException $e) {
            $this->render('admin/cupcake_form', [
                'cupcake' => $_POST + ['ativo' => 0], 'id' => $id, 'erros' => $e->erros(), 'titulo' => 'Cupcake',
            ]);
        }
    }

    public function cupcakeExcluir(): void
    {
        $this->exigirPost();
        try {
            $resultado = $this->cupcakes->excluir($this->idDaRequisicao('id', $_POST));
            $this->flash('success', $resultado === 'excluido'
                ? 'Cupcake excluído.'
                : 'Este cupcake já tem pedidos, então foi apenas desativado (não aparece mais na vitrine).');
        } catch (ValidacaoException $e) {
            $this->flash('danger', $e->getMessage());
        }
        $this->redirecionar('admin/cupcakes');
    }

    public function pedidos(): void
    {
        $status = $_GET['status'] ?? null;
        $this->render('admin/pedidos', [
            'pedidos' => $this->pedidos->listar($status ?: null), 'filtro' => $status, 'titulo' => 'Pedidos',
        ]);
    }

    public function pedido(): void
    {
        $pedido = $this->pedidos->buscar($this->idDaRequisicao()) ?? throw new NaoEncontradoException();
        $this->render('admin/pedido', ['pedido' => $pedido, 'titulo' => 'Pedido #' . $pedido['id']]);
    }

    public function pedidoStatus(): void
    {
        $this->exigirPost();
        $id = $this->idDaRequisicao('id', $_POST);
        try {
            $this->pedidos->atualizarStatus($id, (string) ($_POST['status'] ?? ''));
            $this->flash('success', 'Status atualizado.');
        } catch (ValidacaoException $e) {
            $this->flash('danger', $e->getMessage());
        }
        $this->redirecionar('admin/pedido', ['id' => $id]);
    }
}
