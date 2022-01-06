<?php
session_start();

include '../../../bbdd/conectar.php';

if(isset($_POST['gasto'])) {
    
    $output = array();
    $output['gasto'] = array();
    $output['productos'] = array();
    
    $output['gasto'] = $db->get("gastos_privados",["id","usuario","familia","fecha","importe","tiene_ticket"],["AND"=>["id"=>$_POST['gasto'],"usuario"=>$_SESSION['usuario']]]);
    
    if($output['gasto']['fecha'] != '') {
        $aux = explode("-", $output['gasto']['fecha']);
        $output['gasto']['fecha'] = $aux[2]."/".$aux[1]."/".$aux[0];
    }
    
    if($output['gasto']['tiene_ticket'] == 'S') {
        $ticket = $db->select("tickets_privados",["producto","importe"],["gasto"=>$_POST['gasto']]);
        foreach ($ticket as $linea) {
            $output['productos'][] = array("producto"=>$linea['producto'], "t_importe"=>$linea['importe']);
        }
    }
    
    echo json_encode($output);
    
} else {
    echo "";
}
?>