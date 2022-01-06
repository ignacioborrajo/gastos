<?php
session_start();

include '../../../bbdd/conectar.php';

$salida = "";

$ultimos_ingresos = $db->select("ingresos",
        [
            "[>]familias_ingresos(f)"=>["familia"=>"id"]
        ],
        [
            "ingresos.fecha",
            "ingresos.importe",
            "f.nombre(familia)"
        ],
        [
            "ingresos.usuario"=>$_SESSION['usuario'],
            "ORDER" => ["fecha" => "DESC"],
            "LIMIT" => 4
        ]
    );

foreach ($ultimos_ingresos as $gasto) {
    $salida .= '<div class="m-widget1__item">
                    <div class="row m-row--no-padding align-items-center">
                        <div class="col">
                            <h3 class="m-widget1__title">
                                '.$gasto['familia'].'
                            </h3>
                            <span class="m-widget1__desc">
                                '.$gasto['fecha'].'
                            </span>
                        </div>
                        <div class="col m--align-right">
                            <span class="m-widget1__number m--font-danger">
                                '.$gasto['importe'].'&euro;
                            </span>
                        </div>
                    </div>
                </div>';
}

echo $salida;

?>