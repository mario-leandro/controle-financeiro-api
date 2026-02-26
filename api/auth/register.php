<?php

include_once __DIR__ . "/../common.php";

headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    logMsg("Requisição com método não permitido: " . $_SERVER['REQUEST_METHOD']);
    exit;
}

$dados = json_decode(file_get_contents("php://input"), true);

$nome = $dados['nome'];
$email = $dados['email'];
$senha = $dados['senha'];

if (empty($dados['email']) || empty($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["error" => "Email e senha são obrigatórios"]);
    logMsg("Tentativa de login com email ou senha vazios");
    exit;
}
