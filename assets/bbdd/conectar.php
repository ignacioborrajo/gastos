<?php

require_once 'medoo.php';

use Medoo\Medoo;

// Initialize nacho
$db = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'gastos_nacho_nelly',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'Ruben2042',
    'charset' => 'utf8'
]);

?>