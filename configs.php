<?php

define('DB_HOST', getenv("DB_HOST"));
define('DB_USER', getenv("DB_USER"));
define('DB_PASS', getenv("DB_PASS"));
define('DB_NAME', getenv("DB_NAME"));

define("DIR_LOGS", __DIR__ . "/logs/");

define("CHAVE_JWT", getenv("CHAVE_JWT"));

$routes = [
    "auth" => [
        "login" => __DIR__ . "./api/auth/login.php",
        "register" => __DIR__ . "./api/auth/register.php",
        "logout" => __DIR__ . "./api/auth/logout.php",
        "refresh_token" => __DIR__ . "./api/refresh/refreshToken.php"
    ],
    "transactions" => [
        "create" => __DIR__ . "./api/transactions/create.php",
        "list" => __DIR__ . "./api/transactions/list.php",
        "delete" => __DIR__ . "./api/transactions/delete.php"
    ],
    "check_users" => [
        "update_user" => __DIR__ . "./api/usuarios/atualizarUsuario.php",
        "list_users" => __DIR__ . "./api/usuarios/listarUsuarios.php",
        "delete_user" => __DIR__ . "./api/usuarios/excluirUsuario.php"
    ]
];
