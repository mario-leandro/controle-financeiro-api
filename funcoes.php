<?php

function headers() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0); // Resposta vazia, apenas os cabeçalhos
    }
}

function responderJson($status = 200, $data) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function responderErro($status, $mensagem) {
    responderJson($status, [
        'error' => true, 
        'message' => $mensagem
    ]);
}

function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}
