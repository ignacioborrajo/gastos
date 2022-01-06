<?php

include '../../../bbdd/conectar.php';

$options = array();

$coincidencias = $db->select("productos",["nombre"], ["ORDER" => ["nombre" => "ASC"]]);
foreach ($coincidencias as $coincidencia) {
    $options['options'][] = $coincidencia['nombre'];
}

echo json_encode($options);

?>