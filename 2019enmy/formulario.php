<?php 
require 'main_app/main.php';

if( isset($_REQUEST['logout']) ){
    unset($_SESSION['usuario']);
    header('Location:index.php');
}

if ( isset($_GET["validaciones"]) && !empty($_GET["validaciones"]) ){
    
    $connect = conectar(); 

    if ( $_GET["validaciones"] == "proveedor" ){
        
        $strBusqueda = isset($_GET["term"]) ? utf8_decode(trim($_GET["term"])) : "";
        $strBusqueda = strtoupper($strBusqueda);
        
        $arrInfo = array();
        $stmt ="select nit, nombre, niu, moneda from provedores where UPPER(nombre) LIKE '%{$strBusqueda}%' "  ;  
        $query = ibase_prepare($stmt);  
        $v_query = ibase_execute($query);  
        while ( $rTMP = ibase_fetch_assoc($v_query) ){
            
            $arrInfo[$rTMP["NIU"]]["nit"] = trim($rTMP["NIT"]);
            $arrInfo[$rTMP["NIU"]]["nombre"] = trim($rTMP["NOMBRE"]);
            $arrInfo[$rTMP["NIU"]]["niu"] = trim($rTMP["NIU"]);
            $arrInfo[$rTMP["NIU"]]["moneda"] = trim($rTMP["MONEDA"]);
            
        }
        ibase_free_result($v_query);
        
        $result = array();
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            foreach ( $arrInfo  as $rTMP["key"] => $rTMP["value"] ){
                
                $arrTMP = array();
                $arrTMP["id"] = utf8_encode($rTMP["key"]);
                $arrTMP["value"] = utf8_encode($rTMP["key"]." - ".$rTMP["value"]["nombre"]);
                $arrTMP["info"] = utf8_encode($rTMP["value"]["nit"]." |-| ".$rTMP["value"]["nombre"]." |-| ".$rTMP["key"]." |-| ".$rTMP["value"]["moneda"]);
                $arrTMP["moneda"] = utf8_encode($rTMP["value"]["moneda"]);
                                                                                                                                 
                array_push($result, $arrTMP);
                
            }
        }
        
        print json_encode($result);
        
        die();
        
        
    }

    elseif ( $_GET["validaciones"] == "codigo" ){
        
        $strBusqueda = isset($_GET["term"]) ? utf8_decode(trim($_GET["term"])) : "";
        $strBusqueda = strtoupper($strBusqueda);
        
        $strFecha = isset($_GET["fecha"]) ? trim($_GET["fecha"]) : "";
        
        $arrInfo = array();
        $stmt ="select codigo, descrip, niu, CTRLEXIS from tipcomb where UPPER(codigo) LIKE '%{$strBusqueda}%' AND CTRLEXIS > 0 "  ;  
        $query = ibase_prepare($stmt);  
        $v_query = ibase_execute($query);  
        while ( $rTMP = ibase_fetch_assoc($v_query) ){
            
            $arrInfo[$rTMP["NIU"]]["id"] = $rTMP["NIU"];
            $arrInfo[$rTMP["NIU"]]["codigo"] = trim($rTMP["CODIGO"]);
            $arrInfo[$rTMP["NIU"]]["descripcion"] = trim($rTMP["DESCRIP"]);
            $arrInfo[$rTMP["NIU"]]["ctrlexis"] = trim($rTMP["CTRLEXIS"]);
            
        }
        ibase_free_result($v_query);
        
            
        $result = array();
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            foreach ( $arrInfo  as $rTMP["key"] => $rTMP["value"] ){
                
                $sinVAlorQ = 0;
                $sinVAlorD = 0;
                $boolFirstTime = true;
                $stmt = "SELECT MONEDA, PRECIO FROM PRECOMB WHERE DESDE <= '{$strFecha}' AND HASTA >= '{$strFecha}' AND NIU_COMBUSTIBLES = '{$rTMP["key"]}'";
                $query = ibase_prepare($stmt);  
                $v_query = ibase_execute($query);  
                while ( $arrTMP = ibase_fetch_assoc($v_query) ){

                    if ( $boolFirstTime ){
                        $sinVAlorQ = ($arrTMP["MONEDA"] == 1) ? floatval($arrTMP["PRECIO"]) : 0;
                        $sinVAlorD = ($arrTMP["MONEDA"] == 2) ? floatval($arrTMP["PRECIO"]) : 0;
                    }

                    $boolFirstTime = false;

                }
                ibase_free_result($v_query);
                
                $arrTMP = array();
                $arrTMP["id"] = utf8_encode($rTMP["key"]);
                $arrTMP["value"] = utf8_encode($rTMP["key"]." |-| ".$rTMP["value"]["codigo"]." |-| ".$rTMP["value"]["descripcion"]);
                $arrTMP["descripcion"] = utf8_encode($rTMP["value"]["descripcion"]);
                $arrTMP["info"] = utf8_encode($rTMP["value"]["codigo"]." |-| ".$rTMP["value"]["descripcion"]." |-| ".$rTMP["value"]["id"]." |-| ".$rTMP["value"]["ctrlexis"]);
                $arrTMP["valor_q"] = utf8_encode($sinVAlorQ);
                $arrTMP["valor_d"] = utf8_encode($sinVAlorD);
                $arrTMP["control_valor"] = utf8_encode((( !$sinVAlorQ && !$sinVAlorD ) ? "N" : "Y"));
                                                                                                                                 
                array_push($result, $arrTMP);
                
            }
        }
        
        print json_encode($result);
        
        die();
        
        
    }
    else if ( $_GET["validaciones"] == "tasa_cambio" ){
        
        $strFecha = isset($_GET["fecha"]) ? trim($_GET["fecha"]) : "";
        
        $boolFirstTime = true;
        $sinTasa = 0;
        $stmt = "SELECT TASA FROM TASAS WHERE FECHA = '{$strFecha}' ORDER BY niu DESC";
        $query = ibase_prepare($stmt);  
        $v_query = ibase_execute($query);  
        while ( $rTMP = ibase_fetch_assoc($v_query) ){

            if ( $boolFirstTime ){
                $sinTasa = floatval($rTMP["TASA"]);
            }

            $boolFirstTime = false;

        }
        ibase_free_result($v_query);
        
        print $sinTasa;
        
        die();
        
        
    }
    else if ( $_GET["validaciones"] == "precios_galones" ){
        
        
        $strFecha = isset($_GET["fecha"]) ? trim($_GET["fecha"]) : "";
        $strCodigo = isset($_GET["codigo"]) ? trim($_GET["codigo"]) : "";
        
        
        
        
        $strResultado = "0|-|0";
        
        
        if ( !empty($strFecha) && !empty($strCodigo) ){
            
            $arrExplode = explode("|-|", $strCodigo);
            $strCodigo = trim($arrExplode[0]);
            $strCodigo = intval($strCodigo);
            
            
            $boolFirstTime = true;
            $stmt = "SELECT DESCRIP, PRECIO, MONEDA FROM PRECOMB WHERE DESDE >= '{$strFecha}' AND HASTA <= '{$strFecha}' AND NIU = '{$strCodigo}'  ORDER BY DESCRIP DESC";

            print $stmt;
            
            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);  
            while ( $rTMP = ibase_fetch_assoc($v_query) ){

                if ( $boolFirstTime ){

                    print_r($rTMP["MONEDA"]);

                    $strResultado = floatval($rTMP["PRECIO"])."|-|".$rTMP["MONEDA"];
                }

                $boolFirstTime = false;

            }
            ibase_free_result($v_query);
            
        }
        
            
        
        print $strResultado;
        
        die();
        
        
    }
    
    elseif ( $_GET["validaciones"] == "eliminar" ){
        
        $intNiU = isset($_GET["key"]) ? intval($_GET["key"]) : 0;
        
        if ( intval($intNiU) ){
            
            
            $stmt = "DELETE FROM PRODUCTO2 WHERE PROD_NIU = '{$intNiU}'";
            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);
            
        }
        
        die();
        
    }
    
    elseif ( $_GET["validaciones"] == "codigo_precio" ){
        
        $strBusqueda = isset($_GET["term"]) ? utf8_decode(trim($_GET["term"])) : "";
        $strBusqueda = strtoupper($strBusqueda);
        
        $arrInfo = array();
        $stmt ="SELECT CODIGO, DESCRIP, NIU, CTRLEXIS FROM TIPCOMB where UPPER(CODIGO) LIKE '%{$strBusqueda}%' " ;  
        $query = ibase_prepare($stmt);  
        $v_query = ibase_execute($query);  
        while ( $rTMP = ibase_fetch_assoc($v_query) ){
            
            $arrInfo[$rTMP["NIU"]]["id"] = $rTMP["NIU"];
            $arrInfo[$rTMP["NIU"]]["codigo"] = trim($rTMP["CODIGO"]);
            $arrInfo[$rTMP["NIU"]]["descrip"] = trim($rTMP["DESCRIP"]);

            
            
        }
        ibase_free_result($v_query);
        
        $result = array();
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            while ( $rTMP = each($arrInfo) ){
                
                $arrTMP = array();
                $arrTMP["id"] = utf8_encode($rTMP["key"]);
                $arrTMP["value"] = utf8_encode($rTMP["value"]["codigo"]);
                $arrTMP["descrip"] = utf8_encode($rTMP["value"]["descrip"]);
                $arrTMP["niu"] = utf8_encode($rTMP["key"]);
                                                                                                                                 
                array_push($result, $arrTMP);
                
            }
        }
        
        print json_encode($result);
        
    }
    
    elseif ( $_GET["validaciones"] == 'checkexist' ){
        $strFecha1 = isset($_POST["desde"]) ? trim($_POST["desde"]) : "";
        $strFecha2 = isset($_POST["hasta"]) ? trim($_POST["hasta"]) : "";
        $strFechaUno = date ("d/m/Y", strtotime($strFecha1));
        $strFechaDos = date ("d/m/Y", strtotime($strFecha2));
        $sinPrecio = isset($_POST["precio"]) ? trim($_POST["precio"]) : '';
        $strCodigo = isset($_POST["codigopre"]) ? trim($_POST["codigopre"]) : "";
        $strFiltroFechas = '';
        $strFiltroPrecio = '';
        
        if( $strFechaUno != '' ){
            $strFiltroFechas .= "(PC.DESDE >= '{$strFechaDos}' AND  '{$strFechaDos}' <= PC.HASTA   )";
        }
        if( $strFechaDos != '' ){
            if( $strFiltroFechas != ''){
                $strFiltroFechas .= ' OR ';    
            }
            $strFiltroFechas .= " (PC.DESDE >= '{$strFechaUno}' AND  '{$strFechaUno}' <= PC.HASTA   ) ";
        }
        if( $strFiltroFechas != ''){
            $strFiltroFechas = " AND ( {$strFiltroFechas} ) ";
        }
        
        if( $sinPrecio != '' ){
            $strFiltroPrecio = " AND PC.PRECIO = '{$sinPrecio}'";
        }
        
        $stmt ="SELECT PC.NIU
                FROM PRECOMB as PC
                WHERE  PC.CODIGO = '{$strCodigo}'
                {$strFiltroFechas}
                {$strFiltroPrecio}
                " ;  
        //print $stmt;
            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);     
            $intCount = 0;
            while($rTMP = ibase_fetch_assoc($v_query)){
                $intCount++;
            }
        header('Content-Type: application/json');
        print json_encode(array( 'exist'=> ( ($intCount > 0)?1:0 )   ));  
        
    }
    
    elseif ( $_GET["validaciones"] == 'fechexistone' ){
        $strFecha1 = isset($_POST["desde"]) ? trim($_POST["desde"]) : "";
        $strFecha2 = isset($_POST["hasta"]) ? trim($_POST["hasta"]) : "";
        $strFechaUno = date ("Y/m/d", strtotime($strFecha1));
        $strFechaDos = date ("Y/m/d", strtotime($strFecha2));
        $strCodigo = isset($_POST["codigopre"]) ? trim($_POST["codigopre"]) : "";
        $stmt ="SELECT PC.NIU
                FROM PRECOMB as PC
                WHERE  
                     PC.DESDE >= '$strFechaUno'
                AND  PC.HASTA <= '$strFechaUno'   
                AND  PC.CODIGO = '{$strCodigo}'
                
                " ;  

            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);     
            $intCount = 0;
            while($rTMP = ibase_fetch_assoc($v_query)){
                $intCount++;
            }
        header('Content-Type: application/json');
        print json_encode(array( 'exist'=> $intCount));  

    }
    
    die();
    
}
else if ( isset($_GET["busqueda_registro"]) && ( $_GET["busqueda_registro"] == "true" ) ){
    $connect = conectar();
    $strFecha1 = isset($_POST["fecha_uno"]) ? trim($_POST["fecha_uno"]) : "";
    $strFecha2 = isset($_POST["fecha_dos"]) ? trim($_POST["fecha_dos"]) : "";
    $strBuqueda = isset($_POST["busqueda"]) ? trim($_POST["busqueda"]) : "";
    $strFechaUno = date ("Y/m/d", strtotime($strFecha1));
    $strFechaDos = date ("Y/m/d", strtotime($strFecha2));
    
    //print "<pre>";
    //print_r($strFecha1);
    //print "</pre><br><br>";
    
    //print "<pre>";
    //print_r($strFecha2);
    //print "</pre><br><br>";
    
    //print "<pre>";
    //print_r($strBuqueda);
    //print "</pre><br><br>";
    
    $strFilter = "";
    if ( !empty($strBuqueda) ){
        $strFilter = " AND ( UPPER(CODIGO) LIKE '%{$strBuqueda}%' OR UPPER(PROVEEDOR) LIKE '%{$strBuqueda}%' OR UPPER(NUMORDEN) LIKE '%{$strBuqueda}%' ) ";
        
        
    }
    
    $arrInfo = array();
    $stmt ="select FIRST(100) PROD_NIU NIU, CODIGO, FECHATRAN, NUMORDEN, 
                   PROVEEDOR, VALORQ, CANTSOLI, DESPACHO, EXISTENC
            from PRODUCTO2
            WHERE   PROVEEDOR IS NOT NULL
            AND  FECHATRAN >= '{$strFechaUno}'
            AND  FECHATRAN <= '{$strFechaDos}'
            {$strFilter}
            ORDER BY FECHATRAN desc"  ;  
    
//print "<pre>";
    //print_r($stmt);
    //print "</pre><br><br>";
    $query = ibase_prepare($stmt);  
    $v_query = ibase_execute($query); 
    $intCount = 0;
    while($rTMP = ibase_fetch_assoc($v_query)){
        $arrInfo[$intCount]["NIU"] = $rTMP["NIU"];
        $arrInfo[$intCount]["CODIGO"] = $rTMP["CODIGO"];
        $arrInfo[$intCount]["FECHATRAN"] = $rTMP["FECHATRAN"];
        $arrInfo[$intCount]["NUMORDEN"] = $rTMP["NUMORDEN"];
        $arrInfo[$intCount]["PROVEEDOR"] = $rTMP["PROVEEDOR"];
        $arrInfo[$intCount]["VALORQ"] = $rTMP["VALORQ"];
        $arrInfo[$intCount]["CANTSOLI"] = $rTMP["CANTSOLI"];
        $arrInfo[$intCount]["DESPACHO"] = $rTMP["DESPACHO"];
        $arrInfo[$intCount]["EXISTENC"] = $rTMP["EXISTENC"];
        $intCount++;
    }
    
    ////print "<pre>";
    //print_r($arrInfo);
    //print "</pre><br><br>";
    ?>
    <div class="col-md-12">
        
        <?php
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            foreach ( $arrInfo as $key => $value ){
                $intId = isset($value['NIU'])?$value['NIU']:0;
              ?>
                <div id="DivContenedor_<?php print $intId; ?>" class="panel panel-primary">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" href="#collapse1_<?php print $intId; ?>"><?php echo $value['FECHATRAN']; ?> - <?php echo $value['NUMORDEN']; ?></a> 
                    </h4>
                  </div>
                  <div id="collapse1_<?php print $intId; ?>" class="panel-collapse collapse alert-info">
                  <div class="panel-body">
                  </div>
                        <table style="width:100%">
                            <tr>
                                <td><b>No. Orden: </b><?php echo $value['NUMORDEN']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Proveedor:  </b><?php echo $value['PROVEEDOR']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Monto:  </b><?php echo $value['VALORQ']; ?></td>
                           </tr>
                           <tr>
                                <td><b>Cantidad Gas: </b><?php echo $value['CANTSOLI']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Despacho:  </b><?php echo $value['DESPACHO']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Existencia:  </b><?php echo $value['EXISTENC']; ?></td>
                           </tr><br>
                        </table>
                        <br>
                    </div>
                </div>
            <?php
            }
            
        }
    else{
        ?>
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
              NO EXISTE
            </div>
        </div>
        <?php
    }
            
        ?>
        
    </div>
    <?php
    
        
    
    
    die();
}
else if ( isset($_GET["busqueda_registro_precio"]) && ( $_GET["busqueda_registro_precio"] == "true" ) ){
   $connect = conectar();
    $strFecha1 = isset($_POST["fecha_uno"]) ? trim($_POST["fecha_uno"]) : "";
    $strFecha2 = isset($_POST["fecha_dos"]) ? trim($_POST["fecha_dos"]) : "";
    $strBuqueda = isset($_POST["busqueda"]) ? trim($_POST["busqueda"]) : "";
    
    $strFilter = "";
    if ( !empty($strBuqueda) ){
        $strFilter = " AND ( UPPER(CODIGO) LIKE '%{$strBuqueda}%' ) ";
        
        
    }
    
    $arrInfo = array();
    $stmt ="SELECT CODIGO, DESCRIP, DESDE, HASTA, PRECIO, MONEDA, NIU, NIU_COMBUSTIBLES FROM PRECOMB 
            WHERE   CODIGO IS NOT NULL
            AND  DESDE >= '{$strFecha1}'
            AND  HASTA <= '{$strFecha2}'
            {$strFilter}
            ORDER BY 1"  ;  

    $query = ibase_prepare($stmt);  
    $v_query = ibase_execute($query); 

    while($rTMP = ibase_fetch_assoc($v_query)){
        $arrInfo[$rTMP["NIU"]]["CODIGO"] = $rTMP["CODIGO"];
        $arrInfo[$rTMP["NIU"]]["DESCRIP"] = $rTMP["DESCRIP"];
        $arrInfo[$rTMP["NIU"]]["DESDE"] = $rTMP["DESDE"];
        $arrInfo[$rTMP["NIU"]]["HASTA"] = $rTMP["HASTA"];
        $arrInfo[$rTMP["NIU"]]["PRECIO"] = $rTMP["PRECIO"];
        $arrInfo[$rTMP["NIU"]]["MONEDA"] = $rTMP["MONEDA"];
        $arrInfo[$rTMP["NIU"]]["NIU"] = $rTMP["NIU"];
        $arrInfo[$rTMP["NIU"]]["NIU_COMBUSTIBLES"] = $rTMP["NIU_COMBUSTIBLES"];
    }

    ?>
    <div class="col-md-12">
        
        <?php
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            foreach ( $arrInfo as $key => $value ){
              
              ?>
                <div id="DivContenedor_<?php print $key; ?>" class="panel panel-primary">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" href="#collapse1_<?php print $key; ?>"><?php echo $value['CODIGO']; ?> - <?php echo $value['PRECIO']; ?></a> 
                    </h4>
                  </div>
                  <div id="collapse1_<?php print $key; ?>" class="panel-collapse collapse alert-info">
                  <div class="panel-body">
                  </div>
                        <table style="width:100%">
                            <tr>
                                <td><b>Descripcion: </b><?php echo $value['DESCRIP']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Desde:  </b><?php echo $value['DESDE']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Hasta:  </b><?php echo $value['HASTA']; ?></td>
                           </tr>
                           <tr>
                                <td><b>Precio: </b><?php echo $value['PRECIO']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Moneda:  </b><?php echo $value['MONEDA']; ?></td>
                            </tr>
                          <br>
                        </table>
                        <br>
                    </div>
                </div>
            <?php
            }
            
        }
    else{
        ?>
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
              NO EXISTE
            </div>
        </div>
        <?php
    }
            
        ?>
        
    </div>
    <?php
    
        
    
    
    die();
}

if ( isset($_POST["hidFormulario"]) ){
        
    $connect = conectar();
        
    $strDespacho = "0";
    $strDespachoD = "0";
    $strDespachoQ = "0";    
    $strFecha = isset($_POST["fecha_transac"]) ? trim($_POST["fecha_transac"]) : "";
    $strNumOrden = isset($_POST["numero_orden"]) ? trim($_POST["numero_orden"]) : "";
    //$strValorQ = isset($_POST["valor_q"]) ? trim($_POST["valor_q"]) : 0;
    $strValorQ = isset($_POST["hidvalor_q"]) ? floatval($_POST["hidvalor_q"]) : 0;
    //$strValorS = isset($_POST["valor_s"]) ? trim($_POST["valor_s"]) : 0;
    $strValorS = isset($_POST["hidvalor_s"]) ? floatval($_POST["hidvalor_s"]) : 0;
    //$strTasaCambio = isset($_POST["tasa_cambio"]) ? trim($_POST["tasa_cambio"]) : 1;
    $strTasaCambio = isset($_POST["hidtasa_cambio"]) ? floatval($_POST["hidtasa_cambio"]) : 1;
    //$strPrecioQ = isset($_POST["precio_galon_q"]) ? trim($_POST["precio_galon_q"]) : 0;
    $strPrecioQ = isset($_POST["hidprecio_galon_q"]) ? floatval($_POST["hidprecio_galon_q"]) : 0;
    //$strPrecioS = isset($_POST["precio_galon_s"]) ? trim($_POST["precio_galon_s"]) : 0;
    $strPrecioS = isset($_POST["hidprecio_galon_s"]) ? floatval($_POST["hidprecio_galon_s"]) : 0;
    //$strCantidadG = isset($_POST["cantidad_galones"]) ? trim($_POST["cantidad_galones"]) : 0;
    $strCantidadG = isset($_POST["hidcantidad_galones"]) ? floatval($_POST["hidcantidad_galones"]) : 0;
    $strProdNiu = "0"; 
    
    $strDescrip = isset($_POST["hidCodigo"]) ? trim($_POST["hidCodigo"]) : "";
    $arrSplit = explode(" |-| ", $strDescrip);
    
    /*
    print "<pre>";
    print_r($arrSplit);
    print "</pre>";
    */

    $strCodigo = $arrSplit[0];
    $strId = $arrSplit[2];
    $strControlExis = $arrSplit[3];
    $strDescrip = $arrSplit[1];

    $strProveedor = isset($_POST["hidProveedor"]) ? trim($_POST["hidProveedor"]) : "";
    $arrSplit = explode(" |-| ", $strProveedor);
    $strNit = $arrSplit[0];
    $strMoneda = $arrSplit[3]; 
    $strProveedor = $arrSplit[1];

    $stmt = "execute procedure grabar_producto2 ('{$strProdNiu}','{$strCodigo}','{$strDescrip}','{$strCantidadG}','{$strDespacho}','{$strCantidadG}','{$strProveedor}','{$strFecha}','{$strNumOrden}','{$strId}','{$strControlExis}','{$strValorQ}','{$strValorS}','{$strTasaCambio}','{$strPrecioS}','{$strDespachoD}','{$strValorS}','{$strPrecioQ}','{$strDespachoQ}','{$strValorQ}','{$strMoneda}')";
    $query = ibase_prepare($stmt);  
    $v_query = ibase_execute($query);


    /*
    echo   $strProdNiu . "         strProdNiu<br>";
    echo   $strCodigo . "      strCodigo<br>";
    echo   $strDescrip . "      strDescrip<br>";
    echo   $strCantidadG . "             strCantidadG<br>";
    echo   $strDespacho . "               strDespacho<br>";
    echo   $strCantidadG . "                  strCantidadG<br>";
    echo   $strProveedor . "                     strProveedor<br>";
    echo   $strFecha . "              strFecha<br>";
    echo   $strNumOrden . "              strNumOrden<br>";
    echo   $strId . "                  strId<br>";
    echo   $strControlExis . "                strControlExis<br>";
    echo   $strValorQ . "                 strValorQ<br>";
    echo   $strValorS . "               strValorS<br>";
    echo   $strTasaCambio . "               strTasaCambio<br>";
    echo   $strPrecioS . "             strPrecioS<br>";
    echo   $strDespachoD . "               strDespachoD<br>";
    echo   $strValorS . "             strValorS<br>";
    echo   $strPrecioQ . "              strPrecioQ<br>";
    echo   $strDespachoQ . "            strDespachoQ<br>";
    echo   $strCantidadG .  "             strCantidadG<br>";
    echo  $strMoneda  . "                strMoneda<br>";
        
    
    die();
    */
    
}

if ( isset($_POST["hidFormularioPrecio"]) ){
        
    $connect = conectar();
    
    $strCodigoNiu = "0";    
    $strDesde = isset($_POST["desde_pre"]) ? trim($_POST["desde_pre"]) : "";
    $strHasta = isset($_POST["hasta_pre"]) ? trim($_POST["hasta_pre"]) : "";
    $strPrecio = isset($_POST["precio_pre"]) ? floatval($_POST["precio_pre"]) : 0;
    $strDescrip = isset($_POST["descrip"]) ? trim($_POST["descrip"]) : "";
    $strMoneda = isset($_POST["moneda"]) ? floatval($_POST["moneda"]) : 0;
    $strNiuCombustible = isset($_POST["niuCombustible"]) ? floatval($_POST["niuCombustible"]) : "";
    $strCodigo = isset($_POST["codigopre"]) ? trim($_POST["codigopre"]) : "";
   
    $stmt = "execute procedure grabar_precomb
    ('{$strCodigoNiu}','{$strDesde}','{$strHasta}','{$strPrecio}','{$strDescrip}','{$strMoneda}','{$strNiuCombustible}','{$strCodigo}')";
    $query = ibase_prepare($stmt);  
    $v_query = ibase_execute($query);
    
    
   // echo   $strDesde . "         strDesde<br>";
    //echo   $strHasta . "       strHasta<br>";
    //echo   $strPrecio . "      strPrecio<br>";
    //echo   $strDescrip . "             strDescrip<br>";
    //echo   $strMoneda . "               strMoneda<br>";
    //echo   $strNiuCombustible . "                  strNiuCombustible<br>";
    //echo   $strCodigo . "                     strCodigo<br>";
   

}
    


?>
<!DOCTYPE html>

<html lang"es">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="shortcut icon" href="images/discolsa.ico">
    <head>
       <title>ORDEN COMPRA</title>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <style>
            body {font-family: Arial;}
            .tab {
                overflow: hidden;
                border: 1px solid #ccc;
                background-color: #f1f1f1;
            }
            .tab button {
                background-color: inherit;
                float: left;
                border: none;
                outline: none;
                cursor: pointer;
                padding: 14px 16px;
                transition: 0.3s;
                font-size: 17px;
            }
            .tab button:hover {
                background-color: #ddd;
            }
            .tab button.active {
                background-color: #ccc;
            }
            .tabcontent {
                display: none;
                padding: 6px 12px;
                border: 1px solid #ccc;
                border-top: none;
            }
            .tab button.active {

                background-color: #b8daff;

            }
            .tab button:hover {

                background-color: #b8daff;

            }
           .center{

                text-align: center;

            }
        </style>
        <?php
        load_header();
        ?>
   </head>
  
   <body>
     <div class="tab alert-info">
             
              <button class="tablinks" href="logout.php"><a href="logout.php" class="alert alert-danger">SALIR</a></button>
              <button class="tablinks" onclick="openCity(event, 'ing')">ORDEN</button>
              <button class="tablinks" onclick="openCity(event, 'reg')">REGS</button>
              <button class="tablinks" onclick="openCity(event, 'pre')">PRECIOS</button>
              
        </div>
        <div id="ing" class="tabcontent">
      <form action="formulario.php" method="post" class="alert-primary">
          <input type="hidden" name="hidFormulario" value="1">
           <P></P>
          <div class="form-group row">
              <label class="col" for="lname">Numero De Orden:</label>
              <input type="tel" name="numero_orden" class="col form-control form-control-lg" required>
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Proveedor:</label>
              <input type="text" name="proveedor" id="proveedor" onfocus="fntAutoCompleteProveedor();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="col form-control form-control-lg" required>
              <input type="hidden" name="hidProveedor" id="hidProveedor">
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Fecha Transaccion:</label>
              <input type="date" name="fecha_transac" id="fecha_transac" class="col form-control form-control-lg" required onchange="fntGetFecha();">
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Codigo:</label>
              <input type="text" name="codigo" id="codigo" onfocus="fntAutoCompleteCodigo();" class="col form-control form-control-lg" style="text-transform:uppercase;" required>
              <input type="hidden" name="hidCodigo" id="hidCodigo">
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Descripcion:</label>
              <input type="text" name="descripcion" class="col form-control form-control-lg" id="descripcion" style="text-transform:uppercase;"  >
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Valor:</label>
              <div class="col">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text alert-info">Q</div>
                        <input type="tel" name="valor_q" step="any" id="valor_q" required class="col form-control form-control-lg" onchange="fntCalculoValor();">
                        <input type="hidden" name="hidvalor_q" id="hidvalor_q" value="">
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text alert-info">$</div>
                        <input type="tel" name="valor_s" step="any" id="valor_s"  required class="col form-control form-control-lg" onchange="fntCalculoValor(true);">
                        <input type="hidden" name="hidvalor_s" id="hidvalor_s" value="">
                  </div>
                  </div>
                </div>
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Tasa De Cambio:</label>
              <input type="tel" name="tasa_cambio" step="any" id="tasa_cambio" class="col form-control form-control-lg" required  >
              <input type="hidden" name="hidtasa_cambio" id="hidtasa_cambio" value="">
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Precio:</label>
               <div class="col">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text alert-info">Q</div>
                        <input type="tel" name="precio_galon_q" step="any" id="precio_galon_q" required class="col form-control form-control-lg" onchange="fntCalculoValor();">
                        <input type="hidden" name="hidprecio_galon_q" id="hidprecio_galon_q" value="">
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text alert-info">$</div>
                        <input type="tel" name="precio_galon_s" step="any" id="precio_galon_s" required class=" col form-control form-control-lg" onchange="fntCalculoValor(true);">
                        <input type="hidden" name="hidprecio_galon_s" id="hidprecio_galon_s" value="">
                    </div>
                  </div>
                </div>
          </div>
          <div class="form-group row">
              <label class="col" for="lname">Cantidad De Galones:</label>
              <input type="tel" name="cantidad_galones" step="any" id="cantidad_galones" class="col form-control form-control-lg" required>
              <input type="hidden" name="hidcantidad_galones" id="hidcantidad_galones" value="">
          </div>
          <p></p>
          <input type="submit" value="ENVIAR" class="btn btn-primary">
          
      </form>
      </div> 
       <div id="reg" class="tabcontent">
      <div class="container">
          <div class="panel-group">
          
       
           <?php
            $connect = conectar();
              
              
              
            ?>
              <div class="col-md-12">
              <div class="form-row alert-info">
                    <p></p>
                    <div class="col">
                        <br><input type="date" name="fecha_uno_registro" id="fecha_uno_registro" class="form-control form-control-lg"  value="<?php print date("Y-m-01"); ?>" onchange="fntBusquedaRegistro()">
                    </div>
                    <div class="col">
                        <br><input type="date" name="fecha_dos_registro" id="fecha_dos_registro"  class="form-control form-control-lg" value="<?php print date("Y-m-d"); ?>" onchange="fntBusquedaRegistro() "><br>
                    </div><br>
                    <div class="col-12">
                        <input type="text" name="buscar_registro"  id="buscar_registro"  class="form-control form-control-lg" placeholder="Busqueda por nombre y código" onkeyup="fntBusquedaRegistro()"><br>
                    </div>
                </div>
            <hr>
        </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-12">
                <div id="divContentResultRegistro">&nbsp;</div>
                
            </div>
            <div class="col-md-12">&nbsp;</div>
            
            <script>
                function fntBusquedaRegistro(){
                    
                    var strFechaUno = $("#fecha_uno_registro").val();
                    var strFechaDos = $("#fecha_dos_registro").val();
                    var strBusqueda = $("#buscar_registro").val();
                    
                    //alert(strFechaUno + "                                  strFechaUno");
                    //alert(strFechaDos + "                                  strFechaDos");
                    //alert(strBusqueda + "                                  strBusqueda");
                    
                    $.ajax({
                      
                          url: "formulario.php?busqueda_registro=true",
                        data: {
                                fecha_uno:strFechaUno,
                                fecha_dos: strFechaDos,
                                busqueda: strBusqueda,
                            },
                          async: true,
                          global: false,
                          type: "post",
                          dataType: "html",
                            success: function(data) {

                              $("#divContentResultRegistro").html("");
                              $("#divContentResultRegistro").html(data);


                              return false;
                          }
                      });
                      
                }  
            
            </script>
            
            </div>
      </div>
      </div>
      
    <div id="pre" class="tabcontent">
         <div class="container">
          <div class="panel-group">
          
       
           <?php
            $connect = conectar();
              
              
              
            ?>
              <div class="col-md-12">
              <div class="form-row alert-info">
                    <p></p>
                    <div class="col">
                        <br><input type="date" name="fecha_uno_pre" id="fecha_uno_pre" class="form-control form-control-lg"  value="<?php print date("Y-m-01"); ?>" onchange="fntBusquedaPrecio()">
                    </div>
                    <div class="col">
                        <br><input type="date" name="fecha_dos_pre" id="fecha_dos_pre"  class="form-control form-control-lg" value="<?php print date("Y-m-d"); ?>" onchange="fntBusquedaPrecio() "><br>
                    </div><br>
                    <div class="col-12">
                        <input type="text" name="buscar_pre"  id="buscar_pre"  class="form-control form-control-lg" placeholder="Busqueda por código" onkeyup="fntBusquedaPrecio()"><br>
                    </div>
                    <div class="col-12 center">
                         <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                            INGRESAR PRECIO A CODIGO
                          </a>
                          
                          <div class="collapse" id="collapseExample">
                      <div class="card card-body alert-info">
                           <form action="formulario.php" method="post" class="alert-primary">
                              <input type="hidden" name="hidFormularioPrecio" value="1">
                               <P></P>
                               <div class="form-group row">
                                  <label class="col" for="lname">Desde:</label>
                                  <input type="date" name="desde_pre" id="desde_pre"  class="col form-control form-control-lg" required >
                              </div>
                              <div class="form-group row">
                                  <label class="col" for="lname">Hasta:</label>
                                  <input type="date" name="hasta_pre" id="hasta_pre" class="col form-control form-control-lg" required >
                              </div>
                                <div class="form-group row">
                                  <label class="col" for="lname">Codigo:</label>
                                  <input type="text" name="codigopre" id="codigopre" onfocus="fntAutoCompleteCodigoPre();" style="text-transform:uppercase;"  class="col form-control form-control-lg" required>
                                  <input type="hidden" name="niuCombustible" id="niuCombustible">
                              </div>
                              <div class="form-group row">
                                  <label class="col" for="lname">Descripcion:</label>
                                  <input type="text" name="descrip" class="col form-control form-control-lg" id="descrip" required >
                              </div>
                              
                              
                              <div class="center col-12 btn-group btn-group-toggle" data-toggle="buttons">
                                 <label class="center col" for="lname">Moneda:</label>
                                  <label class="center col-6 btn btn-primary active">
                                    <input type="radio" name="moneda" class="col form-control form-control-lg" id="moneda" value="1" autocomplete="off" checked>  - Quetzal -
                                  </label>
                                  <label class="center col-6 btn btn-primary">
                                    <input type="radio" name="moneda" class="col form-control form-control-lg" id="moneda" value="2" autocomplete="off">  - Dolar -
                                  </label><p></p>
                             </div><p></p>
                              <div class="form-group row">
                                  <label class="col" for="lname">Precio:</label>
                                  <input type="number" name="precio_pre" step="any" id="precio" class="col form-control form-control-lg" required  >
                              </div>
                              <p></p>
                              <input type="submit" value="ENVIAR" class="btn btn-primary">

                          </form>
                          </div>
                        </div>
                    </div>
                </div>
            <hr>
        </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-12">
                <div id="divContentResultPrecio">&nbsp;</div>
                
            </div>
            <div class="col-md-12">&nbsp;</div>
            
            <script>
                function fntBusquedaPrecio(){
                    
                   var strFechaUno = $("#fecha_uno_pre").val();
                    var strFechaDos = $("#fecha_dos_pre").val();
                    var strBusqueda = $("#buscar_pre").val();
                    
                    //alert(strFechaUno + "                                  strFechaUno");
                    //alert(strFechaDos + "                                  strFechaDos");
                    //alert(strBusqueda + "                                  strBusqueda");
                    
                    $.ajax({
                      
                          url: "formulario.php?busqueda_registro_precio=true",
                        data: {
                                fecha_uno:strFechaUno,
                                fecha_dos: strFechaDos,
                                busqueda: strBusqueda,
                            },
                          async: true,
                          global: false,
                          type: "post",
                          dataType: "html",
                            success: function(data) {

                              $("#divContentResultPrecio").html("");
                              $("#divContentResultPrecio").html(data);


                              return false;
                          }
                      });
                      
                }  
            
            </script>
            
            </div>
      </div>
    </div>  
      
      
      <div class="imgcontainer">
            <img src="images/discolsa.jpg" alt="Avatar" class="avatar" style="width:100%;">
      </div>
       
        <script>
            
            function fntEliminar(intIndex){
              
              
              $.ajax({
                      
                  url: "formulario.php?validaciones=eliminar&key="+intIndex,
                  async: true,
                  global: false,

                  success: function(data) {

                      
                      $("#DivContenedor_"+intIndex).remove();


                      return false;
                  }
              });
              
          }
          
          function openCity(evt, cityName) {
                var i, tabcontent, tablinks;
                    tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                    tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                    document.getElementById(cityName).style.display = "block";
                    evt.currentTarget.className += " active";
            }
            
            function fntAutoCompleteProveedor(){
         
              $( "#proveedor" ).autocomplete({
                                                                 
                 source: "formulario.php?validaciones=proveedor",
                 minLength: 2,
                 select: function( event, ui ) {
                     
                     var objhidProveedor = document.getElementById("hidProveedor");
                     var objproveedor = document.getElementById("proveedor");
                     
                     objhidProveedor.value = ui.item.info;
                     objproveedor.value = ui.item.value;
                     
                     $( "#valor_s" ).val( "" );
                     $( "#hidvalor_s" ).val( "" );
                     //$( "#tasa_cambio" ).val( "" ); 
                     $( "#precio_galon_s" ).val( "" ); 

                     $( "#valor_q" ).val( "" );
                     $( "#hidvalor_q" ).val( "" );
                     $( "#precio_galon_q" ).val( "" ); 
                     $( "#hidprecio_galon_q" ).val( "" );
                     $( "#cantidad_galones" ).val( "" );
                     $( "#hidcantidad_galones" ).val( "" );
                     
                     var intMoneda = ui.item.moneda;
                     
                     if ( intMoneda == 1 ){
                        $( "#valor_s" ).prop( "disabled", true ); 
                         $( "#tasa_cambio" ).prop( "disabled", true );
                         $( "#precio_galon_s" ).prop( "disabled", true );
                         
                         $( "#valor_q" ).prop( "disabled", false );
                         $( "#precio_galon_q" ).prop( "disabled", false );
                         
                     }

                     else if ( intMoneda == 2 ){
                        $( "#valor_s" ).prop( "disabled", true ); 
                         $( "#tasa_cambio" ).prop( "disabled", false );
                         $( "#precio_galon_s" ).prop( "disabled", false );
                         
                         $( "#valor_q" ).prop( "disabled", false );
                         $( "#precio_galon_q" ).prop( "disabled", true );
                         
                     }
                     else{
                         $( "#valor_s" ).prop( "disabled", true ); 
                         $( "#tasa_cambio" ).prop( "disabled", true );
                         $( "#precio_galon_s" ).prop( "disabled", true );
                         
                         $( "#valor_q" ).prop( "disabled", true );
                         $( "#precio_galon_q" ).prop( "disabled", true );
                     }
                     
                        
                     $( "#cantidad_galones" ).prop( "disabled", true );
                     
                     
                     
                     
                 }
             });
              
          }
            
           function fntAutoCompleteCodigo(){
               
               var strFecha = $( "#fecha_transac" ).val(); 
               
         
              
              $( "#codigo" ).autocomplete({
                                                                 
                 source: "formulario.php?validaciones=codigo&fecha="+strFecha,
                 minLength: 2,
                 select: function( event, ui ) {
                     
                     var objcodigo = document.getElementById("codigo");
                     var objdescripcion = document.getElementById("descripcion");
                     
                     objcodigo.value = ui.item.value;
                     objdescripcion.value = ui.item.matricula;
                     
                     $( "#codigo" ).val( ui.item.value ); 
                     $( "#descripcion" ).val( ui.item.descripcion );
                     $( "#hidCodigo" ).val( ui.item.info ); 
                     
                     var sinValorQ = ui.item.valor_q;
                     var sinValorD = ui.item.valor_d;
                     var strValorControl = ui.item.control_valor;
                     
                     //alert(sinValorQ + "                     sinValorQ");
                     //alert(sinValorD + "                     sinValorD");
                     //alert(strValorControl + "                     strValorControl");
                     
                     
                     //var objPrecioQ = document.getElementById("precio_galon_q");
                     //var objPrecioD = document.getElementById("precio_galon_s");
                     
                     if ( strValorControl == "N" ){
                         alert("El código no contiene precio....");
                     }
                     else{
                         //objPrecioQ.value = sinValorQ;
                         //objPrecioD.value = sinValorD;
                         
                         //alert(sinValorQ + "                    sinValorQ");
                         //alert(sinValorD + "                    sinValorD");
                         //alert($( "#precio_galon_s" ) + "                    precio_balon_s");
                         //alert($( "#precio_galon_q" ) + "                    precio_galon_q");
                         
                         $( "#precio_galon_s" ).val( sinValorD ); 
                         $( "#precio_galon_q" ).val( sinValorQ );
                         $( "#hidprecio_galon_s" ).val( sinValorD ); 
                         $( "#hidprecio_galon_q" ).val( sinValorQ );
                     }
                     
                     fntGetPrecioGalon();
                     
                     
                 }
             });
              
          } 
            
            function fntGetFecha(){
                
                var strFecha = $( "#fecha_transac" ).val(); 
                
                $.ajax({
                      
                  url: "formulario.php?validaciones=tasa_cambio&fecha="+strFecha,
                  async: true,
                  global: false,

                  success: function(data) {
                      
                      var sinTasa = data;
                      
                      $( "#tasa_cambio" ).val(sinTasa);
                      $( "#hidtasa_cambio" ).val(sinTasa);
                      
                      fntGetPrecioGalon();


                      return false;
                  }
              });
                
            }
            
            function fntGetPrecioGalon(){
                
                var strFecha = $( "#fecha_transac" ).val();
                var strCodigo = $( "#codigo" ).val();
                
                //alert(strCodigo + "                 strCodigo");
                
                $.ajax({
                      
                  url: "formulario.php?validaciones=precios_galones&fecha="+strFecha+"&codigo="+strCodigo,
                  async: true,
                  global: false,

                  success: function(data) {
                      
                      
                      var arrSplit = data.split("|-|");
                      var sinPrecio = arrSplit[0] ? arrSplit[0] : "";
                      var intMoneda = arrSplit[1] ? arrSplit[1] : 0;
                      
                      
                      //$( "#precio_galon_s" ).val( "" ); 
                      //$( "#precio_galon_q" ).val( "" );
                      
                      //$( "#hidprecio_galon_s" ).val( "" ); 
                      //$( "#hidprecio_galon_q" ).val( "" );
                      
                     // alert(intMoneda + "              intMoneda precion moneda")
                      
                      if ( intMoneda == 1 ){
                          $( "#precio_galon_s" ).val( "" ); 
                          $( "#precio_galon_q" ).val( sinPrecio ); 
                          
                          $( "#hidprecio_galon_s" ).val( "" ); 
                          $( "#hidprecio_galon_q" ).val( sinPrecio );
                      }
                      else if ( intMoneda == 2 ){
                          $( "#precio_galon_s" ).val( sinPrecio ); 
                          $( "#precio_galon_q" ).val( "" ); 
                          $( "#hidprecio_galon_s" ).val( sinPrecio ); 
                          $( "#hidprecio_galon_q" ).val( "" );
                      }
                      
                          
                      //$( "#tasa_cambio" ).val(data); 


                      return false;
                  }
              });
                
            }
            
            function fntCalculoValor(boolDolar){
                boolDolar = !boolDolar ? false : true;
                
                
                var sinTasa = $( "#tasa_cambio" ).val(); 
                if ( boolDolar ){
                    
                    
                    var sinMontoQ = $( "#valor_q" ).val();
                    $( "#hidvalor_q" ).val(sinMontoQ);

                    var sinMontoS = sinMontoQ/sinTasa;
                    sinMontoS = sinMontoS*1;
                    sinMontoS = ( isNaN(sinMontoS) || sinMontoS<=0 ) ? "" : sinMontoS.toFixed(5); 
                    $( "#valor_s" ).val(sinMontoS);
                    $( "#hidvalor_s" ).val(sinMontoS);
                    
                    var sinPrecioGalonS = $( "#precio_galon_s" ).val(); 
                    sinPrecioGalonS = sinPrecioGalonS*1;
                    $( "#hidprecio_galon_s" ).val(sinPrecioGalonS);
                    
                    var sinCantidadGalones = sinMontoS/sinPrecioGalonS;
                    sinCantidadGalones = sinCantidadGalones*1;
                    sinCantidadGalones = sinCantidadGalones.toFixed(5); 
                    $( "#cantidad_galones" ).val(sinCantidadGalones); 
                    $( "#hidcantidad_galones" ).val(sinCantidadGalones);
                    
                    
                }
                else if ( !boolDolar ){
                    
                    var sinMontoQ = $( "#valor_q" ).val();
                    $( "#hidvalor_q" ).val(sinMontoQ);
                    
                    var sinPrecioGalonQ = $( "#precio_galon_q" ).val(); 
                    sinPrecioGalonQ = sinPrecioGalonQ*1;
                    $( "#hidprecio_galon_q" ).val(sinPrecioGalonQ);
                    
                    var sinCantidadGalones = sinMontoQ/sinPrecioGalonQ
                    sinCantidadGalones = sinCantidadGalones*1;
                    sinCantidadGalones = sinCantidadGalones.toFixed(5); 
                    $( "#cantidad_galones" ).val(sinCantidadGalones);
                    $( "#hidcantidad_galones" ).val(sinCantidadGalones); 
                    
                }
                
                
            }
            
            
            function fntAutoCompleteCodigoPre(){
              
              $( "#codigopre" ).autocomplete({
                                                                 
                 source: "formulario.php?validaciones=codigo_precio",
                 minLength: 2,
                 select: function( event, ui ) {
                     
                     var objCodigo = document.getElementById("codigopre");
                     var objNiuCombustible = document.getElementById("niuCombustible");
                     var objDescripcion = document.getElementById("descrip");
                     
                     objCodigo.value = ui.item.value;
                     objNiuCombustible.value = ui.item.niu;
                     objDescripcion.value = ui.item.descrip;
                     
                 }
             });
                
            
          }
            
        $(function(){
            $('#precio,#desde_pre, #hasta_pre, #codigopre').change(function(){
                $.ajax({

                    url: "formulario.php?validaciones=checkexist",
                    method: "POST",
                    data:{
                        precio:$('#precio').val(),
                        desde:$('#desde_pre').val(),
                        hasta:$('#hasta_pre').val(),
                        codigopre:$('#codigopre').val(),
                    },
                    dataType: "json",
                    async: true,
                    global: false,
                    success: function(data) {
                        if(data.exist){
                            if(data.exist == 1){
                                alert("La fecha o el precio ingresado de este Codigo esta en uso.");
                            }
                        }
                        return false;
                    }
                });    
            })
        });
            
       </script>

   </body>
</html>        
