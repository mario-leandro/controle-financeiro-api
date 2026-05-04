<?php

include_once __DIR__ . "/../common.php";

$usuarioId = autenticar();

echo json_encode([
    "mensagem" => "Usuário autenticado",
    "usuario_id" => $usuarioId
]);
