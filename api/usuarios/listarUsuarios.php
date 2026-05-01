<?php

$dados = $_REQUEST_DATA ?? [];

$token = $dados['token'];
$db_connection = null;

function listarTodosUsuarios()
{
    global $db_connection;

    $db_connection = new Database();
    $usuarios = $db_connection->get("usuarios");

    return $usuarios;
}

function buscarUsuarioPorToken($id)
{
    global $db_connection;

    $db_connection = new Database();
    $usuario = $db_connection->get("usuarios", ["id" => $id]);

    return $usuario ? $usuario[0] : null;
}

try {
    if ($token) {
        $usuario = buscarUsuarioPorToken($token);
        if ($usuario) {
            http_response_code(200);
            echo json_encode(["success" => true, "usuario" => $usuario]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Usuário não encontrado"]);
        }
    } else {
        $usuarios = listarTodosUsuarios();
        http_response_code(200);
        echo json_encode(["success" => true, "usuarios" => $usuarios]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao listar usuários: " . $e->getMessage()]);
    logMsg("Erro ao listar usuários: " . $e->getMessage());
}
