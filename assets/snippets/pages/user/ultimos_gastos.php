<?php

include '../../../bbdd/conectar.php';

$salida = "";

$ultimos_gastos = $db->select(
    "gastos",
    [
        "[>]usuarios(u)" => ["usuario" => "id"],
        "[>]familias(f)" => ["familia" => "id"]
    ],
    [
        "fecha",
        "importe",
        "u.nombre(usuario)",
        "f.nombre(familia)"
    ],
    [
        "ORDER" => ["fecha" => "DESC"],
        "LIMIT" => 4
    ]
);
/*
foreach ($ultimos_gastos as $gasto) {
    $salida .= '<div class="col-xl-3 m-widget1">
                    <div class="m-widget1__item">
                        <div class="row m-row--no-padding align-items-center">
                            <div class="col">
                                <h3 class="m-widget1__title">
                                    '.$gasto['familia'].'
                                </h3>
                                <span class="m-widget1__desc">
                                    '.$gasto['usuario'].'<br>'.$gasto['fecha'].'
                                </span>
                            </div>
                            <div class="col m--align-right">
                                <span class="m-widget1__number m--font-danger">
                                    '.$gasto['importe'].'&euro;
                                </span>
                            </div>
                        </div>
                    </div>
                </div>';
}
*/
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
