<?php 
require 'main_app/main.php';
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>DISCOLSA_DT</title>
        <link rel="stylesheet" href="css/main.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="images/discolsa.ico">
        <?php
         load_header();
        ?>
    </head>
    <body>
        <?php 
        if( isset($_REQUEST['error']) ){
            ?>
            <div class="error">
                <span class="alert alert-danger">Datos de Ingreso no validos, intente de nuevo
                </span>
            </div>
            <?php
        }
        if( isset($_SESSION['usuario']) && $_SESSION['usuario'] != ''  ){
            header('Location:formulario.php');
        }
        ?>       
<div class="container">
    <div class="row">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-signin my-5">
          <div class="card-body">
             <div class="form-label-group">
                   <h5 class="card-title text-center alert-info"><a href="../index.php">ELEGIR BASE DE DATOS</a></h5>
              </div>
            <div class="imgcontainer">
            <img src="images/discolsa.jpg" alt="Avatar" class="avatar">
          </div>
            <h5 class="card-title text-center">ORDENES DE COMPRA 2.0</h5>
            <h5 class="card-title text-center">PRODUCCION</h5>
            <form class="formlg form-signin" action="login.php" id="formlg" method="post" >
             
              <div class="form-label-group">
               <label for="usuariolg">User Name</label>
                <input type="text" id="usuariolg" name="usuariolg" class="form-control form-control-lg" placeholder="User Name" style="text-transform:uppercase;" required>
              </div>

              <div class="form-label-group">
               <label for="passlg">Password</label>
                <input type="password" id="passlg" name="passlg" class="form-control form-control-lg" placeholder="Password" required>
              </div>
              <button class="botonlg btn btn-lg btn-primary btn-block text-uppercase" type="submit">Sign in</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>


<style>
    :root {
  --input-padding-x: 1.5rem;
  --input-padding-y: .75rem;
}

body {
  background: #007bff;
  background: linear-gradient(to right, #0062E6, #33AEFF);
}

.card-signin {
  border: 0;
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
}

.card-signin .card-title {
  margin-bottom: 2rem;
  font-weight: 300;
  font-size: 1.5rem;
}

.card-signin .card-body {
  padding: 2rem;
}

.form-signin {
  width: 100%;
}

.form-signin .btn {
  font-size: 80%;
  border-radius: 5rem;
  letter-spacing: .1rem;
  font-weight: bold;
  padding: 1rem;
  transition: all 0.2s;
}

.form-label-group {
  position: relative;
  margin-bottom: 1rem;
}

.form-label-group input {
  height: auto;
  border-radius: 2rem;
}

.form-label-group>input,
.form-label-group>label {
  padding: var(--input-padding-y) var(--input-padding-x);
}

.form-label-group>label {
  position: absolute;
  top: 0;
  left: 0;
  display: block;
  width: 100%;
  margin-bottom: 0;
  /* Override default `<label>` margin */
  line-height: 1.5;
  color: #495057;
  border: 1px solid transparent;
  border-radius: .25rem;
  transition: all .1s ease-in-out;
}

.form-label-group input::-webkit-input-placeholder {
  color: transparent;
}

.form-label-group input:-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-ms-input-placeholder {
  color: transparent;
}

.form-label-group input::-moz-placeholder {
  color: transparent;
}

.form-label-group input::placeholder {
  color: transparent;
}

.form-label-group input:not(:placeholder-shown) {
  padding-top: calc(var(--input-padding-y) + var(--input-padding-y) * (2 / 3));
  padding-bottom: calc(var(--input-padding-y) / 3);
}

.form-label-group input:not(:placeholder-shown)~label {
  padding-top: calc(var(--input-padding-y) / 3);
  padding-bottom: calc(var(--input-padding-y) / 3);
  font-size: 12px;
  color: #777;
}

.btn-google {
  color: white;
  background-color: #ea4335;
}

.btn-facebook {
  color: white;
  background-color: #3b5998;
}
</style>