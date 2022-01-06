<?php
session_start();

include '../../../bbdd/conectar.php';

if(isset($_POST['ingreso'])) {
    
    $db->delete("ingresos",["AND"=>["id"=>$_POST['ingreso'],"usuario"=>$_SESSION['usuario']]]);
    
    echo json_encode(array("resultado"=>true));
    
} else {
    echo json_encode(array("resultado"=>false));
}

?>