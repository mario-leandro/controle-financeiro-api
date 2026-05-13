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

    $transacoes = $db_connection->sql(
        "
    SELECT 
        t.*,
        c.nome AS categoria_nome
    FROM cf_db.transactions t
    LEFT JOIN " . DB_NAME . ".categories c 
        ON t.category_id = c.id
    WHERE t.usuario_id = " . (int)$usuarioId
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
