<?php
session_start();

include '../../../bbdd/conectar.php';

$pie_data = array("name"=>$_POST['nombre'], "id"=>$_POST['familia'], "data"=>array());

if(isset($_POST['familia']) && $_POST['familia'] > 0) {
    $familias = $db->select("familias_privadas",["id","nombre"],["AND"=>["usuario"=>$_SESSION['usuario'],"padre"=>$_POST['familia']]]);
    foreach ($familias as $familia) {
        $gasto = $db->sum("gastos_privados","importe",["AND"=>["usuario"=>$_SESSION['usuario'],"familia"=>$familia['id']]]);
        $pie_data["data"][] = array($familia['nombre'],round($gasto, 2));
    }  
}

echo json_encode($pie_data);

?>