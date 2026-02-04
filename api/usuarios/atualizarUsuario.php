<?php

include_once __DIR__ . "/../commom.php";

headers();

$dados = json_decode(file_get_contents("php://input"), true);

if (empty($dados['id']) || empty($dados['nome']) || empty($dados['email']) || !isset($dados['senha'])) {
    responderErro(400, "ID, nome, email e senha são obrigatórios");
    logMsg("Dados incompletos para atualização de usuário: " . json_encode($dados));
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
        logMsg("Tentativa de atualização de usuário inexistente com ID: " . $id);
    }

    // Preparar dados para atualização
    $dadosAtualizacao = [
        "nome" => $nome,
        "email" => $email
    ];

    if (!empty($senha)) {
        // Hash da nova senha
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        $dadosAtualizacao["senha"] = $senhaHash;
    }

    // Atualizar usuário
    $db_connection->update("usuarios", $dadosAtualizacao, ["id" => $id]);

    responderJson(200, ['message' => 'Usuário atualizado com sucesso']);
    logMsg("Usuário atualizado com ID: " . $id);
} catch (Exception $e) {
    responderErro(500, "Erro ao atualizar usuário: " . $e->getMessage());
    logMsg("Erro ao atualizar usuário: " . $e->getMessage());
}