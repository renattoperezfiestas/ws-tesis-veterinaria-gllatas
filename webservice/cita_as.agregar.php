<?php
require_once 'token.validar.php';
require_once '../negocio/Cita_as.clase.php';
require_once '../util/funciones/Funciones.clase.php';

if (! isset($_POST["token"])){
    Funciones::imprimeJSON(500, "Debe especificar un token", "");
    exit();
}

$token = $_POST["token"];

try {
   if(validarToken($token)){ //token válido
       
         $fechaCita=$_POST["p_fecha_cita"];
         $fechaCita= date("Y-m-d");
         
        $estado=$_POST["p_estado"];
        $idMascota=$_POST["p_id_mascota"];

        
     
     $obj = new Cita_as();
     $obj->setFecha_cita($fechaCita);
     $obj->setEstado($estado);
     $obj->setId_mascota($idMascota);
     
     
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