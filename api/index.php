<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/common.php";

// headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$type = $dados["type"];
$action = $dados["action"];
$data = $dados["data"] ?? [];

// var_dump($type);
// var_dump($action);

if (!$type || !$action) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Rota não encontrada"
    ]);
    exit;
}

$routes = pathRoutes($type, $action);

if (!$routes) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Rota não encontrada"
    ]);
    exit;
}

$GLOBALS["REQUEST_DATA"] = $data;
// var_dump($GLOBALS["REQUEST_DATA"] = $data);

require_once $routes;
