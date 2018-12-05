<?php

require_once '../negocio/Mascota.clase.php';
require_once '../util/funciones/Funciones.clase.php';
require_once 'token.validar.php';

if (! isset($_POST["token"])){
    Funciones::imprimeJSON(500, "Debe especificar un token", "");
    exit();
}

$token = $_POST["token"];
try {
    if(validarToken($token)){
        $obj = new Mascota();
       // $resultado = $obj->listar_for_veterinario($_POST["p_tc"]);
        $resultado = $obj->listar_for_veterinario();
        Funciones::imprimeJSON(200, "", $resultado);
    }
    
    
} catch (Exception $exc) {
    
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}
