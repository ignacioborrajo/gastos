<?php
session_start();

include '../../../bbdd/conectar.php';

if(isset($_POST['ingreso_id'])) {
    
    $aux = explode("/",$_POST['fecha']);
    $fecha = $aux[2]."-".$aux[1]."-".$aux[0];
    
    $db->update("ingresos",[
        
        "familia"=>$_POST['familia'],
        "fecha"=>$fecha,
        "importe"=>$_POST['importe']
        
    ],["AND"=>["id"=>$_POST['ingreso_id'],"usuario"=>$_SESSION['usuario']]]);
    
    echo $_POST['ingreso_id'];
    
} else {
    echo 0;
}

?>