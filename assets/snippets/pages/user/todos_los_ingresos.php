<?php
session_start();

include '../../../bbdd/conectar.php';

$todos_ingresos = $db->select("ingresos",
        [
            "[>]familias_ingresos(f)"=>["familia"=>"id"]
        ],
        [
            "ingresos.id",
            "ingresos.fecha",
            "ingresos.importe",
            "f.nombre(familia)"
        ],
        [
            "ingresos.usuario"=>$_SESSION['usuario'],
            "ORDER" => ["fecha" => "DESC"]
        ]
    );

echo json_encode($todos_ingresos);

?>