<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];
$usuarioId = $GLOBALS["usuario_id"] ?? null;

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

$db_connection = null;

try {
    $db_connection = new Database();

    $transacoes = $db_connection->get(
        "transactions",
        ["usuario_id" => $usuarioId],
        [
            "id",
            "account_id",
            "category_id",
            "tipo",
            "descricao",
            "valor",
            "data_transacao",
            "observacao",
            "created_at"
        ]
    );

    echo json_encode([
        "success" => true,
        "message" => "Transações listadas com sucesso",
        "data" => $transacoes
    ]);
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao listar transações"]);
}
