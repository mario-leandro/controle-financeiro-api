<?php

$data = $GLOBALS["REQUEST_DATA"] ?? [];
$usuarioId = $GLOBALS["usuario_id"] ?? null;

$nome = $data["nome"] ?? null;
$tipo = strtolower($data["tipo"]) ?? null;
$saldoInicial = $data["saldo_inicial"] ?? 0;
$limiteTotal = $data["limite_total"] ?? 0;
$diaFechamento = $data["dia_fechamento"] ?? null;
$diaVencimento = $data["dia_vencimento"] ?? null;

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

if (!$nome || !$tipo) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Nome e tipo são obrigatórios"]);
    exit;
}

$tiposPermitidos = ["conta_corrente", "poupanca", "carteira", "cartao", "investimento"];

if (!in_array($tipo, $tiposPermitidos)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Tipo de conta inválido"]);
    exit;
}

$db = new DataBase();

$id = $db->insert("accounts", [
    "usuario_id" => $usuarioId,
    "nome" => $nome,
    "tipo" => $tipo,
    "saldo_inicial" => $saldoInicial,
    "limite_total" => $limiteTotal,
    "dia_fechamento" => $diaFechamento ?: null,
    "dia_vencimento" => $diaVencimento ?: null,
    "ativa" => 1
]);

echo json_encode([
    "success" => true,
    "message" => "Conta cadastrada com sucesso",
    "data" => ["id" => $id]
]);