<?php

require_once '../negocio/Articulo.clase.php';
require_once '../util/funciones/Funciones.clase.php';
require_once 'token.validar.php';

if (! isset($_POST["token"])){
    Funciones::imprimeJSON(500, "Debe especificar un token", "");
    exit();
}

$token = $_POST["token"];
try {
    if(validarToken($token)){
        $obj = new Articulo();
        $resultado = $obj->Listar();
        
        
        $listaArticulo = array();
        for ($i = 0; $i < count($resultado); $i++){ 
        
            $foto = $obj->obtenerFoto ($resultado[$i]["codigo_articulo"]);
        
            $datosArticulo = array(
                "codigo" => $resultado[$i]["codigo_articulo"],
                "nombre" => $resultado[$i]["nombre"],
                "precio" => $resultado[$i]["precio_venta"],
                "stock" => $resultado[$i]["stock"], 
                "foto" => $foto
            );
            
            $listaArticulo[$i] = $datosArticulo;
        }
        
        Funciones::imprimeJSON(200, "", $listaArticulo);
    }
    
    
} catch (Exception $exc) {
    
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}
