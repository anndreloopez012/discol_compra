<?php
require 'main_app/main.php';

function validarUsuario($usuario, $pass)  {  
    $v_result=0;  
    if (($pass<>'') && ($usuario<>'')) {  
        $connect = conectar(); // llama funcion de conectar  
        //define si existe usuario en DB.  
        $stmt ="select * from AXESO where USUARIO  = '$usuario' AND CLAVE = '$pass' "  ;  
        $query = ibase_prepare($stmt);  
        $v_query = ibase_execute($query);  
        $v_reg = ibase_fetch_row($v_query); //Verifica aquí si recibes los parámetros correctos 
        ibase_free_query($query); 
        if ( is_array($v_reg)  &&  count($v_reg) > 0){  
            $_SESSION['usuario'] = $usuario;  
            $v_result=1;
        }  
    }  
    return $v_result;  
}  

if( validarUsuario($_POST['usuariolg'],$_POST['passlg']) == 1 ){
    header('Location:formulario.php');
} 
else{
    header('Location:index.php?error=1');
}
   





?>