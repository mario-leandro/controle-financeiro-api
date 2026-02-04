<?php

include_once __DIR__ . "/../commom.php";

headers();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderErro(405, "Método não permitido");
    logMsg("Método não permitido em listarUsuarios.php: " . $_SERVER['REQUEST_METHOD']);
}

$id_usuario = $_GET['id'] ?? null;
$db_connection = null;

function listarTodosUsuarios() {
    global $db_connection;

    $db_connection = new Database();
    $usuarios = $db_connection->get("usuarios");

    return $usuarios;
}

function buscarUsuarioPorId($id) {
    global $db_connection;

    $db_connection = new Database();
    $usuario = $db_connection->get("usuarios", ["id" => $id]);

    return $usuario ? $usuario[0] : null;
}

try {
    if ($id_usuario) {
        $usuario = buscarUsuarioPorId($id_usuario);
        if ($usuario) {
            responderJson(200, $usuario);
        } else {
            responderErro(404, "Usuário não encontrado");
        }
    } else {
        $usuarios = listarTodosUsuarios();
        responderJson(200, $usuarios);
    }
} catch (Exception $e) {
    responderErro(500, "Erro ao listar usuários: " . $e->getMessage());
    logMsg("Erro ao listar usuários: " . $e->getMessage());
}