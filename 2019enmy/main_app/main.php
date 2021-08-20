<?php 
require 'main_app/conexion.php';

session_start();
    if(isset($_SESSION['tiempo']) ) {
        $inactivo = 240;
        $vida_session = time() - $_SESSION['tiempo'];

            if($vida_session > $inactivo)
            {
                session_unset();
                session_destroy();              
                header("Location:index.php");
                exit();
            }
    }
    $_SESSION['tiempo'] = time();



function load_header(){
    ?>
    <link rel="stylesheet" href="css/jquery.ui.css">
    <script src="https://code.jquery.com/jquery-3.1.0.js" integrity="sha256-slogkvB1K3VOkzAI8QITxV3VzpOnkeNVsKvtkYLMjfk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <?php
}
?>