<?php

include_once __DIR__ . "/../common.php";

headers();

try {
    $decoded = validarJwt();
    $novoToken = refreshJwt($decoded->sub);
    echo json_encode(["token" => $novoToken]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["error" => $e->getMessage()]);
}
