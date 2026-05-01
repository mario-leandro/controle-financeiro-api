<?php

$dados = $_REQUEST_DATA ?? [];

if (empty($dados['id']) || empty($dados['nome']) || empty($dados['email']) || !isset($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID, nome, email e senha são obrigatórios"]);
    logMsg("Dados incompletos para atualização de usuário: " . json_encode($dados));
    exit();
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
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Usuário não encontrado"]);
        logMsg("Tentativa de atualização de usuário inexistente com ID: " . $id);
        exit;
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

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Usuário atualizado com sucesso']);
    logMsg("Usuário atualizado com ID: " . $id);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erro ao atualizar usuário: " . $e->getMessage()]);
    logMsg("Erro ao atualizar usuário: " . $e->getMessage());
}
