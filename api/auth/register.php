<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . "/../common.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    logMsg("Método não permitido: " . $_SERVER['REQUEST_METHOD'] . " em " . $_SERVER['REQUEST_URI']);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Nome, email e senha são obrigatórios"]);
    logMsg("Dados incompletos para registro: " . json_encode($dados));
    exit;
}

$db_connection = null;

try {
    $db_connection = new Database();

    $usuarioExistente = $db_connection->get("usuarios", ["email" => $dados['email']]);

    if ($usuarioExistente) {
        http_response_code(409);
        echo json_encode(["error" => "Email já cadastrado"]);
        logMsg("Email já cadastrado: " . $dados['email']);
        exit;
    }

    $senhaHash = password_hash($dados['senha'], PASSWORD_BCRYPT);

    $usuarioId = criarUsuario(
        $dados['nome'],
        $dados['email'],
        $senhaHash
    );

    http_response_code(201);
    echo json_encode([
        "success" => true,
        "usuario_id" => $usuarioId
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erro ao criar usuário: " . $e->getMessage()
    ]);
    logMsg("Erro ao criar usuário: " . $e->getMessage());
}
