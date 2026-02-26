<?php

include_once __DIR__ . "/../common.php";

headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    logMsg("Requisição com método não permitido: " . $_SERVER['REQUEST_METHOD']);
    exit;
}

$body = json_decode(file_get_contents("php://input"), true);

if (!isset($body['refresh_token'])) {
    throw new Exception("Refresh token não enviado");
}

$refreshToken = $body['refresh_token'];
$hash = hashToken($refreshToken);

$db_connection = null;

try {
    $db_connection = new Database();

    // Buscar no banco
    $tokenDb = $db_connection->get("authentication", [
        "token" => $hash,
        "tipo" => "refresh",
        "revogado" => false,
        "dth_expire[>]" => date("Y-m-d H:i:s")
    ]);

    if (!$tokenDb) {
        throw new Exception("Refresh token inválido");
    }

    // Gerar novo JWT
    $novoJwt = gerarJwt($tokenDb['usuario_id']);

    echo json_encode([
        "access_token" => $novoJwt
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => $e->getMessage()]);
}
