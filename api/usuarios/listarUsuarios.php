<?php

include_once __DIR__ . "/../common.php";

headers();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    logMsg("Método não permitido em listarUsuarios.php: " . $_SERVER['REQUEST_METHOD']);
    exit;
}

$id_usuario = $_GET['id'] ?? null;
$token = $_GET['token'];
$db_connection = null;

function listarTodosUsuarios()
{
    global $db_connection;

    $db_connection = new Database();
    $usuarios = $db_connection->get("usuarios");

    return $usuarios;
}

function buscarUsuarioPorId($id)
{
    global $db_connection;

    $db_connection = new Database();
    $usuario = $db_connection->get("usuarios", ["id" => $id]);

    return $usuario ? $usuario[0] : null;
}

try {
    if ($id_usuario) {
        $usuario = buscarUsuarioPorId($id_usuario);
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
