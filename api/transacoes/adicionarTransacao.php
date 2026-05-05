<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];
$usuarioId = $GLOBALS["usuario_id"] ?? null;

$tipo = $dados["tipo"] ?? null;
$accountId = $dados["account_id"] ?? null;
$categoryId = $dados["category_id"] ?? null;
$descricao = $dados["descricao"] ?? null;
$valor = $dados["valor"] ?? null;
$dataTransacao = $dados["data_transacao"] ?? null;
$observacao = $dados["observacao"] ?? null;

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

if (!$tipo || !$accountId || !$descricao || !$valor || !$dataTransacao) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "tipo, account_id, descricao, valor e data_transacao são obrigatórios"
    ]);
    exit;
}

if (!in_array($tipo, ["receita", "despesa", "transferencia"])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Tipo inválido"]);
    exit;
}

if (!is_numeric($valor) || $valor <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Valor precisa ser maior que zero"]);
    exit;
}

$db = new Database();

$conta = $db->get("accounts", [
    "id" => $accountId,
    "usuario_id" => $usuarioId
]);

if (!$conta) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Conta não encontrada"]);
    exit;
}

if ($categoryId) {
    $categoria = $db->get("categories", [
        "id" => $categoryId,
        "usuario_id" => $usuarioId
    ]);

    if (!$categoria) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Categoria não encontrada"]);
        exit;
    }
}

$id = $db->insert("transactions", [
    "usuario_id" => $usuarioId,
    "account_id" => $accountId,
    "category_id" => $categoryId ?: null,
    "tipo" => $tipo,
    "descricao" => $descricao,
    "valor" => $valor,
    "data_transacao" => $dataTransacao,
    "observacao" => $observacao
]);

if (!$id) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao cadastrar transação"]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Transação cadastrada com sucesso",
    "data" => ["id" => $id]
]);
