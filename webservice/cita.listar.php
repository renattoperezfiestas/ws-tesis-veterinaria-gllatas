<?php

require_once '../negocio/Cita.clase.php';
require_once '../util/funciones/Funciones.clase.php';
require_once 'token.validar.php';

if (! isset($_POST["token"])){
    Funciones::imprimeJSON(500, "Debe especificar un token", "");
    exit();
}

$token = $_POST["token"];
try {
    if(validarToken($token)){
        $obj = new Cita();
        $resultado = $obj->listar_app();
        
        
        $listaArticulo = array();
        for ($i = 0; $i < count($resultado); $i++){ 
        
            $foto = $obj->obtenerFoto ($resultado[$i]["id_cita"]);
        
            $datosArticulo = array(
                "codigo" => $resultado[$i]["id_cita"],
                "fecha" => $resultado[$i]["fecha_cita"],
                "nombre" => $resultado[$i]["mascota"],
                "estado" => $resultado[$i]["estado"],
                "id_mascota" => $resultado[$i]["id_mascota"],
                "foto" => $foto
            );
            
            $listaArticulo[$i] = $datosArticulo;
        }
        
        Funciones::imprimeJSON(200, "", $listaArticulo);
    }
    
    
} catch (Exception $exc) {
    
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}
