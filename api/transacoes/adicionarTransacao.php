<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];
$usuarioId = $GLOBALS["usuario_id"] ?? null;

$tipo = $dados["tipo"];
$accountId = $dados["account_id"];
$categoryId = $dados["category_id"];
$descricao = $dados["descricao"];
$valor = $dados["valor"];
$metodoPagamento = $dados["metodo_pagamento"] ?? null;
$parcelas = $dados["parcelas"] ?? null;
$dataTransacao = $dados["data_transacao"];
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

if (!in_array($tipo, ["receita", "despesa"])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Tipo inválido"]);
    exit;
}

if (!is_numeric($valor) || $valor <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Valor precisa ser maior que zero"]);
    exit;
}

if ($metodoPagamento === "credito") {
    if (!isset($parcelas)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Parcelas é obrigatório"]);
        exit;
    }

    if (!is_numeric($parcelas) || $parcelas <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Parcelas inválidas"]);
        exit;
    }
}

try {
    $db_connection = new Database();

    $conta = $db_connection->get("accounts", [
        "id" => $accountId,
        "usuario_id" => $usuarioId
    ]);

    if (!$conta) {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Conta não encontrada"]);
        exit;
    }

    if ($categoryId) {
        $categoria = $db_connection->get("categories", [
            "id" => $categoryId,
            "usuario_id" => $usuarioId
        ]);

        if (!$categoria) {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Categoria não encontrada"]);
            exit;
        }
    }

    $id = $db_connection->insert("transactions", [
        "usuario_id" => $usuarioId,
        "account_id" => $accountId,
        "category_id" => $categoryId,
        "tipo" => $tipo,
        "descricao" => $descricao,
        "valor" => $valor,
        "metodo_pagamento" => $metodoPagamento,
        "parcelas" => $parcelas,
        "data_transacao" => $dataTransacao,
        "observacao" => $observacao
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Transação cadastrada com sucesso",
        "data" => ["id" => $id]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erro ao cadastrar transação"]);
    exit;
}
