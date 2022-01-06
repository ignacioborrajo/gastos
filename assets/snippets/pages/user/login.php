<?php

include '../../../bbdd/conectar.php';

if(isset($_POST['email']) && $_POST['email'] != '' && isset($_POST['password']) && $_POST['password'] != '') {
    
    $password = md5($_POST['password']);
    
    if($db->has("usuarios",["AND"=>["OR"=>["email"=>$_POST['email'],"user"=>$_POST['email']],"password"=>$password]])) {
        
        $usuario = $db->get("usuarios","id",["AND"=>["OR"=>["email"=>$_POST['email'],"user"=>$_POST['email']],"password"=>$password]]);
        
        session_start(); 
        $_SESSION['usuario'] = $usuario;
        
        echo 'OK';
        
    } else {
        echo 'ERROR';
    }
    
} else {
    echo 'ERROR';
}

?>