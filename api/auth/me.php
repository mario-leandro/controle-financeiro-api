<?php

include_once __DIR__ . "/../common.php";

headers();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    logMsg("Método não permitido: " . $_SERVER['REQUEST_METHOD'] . " em " . $_SERVER['REQUEST_URI']);
    exit;
}

try {
    $usuarioId = autenticar();

    $db_connection = new Database();

    $usuarios = $db_connection->get("usuarios", ["id" => $usuarioId], ["id", "nome", "email", "criado_em"]);
    $usuario = $usuarios[0] ?? null;

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado"]);
        logMsg("Usuário autenticado não encontrado: ID $usuarioId");
        exit;
    }

    echo json_encode([
        "success" => true,
        "usuario" => $usuario
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        "error" => "Não autorizado"
    ]);
    logMsg("Acesso não autorizado: " . $e->getMessage());
}
