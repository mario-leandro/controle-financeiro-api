<?php

include_once __DIR__ . "/../common.php";
headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

$raw = file_get_contents("php://input");

if (!$raw) {
    http_response_code(400);
    echo json_encode(["error" => "Body vazio"]);
    exit;
}

$dados = json_decode($raw, true);

if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido"]);
    exit;
}

if (empty($dados['email']) || empty($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Email e senha são obrigatórios"]);
    exit;
}

$email = $dados['email'];
$senha = $dados['senha'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Email inválido"]);
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
