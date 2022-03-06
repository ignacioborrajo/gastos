<?php

require_once 'medoo.php';

use Medoo\Medoo;

// Initialize nacho
$db = new Medoo([
    'database_type' => 'mariadb',
    'database_name' => 'portal_gastos',
    'server' => 'db',
    'username' => 'root',
    'password' => 'Ruben2042',
    'charset' => 'utf8'
]);

?>
