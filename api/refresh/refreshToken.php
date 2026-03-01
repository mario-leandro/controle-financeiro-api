<?php

include_once __DIR__ . "/../common.php";
headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
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

if (empty($dados['refresh_token'])) {
    http_response_code(400);
    echo json_encode(["error" => "Refresh token não fornecido"]);
    exit;
}

$refreshToken = $dados['refresh_token'];
$refreshHash = hashToken($refreshToken);

try {
    $db_connection = new Database();

    $buscarRefreshToken = $db_connection->get_limit(
        "authentication",
        ["token" => $refreshHash, "tipo" => "refresh"],
        1
    );

    $tokenDb = $buscarRefreshToken[0] ?? null;

    if (!$tokenDb) {
        http_response_code(401);
        echo json_encode(["error" => "Refresh token inválido"]);
        exit;
    }

    if ($tokenDb['revogado'] || strtotime($tokenDb['dth_expire']) < time()) {
        if ($tokenDb['revogado']) {
            $db_connection->update(
                "authentication",
                ["revogado" => 1],
                ["usuario_id" => $tokenDb['usuario_id']]
            );
        }

        http_response_code(401);
        echo json_encode(["error" => "Refresh token expirado ou revogado"]);
        exit;
    }

    $usuarioId = $tokenDb['usuario_id'];

    $db_connection->update(
        "authentication",
        ["revogado" => 1],
        ["id" => $tokenDb['id']]
    );

    $novoAccessToken = gerarJwt($usuarioId);
    $novoRefreshToken = gerarRefreshToken();
    $novoRefreshHash = hashToken($novoRefreshToken);

    $db_connection->insert("authentication", [
        "usuario_id" => $usuarioId,
        "token" => $novoRefreshHash,
        "tipo" => "refresh",
        "dth_expire" => date("Y-m-d H:i:s", time() + (60 * 60 * 24 * 30)),
        "ip" => $_SERVER['REMOTE_ADDR'] ?? null,
        "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);

    echo json_encode([
        "access_token" => $novoAccessToken,
        "refresh_token" => $novoRefreshToken
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao renovar token"]);
}
