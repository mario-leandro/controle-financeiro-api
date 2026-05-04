<?php

// define("DB_HOST", "192.168.18.72");
// define("DB_USER", "cf_user");
// define("DB_PASS", "iUeBYwEF78");
// define("DB_NAME", "CF_DB");
// define("CHAVE_JWT", "XEwlHisbC6dofW2jvKBEpmYUDcBy0et8wS4DBODQmDP");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

$db_host = $_ENV["DB_HOST"];
$db_user = $_ENV["DB_USER"];
$db_pass = $_ENV["DB_PASS"];
$db_name = $_ENV["DB_NAME"];
$chave_jwt = $_ENV["CHAVE_JWT"];

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);

define("DIR_LOGS", __DIR__ . "/logs/");

define("CHAVE_JWT", $chave_jwt);

function pathRoutes($type, $action)
{
    $routes = array(
        "auth" => array(
            "login" => __DIR__ . "/api/auth/login.php",
            "register" => __DIR__ . "/api/auth/register.php",
            "logout" => __DIR__ . "/api/auth/logout.php",
            "refresh_token" => __DIR__ . "/api/refresh/refreshToken.php"
        ),
        // "transactions" => [
        //     "create" => __DIR__ . "/api/transactions/create.php",
        //     "list" => __DIR__ . "/api/transactions/list.php",
        //     "delete" => __DIR__ . "/api/transactions/delete.php"
        // ],
        "check_users" => array(
            "update_user" => __DIR__ . "/api/usuarios/atualizarUsuario.php",
            "list_users" => __DIR__ . "/api/usuarios/listarUsuarios.php",
            "delete_user" => __DIR__ . "/api/usuarios/excluirUsuario.php"
        ),
    );

    if (isset($routes[$type][$action])) {
        return $routes[$type][$action];
    }

    return null;
}
