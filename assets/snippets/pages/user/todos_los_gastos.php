<?php

include '../../../bbdd/conectar.php';

$salida = "";

$todos_gastos = $db->select("gastos",
        [
            "[>]usuarios(u)"=>["usuario"=>"id"],
            "[>]familias(f)"=>["familia"=>"id"]
        ],
        [
            "gastos.id",
            "gastos.fecha",
            "gastos.importe",
            "gastos.tiene_ticket",
            "u.nombre(usuario)",
            "f.nombre(familia)"
        ],
        [
            "ORDER" => ["fecha" => "DESC"]
        ]
    );

echo json_encode($todos_gastos);

?>