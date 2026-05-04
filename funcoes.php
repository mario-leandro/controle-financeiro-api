<?php

function headers()
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    $allowedOrigins = [
        "http://localhost:3000",
        "http://192.168.18.72:3000"
    ];

    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
    }

    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Content-Type: application/json");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
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
