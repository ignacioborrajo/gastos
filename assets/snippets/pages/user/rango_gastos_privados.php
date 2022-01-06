<?php
session_start();

include '../../../bbdd/conectar.php';

$salida = array();
$categorias = array();

if(isset($_GET['rango'])) {
    
    $time = time();
    
    $inicio = "";
    $fin = "";
    
    if($_GET['rango'] == 'anual') {
        $inicio = mktime("00","00","00","1","1",date("Y",$time));
        $fin = mktime("23","59","59","12","31",date("Y",$time));
        $num_medidas = 5;
    } elseif($_GET['rango'] == 'mensual') {
        $inicio = mktime("00","00","00",date("n",$time),"1",date("Y",$time)); //Primer día del mes
        $fin = mktime("23","59","59",date("n",$time),date("t",$time),date("Y",$time)); //Último día del mes
        $num_medidas = 12;
    } elseif($_GET['rango'] == 'diario') {
        $inicio = mktime("00","00","00",date("n",$time),date("j",$time),date("Y",$time));
        $fin = mktime("23","59","59",date("n",$time),date("j",$time),date("Y",$time));
        $num_medidas = 30;
    }
    
}

if($_GET['detallado'] == 'S') $familias = $db->select("familias_privadas",["id","nombre","padre"]);
else $familias = $db->select("familias_privadas",["id","nombre","padre"],["padre"=>0]);

foreach($familias as $key=>$familia) {
    $data = array();
    
    if($_GET['detallado'] == 'S') {
        $subfamilia = array($familia['id']);
    } else {
        $subfamilia = $db->select("familias_privadas","id",["padre"=>$familia['id']]);
    }
    
    $i_inicio = $inicio;
    $i_fin = $fin;
    
    for($i=0;$i<$num_medidas;$i++) {
    
        if($key == 0) {
            
            //echo date("Y-m-d",$i_inicio)." -> ".date("Y-m-d",$i_fin)."<br>";
            
            $texto = '';
            if($_GET['rango'] == 'anual') {
                $texto = date("Y",$i_inicio);
            } elseif($_GET['rango'] == 'mensual') {
                $texto = date("F",$i_inicio);
            } elseif($_GET['rango'] == 'diario') {
                $texto = date("d/m/Y",$i_inicio);
            }
            
            $categorias[] = $texto;

        }

        $total = $db->sum("gastos_privados","importe",["AND"=>["familia"=>$subfamilia,"fecha[<>]"=>[date("Y-m-d",$i_inicio),date("Y-m-d",$i_fin)],"usuario"=>$_SESSION['usuario']]]);
        $data[] = round($total, 2);
        
        if($_GET['rango'] == 'anual') {
            $nuevo_ano = date("Y",$i_inicio) - 1;
            $i_inicio = mktime("00","00","00","1","1",$nuevo_ano);
            $i_fin = mktime("23","59","59","12","31",$nuevo_ano);
        } elseif($_GET['rango'] == 'mensual') {
            $nuevo_mes = date("n",$i_inicio) - 1;
            $nuevo_ano = date("Y",$i_inicio);

            if($nuevo_mes == 0) $mes_anterior = 12;

            $i_inicio = mktime("00","00","00",$nuevo_mes,"1",$nuevo_ano);
            $i_fin = mktime("00","00","00",$nuevo_mes,date("t",$i_inicio),$nuevo_ano);
        } elseif($_GET['rango'] == 'diario') {
            $i_inicio -= 24*60*60;
            $i_fin -= 24*60*60;
        }
    }
    
    $salida['datos'][] = array("name"=>$familia['nombre'],"data"=>$data);
    $salida['categorias'] = $categorias;
    
}

echo json_encode($salida);

?>