<?php
require 'main_app/main.php';

session_start();
unset($_SESSION['usuario']);
session_destroy();

header("Location:../index.php");
exit;

?>