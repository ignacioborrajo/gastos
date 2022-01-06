<?php

include '../../../bbdd/conectar.php';

if(isset($_POST['importe'])) {
    
    $aux = explode("/",$_POST['fecha']);
    $fecha = $aux[2]."-".$aux[1]."-".$aux[0];
    
    $db->insert("ingresos",[
        
        "usuario"=>$_POST['usuario'],
        "familia"=>$_POST['familia'],
        "fecha"=>$fecha,
        "importe"=>$_POST['importe']
        
    ]);
    
    $last_id = $db->id();
    
    echo $last_id;
    
} else {
    echo 0;
}

?>