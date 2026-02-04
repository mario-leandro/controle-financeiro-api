<?php

include_once __DIR__ . "/../commom.php";

headers();

$dados = json_decode(file_get_contents("php://input"), true);

if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha']) || empty($dados['confSenha'])) {
    responderErro(400, "Nome, email e senha são obrigatórios");
}

$nome = $dados['nome'];
$email = $dados['email'];
$senha = $dados['senha'];
$confSenha = $dados['confSenha'];

if ($senha !== $confSenha) {
    responderErro(400, "As senhas não coincidem");
    logMsg("Tentativa de criação de usuário com senhas não coincidentes para o email: " . $email);
}

$db_connection = null;

try {
    $db_connection = new Database();

    // Verificar se o email já está em uso
    $usuarioExistente = $db_connection->get("usuarios", ["email" => $email]);
    if ($usuarioExistente) {
        responderErro(409, "Email já está em uso");
        logMsg("Tentativa de criação de usuário com email já existente: " . $email);
    }

    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    // Inserir novo usuário
    $novoUsuario = [
        "nome" => $nome,
        "email" => $email,
        "senha" => $senhaHash,
        "criado_em" => date("Y-m-d H:i:s")
    ];

    $novoId = $db_connection->insert("usuarios", $novoUsuario);

    responderJson(201, ['id' => $novoId, 'message' => 'Usuário criado com sucesso']);
    logMsg("Novo usuário criado com ID: " . $novoId . " e email: " . $email);
} catch (Exception $e) {
    responderErro(500, "Erro ao criar usuário: " . $e->getMessage());
    logMsg("Erro ao criar usuário: " . $e->getMessage());
}
