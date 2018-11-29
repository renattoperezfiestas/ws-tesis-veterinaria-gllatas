<?php

require_once '../negocio/SerieComprobante.clase.php';
require_once '../util/funciones/Funciones.clase.php';
require_once 'token.validar.php';

if (! isset($_POST["token"])){
    Funciones::imprimeJSON(500, "Debe especificar un token", "");
    exit();
}

$token = $_POST["token"];
try {
    if(validarToken($token)){
        $obj = new SerieComprobante();
        $resultado = $obj->cargarSerieComprobante($_POST["p_tc"]);
        
        Funciones::imprimeJSON(200, "", $resultado);
    }
    
    
} catch (Exception $exc) {
    
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}
