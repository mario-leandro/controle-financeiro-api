<?php

require_once __DIR__ . "/common.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// var_dump($dados);

$type = $dados["type"] ?? null;
$action = $dados["action"] ?? null;
$data = $dados["data"] ?? [];

$routes = [
    "auth" => [
        "login" => __DIR__ . "/api/auth/login.php",
        "register" => __DIR__ . "/api/auth/register.php",
        "logout" => __DIR__ . "/api/auth/logout.php",
        "refresh_token" => __DIR__ . "/api/refresh/refreshToken.php"
    ],
    // "transactions" => [
    //     "create" => __DIR__ . "./api/transactions/create.php",
    //     "list" => __DIR__ . "./api/transactions/list.php",
    //     "delete" => __DIR__ . "./api/transactions/delete.php"
    // ],
    "check_users" => [
        "update_user" => __DIR__ . "/usuarios/atualizarUsuario.php",
        "list_users" => __DIR__ . "/usuarios/listarUsuarios.php",
        "delete_user" => __DIR__ . "/usuarios/excluirUsuario.php"
    ],
    "test" => [
        "check_success" => __DIR__ . "/testes/test_succeess.php"
    ]
];

if (!$type || !$action || !isset($routes[$type][$action])) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "Rota não encontrada"
    ]);
    exit;
}

$GLOBALS["REQUEST_DATA"] = $data;

require_once $routes[$type][$action];
