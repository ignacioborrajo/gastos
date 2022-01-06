<?php
session_start();

include '../../../bbdd/conectar.php';

if(isset($_POST['ingreso'])) {
    
    $output = array();
    $output['ingreso'] = array();
    
    $output['ingreso'] = $db->get("ingresos",["id","usuario","familia","fecha","importe"],["AND"=>["id"=>$_POST['ingreso'],"usuario"=>$_SESSION['usuario']]]);
    
    if($output['ingreso']['fecha'] != '') {
        $aux = explode("-", $output['ingreso']['fecha']);
        $output['ingreso']['fecha'] = $aux[2]."/".$aux[1]."/".$aux[0];
    }
   
    echo json_encode($output);
    
} else {
    echo "";
}
?>