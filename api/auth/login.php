<?php

include_once __DIR__ . "/../common.php";
headers();

$dados = json_decode(file_get_contents("php://input"), true);

if (empty($dados['email']) || empty($dados['senha'])) {
    http_response_code(400);
    exit;
}

$db_connection = null;

try {
    $db_connection = new Database();

    $usuario = $db_connection->get("usuarios", ["email" => $dados['email']]);

    if (!$usuario || !password_verify($dados['senha'], $usuario['senha'])) {
        http_response_code(401);
        echo json_encode(["error" => "Email ou senha inválidos"]);
        exit;
    }

    $usuarioId = $usuario['id'];

    $accessToken = gerarJwt($usuarioId);
    $refreshToken = gerarRefreshToken();
    $refreshHash = hashToken($refreshToken);

    $db_connection->insert("authentication", [
        "usuario_id" => $usuarioId,
        "token" => $refreshHash,
        "tipo" => "refresh",
        "dth_expire" => date("Y-m-d H:i:s", time() + (60 * 60 * 24 * 30)), // 30 dias
        "ip" => $_SERVER['REMOTE_ADDR'] ?? null,
        "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);

    echo json_encode([
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "usuario" => [
            "id" => $usuario['id'],
            "nome" => $usuario['nome'],
            "email" => $usuario['email']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro interno"]);
}
