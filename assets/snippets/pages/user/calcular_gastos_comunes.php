<?php
session_start();

include '../../../bbdd/conectar.php';

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

if ($_POST['solo_mios'] == 'S') {
    $gasto_total = $db->sum("gastos", "importe", ["AND" => ["fecha[<>]" => [$inicio, $fin], "usuario" => $_SESSION['usuario']]]);
} else {
    $gasto_total = $db->sum("gastos", "importe", ["fecha[<>]" => [$inicio, $fin]]);
}

if ($_POST['solo_mios'] == 'S') {
    $total_este_mes = $db->sum("gastos", "importe", ["AND" => ["fecha[<>]" => [date("Y-m-01"), date("Y-m-t")], "usuario" => $_SESSION['usuario']]]);
} else {
    $total_este_mes = $db->sum("gastos", "importe", ["fecha[<>]" => [date("Y-m-01"), date("Y-m-t")]]);
}

$pie_data = array();
$familias = $db->select("familias", ["id", "nombre"], ["padre" => 0]);
foreach ($familias as $familia) {
    $subfamilias = $db->select("familias", "id", ["padre" => $familia['id']]);
    if ($_POST['solo_mios'] == 'S') {
        $gasto = $db->sum("gastos", "importe", ["AND" => ["fecha[<>]" => [$inicio, $fin], "usuario" => $_SESSION['usuario'], "familia" => $subfamilias]]);
    } else {
        if($subfamilias != null && count($subfamilias) > 0) {
            $gasto = $db->sum("gastos", "importe", ["AND" => ["fecha[<>]" => [$inicio, $fin], "familia" => $subfamilias]]);
        } else {
            $gasto = $db->sum("gastos", "importe", ["AND" => ["fecha[<>]" => [$inicio, $fin], "familia" => $familia['id']]]);
        }
    }
    $pie_data[] = array("name" => $familia['nombre'], "y" => round(floatval($gasto), 2), "drilldown" => $familia['id']);
}

$salida = array("total" => $gasto_total, "total_este_mes" => $total_este_mes, "pie_data" => $pie_data, "inicio" => $inicio, "fin" => $fin);

echo json_encode($salida);
