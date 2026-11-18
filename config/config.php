<?php
/**
 * Configuração da aplicação.
 *
 * Em produção ou desenvolvimento local (XAMPP / MySQL).
 */
return [
    'app_nome'     => 'Cupcakes Gourmet',
    'taxa_entrega' => 8.00,

    // Senha da área administrativa.
    'admin_senha'  => getenv('ADMIN_SENHA') ?: 'admin123',

    'db' => [
        'host'   => getenv('DB_HOST') ?: 'localhost',
        'port'   => getenv('DB_PORT') ?: '3306',
        'dbname' => getenv('DB_NAME') ?: 'cupcakes',
        'user'   => getenv('DB_USER') ?: 'root',
        'pass'   => getenv('DB_PASS') ?: '',
    ],
];
