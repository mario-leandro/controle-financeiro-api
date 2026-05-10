<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];

$email = $dados['email'];
$senha = $dados['senha'];

if (!$email || !$senha) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Email e senha são obrigatórios"
    ]);
    exit;
}

$db_connection = null;

try {
    $db_connection = new Database();

    $usuarios = $db_connection->get("usuarios", ["email" => $email]);
    $usuario = $usuarios[0] ?? null;

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
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
        "success" => true,
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
