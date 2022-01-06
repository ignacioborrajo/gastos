<?php
session_start();

include '../../../bbdd/conectar.php';

if(isset($_POST['gasto'])) {
    
    $db->delete("gastos_privados",["AND"=>["id"=>$_POST['gasto'],"usuario"=>$_SESSION['usuario']]]);
    
    echo json_encode(array("resultado"=>true));
    
} else {
    echo json_encode(array("resultado"=>false));
}

?>