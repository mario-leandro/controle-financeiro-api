<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];
// var_dump($dados);

$nome = $dados['nome'];
$email = $dados['email'];
$senha = $dados['senha'];

if (empty($nome) || empty($email) || empty($senha)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Nome, email, senha são obrigatórios"]);
    exit();
}

$db_connection = null;

try {
    $db_connection = new Database();
    // var_dump($db_connection);

    $usuarioExistente = $db_connection->get("usuarios", ["email" => $email]);

    if ($usuarioExistente) {
        http_response_code(409);
        echo json_encode(["success" => false, "error" => "Email já cadastrado"]);
        exit();
    }

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    $novoUsuario = [
        "nome" => $nome,
        "email" => $email,
        "senha" => $senhaHash,
        "criado_em" => date("Y-m-d H:i:s")
    ];

    $db_connection->insert("usuarios", $novoUsuario);

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "usuario_id" => $novoUsuario
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erro ao criar usuário: " . $e->getMessage()
    ]);
    logMsg("Erro ao criar usuário: " . $e->getMessage());
}
