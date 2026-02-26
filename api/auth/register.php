<?php

include_once __DIR__ . "/../common.php";
headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Nome, email e senha são obrigatórios"]);
    exit;
}

$db_connection = null;

try {
    $db_connection = new Database();

    // Verifica se email já existe
    $usuarioExistente = $db_connection->get("usuarios", ["email" => $dados['email']]);

    if ($usuarioExistente) {
        http_response_code(409);
        echo json_encode(["error" => "Email já cadastrado"]);
        exit;
    }

    $senhaHash = password_hash($dados['senha'], PASSWORD_BCRYPT);

    $usuarioId = criarUsuario(
        $dados['nome'],
        $dados['email'],
        $senhaHash
    );

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "usuario_id" => $usuarioId
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao criar usuário"]);
}
