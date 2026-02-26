<?php

function headers()
{
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0); // Resposta vazia, apenas os cabeçalhos
    }
}

function criarUsuario($nome, $email, $senhaHash)
{
    $db_connection = new Database();
    $novoUsuario = [
        "nome" => $nome,
        "email" => $email,
        "senha" => $senhaHash,
        "criado_em" => date("Y-m-d H:i:s")
    ];

    return $db_connection->insert("usuarios", $novoUsuario);
}
