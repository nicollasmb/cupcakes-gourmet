<?php
/**
 * Autoload simples (PSR-4) para funcionar em hospedagens sem Composer.
 */

// Carrega variáveis do arquivo .env, se existir
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $linhas = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#')) {
            continue;
        }
        if (str_contains($linha, '=')) {
            [$chave, $valor] = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim($valor);
            if (!getenv($chave)) {
                putenv("$chave=$valor");
                $_ENV[$chave] = $valor;
                $_SERVER[$chave] = $valor;
            }
        }
    }
}

require_once __DIR__ . '/helpers.php';

spl_autoload_register(function (string $classe): void {
    $prefixo = 'App\\';
    if (strncmp($classe, $prefixo, strlen($prefixo)) !== 0) {
        return;
    }
    $arquivo = __DIR__ . '/' . str_replace('\\', '/', substr($classe, strlen($prefixo))) . '.php';
    if (is_file($arquivo)) {
        require $arquivo;
    }
});
