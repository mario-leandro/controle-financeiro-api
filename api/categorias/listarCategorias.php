<?php

$data = $GLOBALS["REQUEST_DATA"] ?? [];
$usuarioId = $GLOBALS["usuario_id"] ?? null;

$tipo = $data["tipo"] ?? null;

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

$filtros = [
    "usuario_id" => $usuarioId
];

if ($tipo) {
    $filtros["tipo"] = $tipo;
}

$db = new DataBase();

$categorias = $db->get("categories", $filtros);

echo json_encode([
    "success" => true,
    "message" => "Categorias listadas com sucesso",
    "data" => $categorias
]);