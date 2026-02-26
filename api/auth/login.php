<?php

include_once __DIR__ . "/../commom.php";

headers();

$dados = json_decode(file_get_contents("php://input"), true);

if (empty($dados['email']) || empty($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Email e senha são obrigatórios"]);
    logMsg("Tentativa de login com email ou senha vazios");
    exit;
}

$email = $dados['email'];
$senha = $dados['senha'];
$token = $dados['token'] ?? null;

$db_connection = null;

try {
    $db_connection = new Database();

    // Verificar se o email existe
    $usuario = $db_connection->get("usuarios", ["email" => $email]);
    if (!$usuario) {
        http_response_code(401);
        echo json_encode(["error" => "Email ou senha inválidos"]);
        logMsg("Tentativa de login com email não existente: " . $email);
        exit;
    }

    // Verificar a senha
    if (!password_verify($senha, $usuario['senha'])) {
        http_response_code(401);
        echo json_encode(["error" => "Email ou senha inválidos"]);
        logMsg("Tentativa de login com senha incorreta para o email: " . $email);
        exit;
    }

    // Gerar token JWT
    $usuarioId = $usuario['id'];

    $jwt = gerarJwt($usuarioId);

    echo json_encode([
        "success" => true,
        "token" => $jwt,
        "usuario" => [
            "id" => $usuario['id'],
            "nome" => $usuario['nome'],
            "email" => $usuario['email']
        ]
    ]);
    logMsg("Usuário logado com sucesso: " . $email);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao tentar logar usuário"]);
    logMsg("Erro ao tentar logar usuário: " . $e->getMessage());
}
