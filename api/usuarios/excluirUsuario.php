<?php

include_once __DIR__ . "/../commom.php";

headers();

$dados = json_decode(file_get_contents("php://input"), true);

if (empty($dados['id']) || empty($dados['nome']) || empty($dados['email']) || !isset($dados['senha'])) {
    responderErro(400, "ID, nome, email e senha são obrigatórios");
}

$id = $dados['id'];
$nome = $dados['nome'];
$email = $dados['email'];
$senha = $dados['senha'];

$db_connection = null;

try {
    $db_connection = new Database();

    // Verificar se o usuário existe
    $usuarioExistente = $db_connection->get("usuarios", ["id" => $id]);
    if (!$usuarioExistente) {
        responderErro(404, "Usuário não encontrado");
        logMsg("Tentativa de exclusão de usuário inexistente com ID: " . $id);
    }

    // Excluir usuário
    $db_connection->delete("usuarios", ["id" => $id]);

    responderJson(200, ['message' => 'Usuário excluído com sucesso']);
    logMsg("Usuário excluído com ID: " . $id);
} catch (Exception $e) {
    responderErro(500, "Erro ao excluir usuário: " . $e->getMessage());
    logMsg("Erro ao excluir usuário: " . $e->getMessage());
}
