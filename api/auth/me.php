<?php

try {
    $usuarioId = $GLOBALS["usuario_id"] ?? null;

    if (!$usuarioId) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Usuário não autenticado"
        ]);
        exit;
    }

    $db_connection = new Database();

    $usuario = $db_connection->get(
        "usuarios",
        ["id" => $usuarioId],
        ["id", "nome", "email", "criado_em"]
    )[0] ?? null;

    if (!$usuario) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Usuário não encontrado"
        ]);
        logMsg("Usuário autenticado não encontrado: ID $usuarioId");
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Usuário autenticado",
        "data" => [
            "user" => $usuario
        ]
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Não autorizado"
    ]);
    logMsg("Acesso não autorizado: " . $e->getMessage());
}