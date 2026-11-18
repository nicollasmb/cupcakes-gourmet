<?php
namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = md5(uniqid((string) mt_rand(), true));
        }
        return $_SESSION['csrf'];
    }

    public static function campo(): string
    {
        return '<input type="hidden" name="csrf" value="' . self::token() . '">';
    }

    public static function valido(?string $token): bool
    {
        return !empty($token) && !empty($_SESSION['csrf']) && $_SESSION['csrf'] === $token;
    }
}
