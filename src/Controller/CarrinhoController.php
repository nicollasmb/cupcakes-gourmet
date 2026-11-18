<?php
namespace App\Controller;

use App\Core\Controller;
use App\Core\Database;
use App\Core\ValidacaoException;
use App\Model\Carrinho;
use App\Model\Cupcake;

class CarrinhoController extends Controller
{
    private Carrinho $carrinho;
    private Cupcake $cupcakes;

    public function __construct()
    {
        $_SESSION['carrinho'] ??= [];
        $this->carrinho = new Carrinho($_SESSION['carrinho']);
        $this->cupcakes = new Cupcake(Database::conexao());
    }

    public function index(): void
    {
        $produtos = $this->cupcakes->buscarVarios(array_keys($this->carrinho->itens()));
        $linhas = [];
        $subtotal = 0.0;
        foreach ($this->carrinho->itens() as $id => $qtd) {
            if (!isset($produtos[$id]) || !$produtos[$id]['ativo']) {
                $this->carrinho->remover($id); // produto saiu da vitrine
                continue;
            }
            $p = $produtos[$id];
            $total = $qtd * (float) $p['preco'];
            $subtotal += $total;
            $linhas[] = ['id' => $id, 'nome' => $p['nome'], 'preco' => (float) $p['preco'],
                         'quantidade' => $qtd, 'estoque' => (int) $p['estoque'], 'total' => $total];
        }
        $this->render('carrinho/index', ['linhas' => $linhas, 'subtotal' => $subtotal, 'titulo' => 'Carrinho']);
    }

    public function adicionar(): void
    {
        $this->exigirPost();
        $id = $this->idDaRequisicao('cupcake_id', $_POST);
        $cupcake = $this->cupcakes->buscar($id);
        try {
            if (!$cupcake || !$cupcake['ativo']) {
                throw new ValidacaoException(['cupcake' => 'Este cupcake não está disponível.']);
            }
            $qtdDesejada = ($this->carrinho->itens()[$id] ?? 0) + (int) ($_POST['quantidade'] ?? 1);
            if ($qtdDesejada > (int) $cupcake['estoque']) {
                throw new ValidacaoException(['estoque' => "Só temos {$cupcake['estoque']} unidade(s) de {$cupcake['nome']}."]);
            }
            $this->carrinho->adicionar($id, $_POST['quantidade'] ?? 1);
            $this->flash('success', "{$cupcake['nome']} adicionado ao carrinho.");
        } catch (ValidacaoException $e) {
            $this->flash('danger', $e->getMessage());
        }
        $this->redirecionar(($_POST['voltar'] ?? '') === 'carrinho' ? 'carrinho/index' : 'vitrine/index');
    }

    public function atualizar(): void
    {
        $this->exigirPost();
        $id = $this->idDaRequisicao('cupcake_id', $_POST);
        try {
            $cupcake = $this->cupcakes->buscar($id);
            $qtd = $_POST['quantidade'] ?? '';
            if ($cupcake && filter_var($qtd, FILTER_VALIDATE_INT) !== false && (int) $qtd > (int) $cupcake['estoque']) {
                throw new ValidacaoException(['estoque' => "Só temos {$cupcake['estoque']} unidade(s) de {$cupcake['nome']}."]);
            }
            $this->carrinho->atualizar($id, $qtd);
            $this->flash('success', 'Carrinho atualizado.');
        } catch (ValidacaoException $e) {
            $this->flash('danger', $e->getMessage());
        }
        $this->redirecionar('carrinho/index');
    }

    public function remover(): void
    {
        $this->exigirPost();
        $this->carrinho->remover($this->idDaRequisicao('cupcake_id', $_POST));
        $this->flash('success', 'Item removido do carrinho.');
        $this->redirecionar('carrinho/index');
    }
}
