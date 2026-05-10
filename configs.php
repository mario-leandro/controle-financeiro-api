<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

$db_host = $_ENV["DB_HOST"];
$db_user = $_ENV["DB_USER"];
$db_pass = $_ENV["DB_PASS"];
$db_name = $_ENV["DB_NAME"];
$chave_jwt = $_ENV["CHAVE_JWT"];
$jwt_iss = $_ENV["JWT_ISS"];
$jwt_aud = $_ENV["JWT_AUD"];

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);

define("DIR_LOGS", __DIR__ . "/logs/");

define("CHAVE_JWT", $chave_jwt);
define("JWT_ISS", $jwt_iss);
define("JWT_AUD", $jwt_aud);

function pathRoutes($type, $action)
{
    $routes = array(
        "auth" => array(
            "login" => __DIR__ . "/api/auth/login.php",
            "register" => __DIR__ . "/api/auth/register.php",
            "logout" => __DIR__ . "/api/auth/logout.php",
            "refresh_token" => __DIR__ . "/api/refresh/refreshToken.php",
            "me" => __DIR__ . "/api/auth/me.php"
        ),
        "transactions" => [
            "create" => __DIR__ . "/api/transacoes/adicionarTransacao.php",
            "list" => __DIR__ . "/api/transacoes/listarTransacoes.php",
            "delete" => __DIR__ . "/api/transacoes/delete.php"
        ],
        "check_users" => array(
            // "update_user" => __DIR__ . "/api/usuarios/atualizarUsuario.php",
            "list_users" => __DIR__ . "/api/usuarios/listarUsuarios.php",
            // "delete_user" => __DIR__ . "/api/usuarios/excluirUsuario.php"
        ),
        "accounts" => [
            "create" => __DIR__ . "/api/contas/adicionarConta.php",
            "list" => __DIR__ . "/api/contas/listarContas.php"
        ],
        "categories" => [
            "create" => __DIR__ . "/api/categorias/adicionarCategoria.php",
            "list" => __DIR__ . "/api/categorias/listarCategorias.php"
        ],
    );

    if (isset($routes[$type][$action])) {
        return $routes[$type][$action];
    }

    return null;
}
