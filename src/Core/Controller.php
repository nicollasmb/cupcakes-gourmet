<?php
namespace App\Core;

/**
 * Controller base: renderização de views, redirecionamento e mensagens flash.
 */
abstract class Controller
{
    protected function render(string $view, array $dados = []): void
    {
        extract($dados);
        $config = require __DIR__ . '/../../config/config.php';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $qtdCarrinho = array_sum($_SESSION['carrinho'] ?? []);

        ob_start();
        require __DIR__ . '/../View/' . $view . '.php';
        $conteudo = ob_get_clean();
        require __DIR__ . '/../View/layout/base.php';
    }

    /** Renderiza um trecho de view reutilizável (sem layout). */
    protected function renderParcial(string $view, array $dados = []): string
    {
        extract($dados);
        ob_start();
        require __DIR__ . '/../View/' . $view . '.php';
        return ob_get_clean();
    }

    protected function redirecionar(string $rota, array $params = []): void
    {
        $query = http_build_query(['r' => $rota] + $params);
        header('Location: index.php?' . $query);
        exit;
    }

    protected function flash(string $tipo, string $mensagem): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
    }

    protected function exigirPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new NaoEncontradoException();
        }
        if (!Csrf::valido($_POST['csrf'] ?? null)) {
            $this->flash('danger', 'Sua sessão expirou. Tente novamente.');
            $this->redirecionar('vitrine/index');
        }
    }

    protected function idDaRequisicao(string $chave = 'id', ?array $origem = null): int
    {
        $origem ??= $_GET;
        $id = filter_var($origem[$chave] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            throw new NaoEncontradoException();
        }
        return $id;
    }
}
