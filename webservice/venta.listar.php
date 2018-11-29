<?php

require_once '../negocio/Venta.clase.php';
require_once '../util/funciones/Funciones.clase.php';
require_once 'token.validar.php';

if (! isset($_POST["token"])){
    Funciones::imprimeJSON(500, "Debe especificar un token", "");
    exit();
}

$token = $_POST["token"];
try {
    if(validarToken($token)){
       
        $codigoTipoComprobante = $_POST["p_tc"];
        $numeroSerie = $_POST["p_nser"];
        $codigoCliente = $_POST["p_cli"];
        //$fechaVenta = $_POST["p_fv"];
        $fechaVenta = date('Y-a-');
        $codigoUsuario = $_POST["p_cu"];
        $detalleVenta = $_POST["p_det"];
        
        $obj = new Venta();
        $obj->setCodigoTipoComprobante($codigoTipoComprobante);
        $obj->setNumeroSerie($numeroSerie);
        $obj->setCodigoCliente($codigoCliente);
        $obj->setFechaVenta($fechaVenta);
        $obj->setCodigoUsuario($codigoUsuario);
        $obj->setDetalleVenta($detalleVenta);
        
        $resultado = $obj->agregar();
        
        Funciones::imprimeJSON(200, "", $resultado);
    }
    
    
} catch (Exception $exc) {
    
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}
