<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../funcoes.php';
require_once __DIR__ . '/../configs.php';
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../Database.php';

headers();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
