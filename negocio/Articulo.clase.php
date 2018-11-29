<?php

require_once '../datos/Conexion.clase.php';

class Articulo extends Conexion {
    
    public function listar() {
        try {
            $sql = " select * from articulo order by 2";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    public function obtenerFoto($codigoArticulo){
        $foto = "../imagenes/".$codigoArticulo;
        
        if(file_exists($foto.".jpg")){
            $foto = $foto.".jpg";
        }else{
            if(file_exists($foto.".png")){
                $foto = $foto.".png";
            }else{
                $foto = "none";
            }
        }
        if($foto == "none"){
            return $foto;
        }else{
            return Funciones::$DIRECCION_WEB_SERVICE.$foto;
        }
    }
    }
    
