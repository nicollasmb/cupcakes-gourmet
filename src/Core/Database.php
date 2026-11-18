<?php
namespace App\Core;

use PDO;

/**
 * Conexão PDO com banco de dados MySQL.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function conexao(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../../config/config.php';
            self::$pdo = self::criar($config['db']);
        }
        return self::$pdo;
    }

    public static function criar(array $c): PDO
    {
        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['dbname']};charset=utf8mb4";
        return new PDO($dsn, $c['user'], $c['pass'], $opcoes);
    }

    public static function definirConexao(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }
}
