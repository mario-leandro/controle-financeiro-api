<?php

require_once __DIR__ . "/common.php";

headers();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    logMsg("Método não permitido: " . $_SERVER['REQUEST_METHOD'] . " em " . $_SERVER['REQUEST_URI']);
    exit;
}
$dados = json_decode(file_get_contents("php://input"), true);

if (!$dados) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Body inválido ou vazio"
    ]);
    exit;
}

var_dump($dados);

$type = $dados["type"] ?? null;
$action = $dados["action"] ?? null;
$data = $dados["data"] ?? [];

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
