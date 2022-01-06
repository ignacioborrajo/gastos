<?php
session_start();

include '../../../bbdd/conectar.php';

$gasto_total = $db->sum("gastos_privados","importe",["usuario"=>$_SESSION['usuario']]);

$time = time();
$inicio = mktime("00","00","00",date("n",$time),"1",date("Y",$time));
$fin = mktime("23","59","59",date("n",$time),date("t",$time),date("Y",$time));
$total_este_mes = $db->sum("gastos_privados","importe",["AND"=>["fecha[<>]"=>[date("Y-m-d",$inicio),date("Y-m-d",$fin)],"usuario"=>$_SESSION['usuario']]]);

$pie_data = array();
$familias = $db->select("familias_privadas",["id","nombre"],["AND"=>["usuario"=>$_SESSION['usuario'],"padre"=>0]]);
foreach ($familias as $familia) {
    $subfamilias = $db->select("familias_privadas","id",["padre"=>$familia['id']]);
    $gasto = $db->sum("gastos_privados","importe",["AND"=>["usuario"=>$_SESSION['usuario'],"familia"=>$subfamilias]]);
    $pie_data[] = array("name"=>$familia['nombre'],"y"=>round($gasto, 2),"drilldown"=>$familia['id']);
}

if(isset($_POST['comunes']) && $_POST['comunes'] == 'S') {
    $gasto_comun = $db->sum("gastos","importe",["usuario"=>$_SESSION['usuario']]);
    $total_este_mes_comun = $db->sum("gastos","importe",["AND"=>["fecha[<>]"=>[date("Y-m-d",$inicio),date("Y-m-d",$fin)],"usuario"=>$_SESSION['usuario']]]);
    
    $gasto_total += $gasto_comun;
    $total_este_mes += $total_este_mes_comun;
    
    $pie_data[] = array("name"=>'Gastos Comunes',"y"=>round($gasto_comun, 2),"drilldown"=>0);
    
}

$salida = array("total"=>$gasto_total, "total_este_mes"=>$total_este_mes, "pie_data"=>$pie_data);

echo json_encode($salida);

?>