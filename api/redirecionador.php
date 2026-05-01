<?php

require_once __DIR__ . "/../config/common.php";

headers();

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Body inválido ou vazio"
    ]);
    exit;
}

$type = $input["type"] ?? null;
$action = $input["action"] ?? null;
$data = $input["data"] ?? [];

if (!$type || !$action) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Informe type e action"
    ]);
    exit;
}

global $routes;

if (!isset($routes[$type][$action])) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Rota não encontrada"
    ]);
    exit;
}

$_REQUEST_DATA = $data;

require_once $routes[$type][$action];
