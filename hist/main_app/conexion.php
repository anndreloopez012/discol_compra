<?php
 
function conectar(){ 
    //$host = 'localhost:C:\BDASCII.FDB '; 
    $host = '190.148.213.39/3050:C:\\sincaf\\sincafd\SINCAF.FDB';
    //$host = '190.148.213.39/3050:C:\\test\\sincafd\SINCAF.FDB';
    //$host = 'C:\\TEST\\sincad\SINCAF.FDB'; 
    $username = "SYSDBA"; 
    $password = "bawjdr"; 
    $dbh = ibase_connect( $host, $username, $password ,'UTF8' ) or die ("Acceso Denegado!"); 
}
?>
