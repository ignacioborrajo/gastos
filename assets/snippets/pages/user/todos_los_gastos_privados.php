<?php
session_start();

include '../../../bbdd/conectar.php';

$salida = "";

$todos_gastos = $db->select("gastos_privados",
        [
            "[>]familias_privadas(f)"=>["familia"=>"id"]
        ],
        [
            "gastos_privados.id",
            "gastos_privados.fecha",
            "gastos_privados.importe",
            "gastos_privados.tiene_ticket",
            "f.nombre(familia)"
        ],
        [
            "gastos_privados.usuario" => $_SESSION['usuario'],
            "ORDER" => ["fecha" => "DESC"]
        ]
    );

echo json_encode($todos_gastos);

?>