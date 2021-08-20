<!DOCTYPE html>
<html>
<head>

<style>
    h2{
        text-align: center;
    }
    .rec{
        padding: 0px 30px 0px 60px;
    }
    .recc{
        padding: 0px 30px 0px 90px;
    }
    
    table {
        border: 1px;
        width:100%;
    }
    .table {
        width: 90%;
        padding: 10px 10px 10px 10px;
    }
    
    tr{
        border: 10px;
        text-align: center;
    }
    .conteiner{
        border: 1px;
        text-align: center;
    }
    .cont{
        display: block;
        width: 100%;        
    }
   
    
</style>
</head>
<body>
<?php
require_once 'config.php';
$arrCompra = array();
$intId = $_GET["id"];
$var_consulta= "SELECT
                p.id, p.nombre, dv.id idv, dv.id_status, dv.id_user, dv.precio_uni, dv.cantidad
                FROM travel p
                JOIN detalle_venta dv
                ON dv.id_producto=p.id
                WHERE dv.id = '{$intId}' ";
$qTMP = mysqli_query($link, $var_consulta);
while ( $rTMP = mysqli_fetch_assoc($qTMP) ){
    $arrCompra[$rTMP["id"]]["id"] = $rTMP["id"];
    $arrCompra[$rTMP["id"]]["nombre"] = $rTMP["nombre"];
    $arrCompra[$rTMP["id"]]["idv"] = $rTMP["idv"];
    $arrCompra[$rTMP["id"]]["id_status"] = $rTMP["id_status"];
    $arrCompra[$rTMP["id"]]["id_user"] = $rTMP["id_user"];
    $arrCompra[$rTMP["id"]]["precio"] = $rTMP["precio_uni"];
    $arrCompra[$rTMP["id"]]["cantidad"] = $rTMP["cantidad"];

}

mysqli_close($link);

?>
    <div class="cont">
        <div class="rec"><h1>RECERVACION</h1></div>
        <table class="table table-lithg table-bordered table-hover">
        <tbody>
        <thead>
            <tr>
                <th width="40%" class="text-center">COMPRA</th>
                <th width="40%" class="text-center">NOMBRE</th>
                <th width="15%" class="text-center">PRECIO</th>
                <th width="15%" class="text-center">CANTIDAD</th>
                <th width="15%" class="text-center">ESTATUS</th>
            </tr>
        </thead>
        
        <?php if ( is_array($arrCompra) && ( count($arrCompra)>0 ) ){
                        reset($arrCompra);
                        foreach( $arrCompra as $rTMP['key'] => $rTMP['value'] ){ ?>
            <tr>
                <td width="40%" class="text-center"><?php echo  $rTMP["value"]['idv']; ?></td>
                <td width="40%" class="text-center"><?php echo  $rTMP["value"]['nombre']; ?></td>
                <td width="15%" class="text-center"><?php echo  $rTMP["value"]['precio']; ?></td>
                <td width="10%" class="text-center"><?php echo  $rTMP["value"]['cantidad']; ?></td>
                <td width="10%" class="text-center "><?php echo  $rTMP["value"]['id_status']; ?> </td>

            </tr>

            <?PHP
                }
            }
            ?> 
        </tbody>
        </table>
        <div class="recc"><h5>Presentar el dia del evento.</h5></div>
        
            <h6>______________________________Recortar______________________________</h6>
    </div>
</body>
</html>