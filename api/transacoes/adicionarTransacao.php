<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];

$tipo = $dados["tipo"];
$categoria = $dados["categoria"];
$conta = $dados["conta"];
$descricao = $dados["descricao"];
$valor = $dados["valor"];
$data = $dados["data"];
$observacao = $dados["observacao"] ?? null;

if (!isset($tipo) || !isset($categoria) || !isset($conta) || !isset($descricao) || !isset($valor) || !isset($data)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Tipo, categoria, conta, descricao, valor e data são obrigatorios"
    ]);
    exit();
}

$db_connection = null;

try {
    $db_connection = new Database();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro interno"]);
}
