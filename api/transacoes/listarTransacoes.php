<?php

include_once __DIR__ . "/../common.php";
include_once __DIR__ . "/../middlewares/AuthMiddleware.php";

headers();

// 🔐 Proteção
$usuarioId = autenticar();

// agora você sabe quem é o usuário

echo json_encode([
    "mensagem" => "Usuário autenticado",
    "usuario_id" => $usuarioId
]);
