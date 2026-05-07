<?php

$usuarioId = $GLOBALS["usuario_id"] ?? null;

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

$db = new DataBase();

$contas = $db->get("accounts", [
    "usuario_id" => $usuarioId
]);

echo json_encode([
    "success" => true,
    "message" => "Contas listadas com sucesso",
    "data" => $contas
]);