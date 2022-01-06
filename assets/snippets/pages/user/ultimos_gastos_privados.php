<?php
session_start();

include '../../../bbdd/conectar.php';

$salida = "";

$ultimos_gastos = $db->select("gastos_privados",
        [
            "[>]familias_privadas(f)"=>["familia"=>"id"]
        ],
        [
            "gastos_privados.fecha",
            "gastos_privados.id",
            "gastos_privados.importe",
            "f.nombre(familia)"
        ],
        [
            "gastos_privados.usuario" => $_SESSION['usuario'],
            "ORDER" => ["id" => "DESC"],
            "LIMIT" => 4
        ]
    );

foreach ($ultimos_gastos as $gasto) {
    $salida .= '<div class="col-md-12 col-lg-6 col-xl-3">
                    <div class="m-widget24">
                        <div class="m-widget24__item">
                            <h4 class="m-widget24__title">
                                ' . $gasto['familia'] . '
                            </h4>
                            <br>
                            <span class="m-widget24__desc">
                                ' . $gasto['fecha'] . '
                            </span>
                            <span class="m-widget24__stats m--font-brand">
                                ' . $gasto['importe'] . '&euro;
                            </span>
                            <div class="m--space-10"></div>
                        </div>
                    </div>
                </div>';
}

echo $salida;
