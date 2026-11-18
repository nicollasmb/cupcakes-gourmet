<?php
/**
 * Funções auxiliares (helpers) do projeto.
 */

function formataMoeda(float|int|string $valor): string
{
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function formataData(string $data): string
{
    return date('d/m/Y H:i', strtotime($data));
}

function limpaTexto(mixed $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}
