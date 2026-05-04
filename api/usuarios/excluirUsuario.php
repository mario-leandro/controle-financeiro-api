<?php

$dados = $GLOBALS["REQUEST_DATA"] ?? [];

if (empty($dados['id']) || empty($dados['nome']) || empty($dados['email']) || !isset($dados['senha'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "ID, nome, email e senha são obrigatórios para exclusão"]);
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
        echo json_encode([
            "success" => false,
            "error" =>
            logMsg("Tentativa de exclusão de usuário inexistente com ID: " . $id)
        ]);
        exit();
    }

    $db_connection->delete("usuarios", ["id" => $id]);

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => "Usuário excluído com sucesso"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erro ao excluir usuário: " . $e->getMessage()
    ]);
    logMsg("Erro ao excluir usuário: " . $e->getMessage());
}
