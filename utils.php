<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/configs.php";

// JWT
function gerarJwt($usuarioId)
{
    $secret_key = CHAVE_JWT;

    $payload = [
        "iss" => "http://localhost/development/controle-financeiro-api/",
        "aud" => "http://localhost:3000",
        "sub" => $usuarioId,
        "iat" => time(),
        "exp" => time() + (60 * 15)
    ];

    $jwt = JWT::encode($payload, $secret_key, 'HS256');
    return $jwt;
}

function validarJwt()
{
    $headers = getallheaders();

    $authHeader = null;

    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
    } elseif (isset($headers['authorization'])) {
        $authHeader = $headers['authorization'];
    } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }

    if (!$authHeader) {
        throw new Exception("Token não fornecido");
    }

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        throw new Exception("Formato do token inválido");
    }

    $token = $matches[1];

    try {
        $decoded = JWT::decode($token, new Key(CHAVE_JWT, 'HS256'));

        // valida issuer
        if ($decoded->iss !== "http://localhost/development/controle-financeiro-api/") {
            throw new Exception("Issuer inválido");
        }

        return $decoded;
    } catch (\Firebase\JWT\ExpiredException $e) {
        throw new Exception("Token expirado");
    } catch (Exception $e) {
        throw new Exception("Token inválido");
    }
}

// Token Refresh
function gerarRefreshToken()
{
    return bin2hex(random_bytes(32)); // 64 caracteres
}

// Autenticar Token
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


function hashToken($token)
{
    return hash('sha256', $token);
}

// Logs
function logMsg($msg)
{
    $arquivo_log = DIR_LOGS . "log_" . date("Y-m-d") . ".txt";
    $data_hora = date("Y-m-d H:i:s");
    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'N/A';
    $rota = $_SERVER['REQUEST_URI'] ?? 'Rota não disponível';

    if (is_array($msg) || is_object($msg)) {
        $msg = print_r($msg, true);
    }

    $linha_log = "[$data_hora] [$metodo] [$rota] - $msg" . PHP_EOL;
    file_put_contents($arquivo_log, $linha_log, FILE_APPEND);
}
