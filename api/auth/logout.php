<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];

try {
    if (empty($dados['token'])) {
        http_response_code(400);
        echo json_encode(["error" => "Refresh token não fornecido"]);
        exit;
    }

    $token = $dados['token'];
    $refreshHash = hashToken($token);

    $db = new Database();

    $atualizado = $db->update(
        "authentication",
        ["revogado" => 1],
        ["token" => $token, "tipo" => "refresh"]
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
