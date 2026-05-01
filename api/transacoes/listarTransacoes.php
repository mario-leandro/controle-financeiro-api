<?php

include_once __DIR__ . "/../common.php";
include_once __DIR__ . "/../middlewares/AuthMiddleware.php";

headers();

$usuarioId = autenticar();

echo json_encode([
    "mensagem" => "Usuário autenticado",
    "usuario_id" => $usuarioId
]);
