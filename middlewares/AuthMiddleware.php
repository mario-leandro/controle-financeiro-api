<?php

require_once __DIR__ . "/../utils.php";

function autenticar()
{
    try {
        $decoded = validarJwt();
        return $decoded->sub; // retorna usuario_id
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["error" => $e->getMessage()]);
        exit;
    }
}
