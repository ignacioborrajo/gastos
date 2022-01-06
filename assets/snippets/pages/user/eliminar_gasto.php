<?php

include '../../../bbdd/conectar.php';

if(isset($_POST['gasto'])) {
    
    $db->delete("gastos",["id"=>$_POST['gasto']]);
    
    echo json_encode(array("resultado"=>true));
    
} else {
    echo json_encode(array("resultado"=>false));
}

?>