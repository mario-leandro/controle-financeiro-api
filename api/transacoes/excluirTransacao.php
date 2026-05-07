<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];
$usuarioId = $GLOBALS["usuario_id"] ?? null;

$id = $dados["id"] ?? null;

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

if (!$id) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ID da transação é obrigatório"]);
    exit;
}

$db_connection = null;

try {
    $db_connection = new Database();

    $deleted = $db_connection->delete("transactions", [
        "id" => $id,
        "usuario_id" => $usuarioId
    ]);

    if (!$deleted) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Transação não encontrada"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Transação excluída com sucesso"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro interno"]);
}
