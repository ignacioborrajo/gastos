<?php

include '../../../bbdd/conectar.php';

if(isset($_POST['familia']) && isset($_POST['usuario'])) {
    
    $db->delete("familias_privadas",["AND"=>["id"=>$_POST['familia'],"usuario"=>$_POST['usuario']]]);
    
    echo json_encode(array("resultado"=>true));
    
} else {
    echo json_encode(array("resultado"=>false));
}

?>