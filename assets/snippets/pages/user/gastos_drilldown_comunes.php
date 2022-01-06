<?php
session_start();

include '../../../bbdd/conectar.php';

$pie_data = array("name" => $_POST['nombre'], "id" => $_POST['familia'], "data" => array());

if ($_POST['rango'] == 'T') {
    if ($_POST['solo_mios'] == 'S') {
        $inicio = $db->min("gastos", "fecha", ["usuario" => $_SESSION['usuario']]);
        $fin = $db->max("gastos", "fecha", ["usuario" => $_SESSION['usuario']]);
    } else {
        $inicio = $db->min("gastos", "fecha");
        $fin = $db->max("gastos", "fecha");
    }
} else if ($_POST['rango'] == 'A') {
    $inicio = date("Y-01-01");
    $fin = date("Y-12-31");
} else if ($_POST['rango'] == 'M') {
    $inicio = date("Y-m-01");
    $fin = date("Y-m-t");
}

if (isset($_POST['familia']) && $_POST['familia'] > 0) {

    $familias = $db->select("familias", ["id", "nombre"], ["padre" => $_POST['familia']]);

    foreach ($familias as $familia) {
        if ($_POST['solo_mios'] == 'S') {
            $gasto = $db->sum("gastos", "importe", ["AND" => ["fecha[<>]" => [$inicio, $fin], "usuario" => $_SESSION['usuario'], "familia" => $familia['id']]]);
        } else {
            $gasto = $db->sum("gastos", "importe", ["AND" => ["fecha[<>]" => [$inicio, $fin], "familia" => $familia['id']]]);
        }
        $pie_data["data"][] = array($familia['nombre'], round($gasto, 2));
    }
}

echo json_encode($pie_data);
