<?php
require_once 'token.validar.php';
require_once '../negocio/Venta.clase.php';
require_once '../util/funciones/Funciones.clase.php';

if (! isset($_POST["token"])){
    Funciones::imprimeJSON(500, "Debe especificar un token", "");
    exit();
}

$token = $_POST["token"];

try {
   if(validarToken($token)){ //token válido
         $codigoTipoComprobante=$_POST["p_tc"];
     $numeroSerie=$_POST["p_nser"];
     $codigoCliente=$_POST["p_cli"];
     $fechaVenta= date("Y-m-d");
     $codigoUsuario=$_POST["p_cu"];
    
     $detalleVenta=$_POST["p_det"];
     
     $obj = new Venta();
     $obj->setCodigoTipoComprobante($codigoTipoComprobante);
     $obj->setNumeroSerie($numeroSerie);
     $obj->setCodigoCliente($codigoCliente);
     $obj->setFechaVenta($fechaVenta);
     $obj->setCodigoUsuario($codigoUsuario);
     $obj->setDetalleVenta($detalleVenta);
     
     $resultado=$obj->agregar();
             
     
    
       Funciones::imprimeJSON(200, "venta_agregar_ok_ntbs", $resultado);
       
   }
} catch (Exception $exc) {
    $mensajeError = $exc ->getMessage();
    $position = strpos($mensajeError, "Raise exception");
    if($position>0){
        $mensajeError = substr($mensajeError, $position+27, strlen($mensajeError));
    }
    
    Funciones::imprimeJSON(500, $mensajeError, "");
}