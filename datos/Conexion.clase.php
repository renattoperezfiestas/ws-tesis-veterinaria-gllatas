<?php

require_once 'configuracion.php';
require_once '../util/funciones/Funciones.clase.php';



class Conexion{
    protected $dblink;
    
    public function __construct() {
        $this->abrirConexion();
        //echo "conexión abierta";
    }
    
    public function __destruct() {
        $this->dblink = NULL;
        //echo "Conexión cerrada";
    }
    
    protected function abrirConexion(){
        $servidor = "pgsql:host=".SERVIDOR_BD.";port=".PUERTO_BD.";dbname=".NOMBRE_BD;
        $usuario = USUARIO_BD;
        $clave = CLAVE_BD;
        
        
        
        try {
            $this->dblink = new PDO($servidor, $usuario, $clave);
            $this->dblink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $exc) {
            Funciones::mensaje($exc->getMessage(), "e");
            
        }
        
        return $this->dblink;
    }
}