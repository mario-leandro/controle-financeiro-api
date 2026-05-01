<?php

$db_host = getenv("DB_HOST");
$db_user = getenv("DB_USER");
$db_pass = getenv("DB_PASS");
$db_name = getenv("DB_NAME");

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);

define("DIR_LOGS", __DIR__ . "/logs/");

define("CHAVE_JWT", getenv("CHAVE_JWT"));

$routes = [
    "auth" => [
        "login" => __DIR__ . "./api/auth/login.php",
        "register" => __DIR__ . "./api/auth/register.php",
        "logout" => __DIR__ . "./api/auth/logout.php",
        "refresh_token" => __DIR__ . "./api/refresh/refreshToken.php"
    ],
    // "transactions" => [
    //     "create" => __DIR__ . "./api/transactions/create.php",
    //     "list" => __DIR__ . "./api/transactions/list.php",
    //     "delete" => __DIR__ . "./api/transactions/delete.php"
    // ],
    "check_users" => [
        "update_user" => __DIR__ . "./api/usuarios/atualizarUsuario.php",
        "list_users" => __DIR__ . "./api/usuarios/listarUsuarios.php",
        "delete_user" => __DIR__ . "./api/usuarios/excluirUsuario.php"
    ],
    "test" => [
        "check_success" => __DIR__ . "./testes/test_succeess.php"
    ]
];
