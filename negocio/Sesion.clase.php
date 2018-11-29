<?php

require_once '../datos/Conexion.clase.php';

class Sesion extends Conexion {

    private $email;
    private $clave;

    function getEmail() {
        return $this->email;
    }

    function getClave() {
        return $this->clave;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    public function validarSesion() {
        try {
            $sql = "select * from f_validar_sesion(:p_email, :p_clave);";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_email", $this->getEmail());
            $sentencia->bindParam(":p_clave", $this->getClave());
            $sentencia->execute();
            return $sentencia->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            throw $exc->getTraceAsString();
        }
    }
    
    public function  obtenerFoto($dni){
        $foto = "../imagenes-usuario/".$dni;
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
