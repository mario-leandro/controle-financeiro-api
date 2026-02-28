<?php

include_once __DIR__ . "/../common.php";
headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

try {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (empty($dados['refresh_token'])) {
        http_response_code(400);
        echo json_encode(["error" => "Refresh token não fornecido"]);
        exit;
    }

    $refreshToken = $dados['refresh_token'];
    $refreshHash = hashToken($refreshToken);

    $db = new Database();

    // Atualiza token como revogado
    $atualizado = $db->update(
        "authentication",
        ["revogado" => 1],
        ["token" => $refreshHash, "tipo" => "refresh"]
    );

    if (!$atualizado) {
        http_response_code(400);
        echo json_encode(["error" => "Token inválido ou já revogado"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Logout realizado com sucesso"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao realizar logout"]);
}
