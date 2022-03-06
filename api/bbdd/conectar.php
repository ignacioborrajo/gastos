<?php

require_once 'medoo.php';

use Medoo\Medoo;

// Initialize nachoynelly Neige2018
$gastos_db = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'portal_gastos',
    'server' => 'db',
    'username' => 'root',
    'password' => 'Ruben2042',
    'charset' => 'utf8'
]);

?>
