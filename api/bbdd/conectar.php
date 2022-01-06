<?php

require_once 'medoo.php';

use Medoo\Medoo;

// Initialize nachoynelly Neige2018
$gastos_db = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'gastos_nacho_nelly',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'Ruben2042',
    'charset' => 'utf8'
]);

?>