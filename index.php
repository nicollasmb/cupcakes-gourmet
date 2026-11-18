<?php
/**
 * Front controller: toda requisição passa por aqui.
 * Rota no formato index.php?r=controller/acao
 */
declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

use App\Core\NaoEncontradoException;

session_start();

const ROTAS = [
    'vitrine'  => ['index'],
    'carrinho' => ['index', 'adicionar', 'atualizar', 'remover'],
    'pedido'   => ['checkout', 'confirmado', 'acompanhar'],
    'admin'    => ['login', 'logout', 'cupcakes', 'cupcakeForm', 'cupcakeSalvar', 'cupcakeExcluir', 'pedidos', 'pedido', 'pedidoStatus'],
];

$rota = (string) ($_GET['r'] ?? 'vitrine/index');
[$nome, $acao] = array_pad(explode('/', $rota, 2), 2, 'index');

try {
    if (!isset(ROTAS[$nome]) || !in_array($acao, ROTAS[$nome], true)) {
        throw new NaoEncontradoException();
    }
    $classe = 'App\\Controller\\' . ucfirst($nome) . 'Controller';
    $controller = $nome === 'admin' ? new $classe($acao) : new $classe();
    $controller->$acao();
} catch (NaoEncontradoException) {
    http_response_code(404);
    $codigo = 404;
    $mensagem = 'Página não encontrada.';
    require __DIR__ . '/src/View/layout/erro.php';
} catch (\Throwable $e) {
    error_log('[cupcakes] ' . $e);
    http_response_code(500);
    $codigo = 500;
    $mensagem = 'Ocorreu um erro inesperado. Tente novamente em instantes.';
    require __DIR__ . '/src/View/layout/erro.php';
}
