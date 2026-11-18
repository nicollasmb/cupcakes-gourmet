<?php
namespace Tests;

use App\Core\Database;
use PDO;

/**
 * Conexão com o banco de testes MySQL.
 */
trait BancoDeTeste
{
    protected PDO $db;

    protected function criarBanco(): void
    {
        $this->db = Database::criar([
            'host'   => getenv('DB_HOST') ?: 'localhost',
            'port'   => getenv('DB_PORT') ?: '3306',
            'dbname' => getenv('DB_NAME_TEST') ?: 'cupcakes_test',
            'user'   => getenv('DB_USER') ?: 'root',
            'pass'   => getenv('DB_PASS') ?: '',
        ]);

        $this->db->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->exec('TRUNCATE TABLE itens_pedido');
        $this->db->exec('TRUNCATE TABLE pedidos');
        $this->db->exec('TRUNCATE TABLE clientes');
        $this->db->exec('TRUNCATE TABLE cupcakes');
        $this->db->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    protected function inserirCupcake(string $nome = 'Red Velvet', float $preco = 10.0, int $estoque = 10, int $ativo = 1): int
    {
        $st = $this->db->prepare('INSERT INTO cupcakes (nome, preco, estoque, ativo) VALUES (?, ?, ?, ?)');
        $st->execute([$nome, $preco, $estoque, $ativo]);
        return (int) $this->db->lastInsertId();
    }

    protected function estoqueDe(int $id): int
    {
        return (int) $this->db->query("SELECT estoque FROM cupcakes WHERE id = $id")->fetchColumn();
    }
}
