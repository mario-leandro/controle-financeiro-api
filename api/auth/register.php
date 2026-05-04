<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];

if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Nome, email e senha são obrigatórios"]);
    logMsg("Dados incompletos para registro: " . json_encode($dados));
    exit;
}

$db_connection = null;

try {
    $db_connection = new Database();

    $usuarioExistente = $db_connection->get("usuarios", ["email" => $dados['email']]);

    if ($usuarioExistente) {
        http_response_code(409);
        echo json_encode(["error" => "Email já cadastrado"]);
        logMsg("Email já cadastrado: " . $dados['email']);
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
    echo json_encode([
        "success" => false,
        "error" => "Erro ao criar usuário: " . $e->getMessage()
    ]);
    logMsg("Erro ao criar usuário: " . $e->getMessage());
}
