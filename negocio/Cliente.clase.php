<?php
require_once '../datos/Conexion.clase.php';

class Cliente extends Conexion{
    
     public function listar() {
        try {
            $sql = " select * from f_listar_cliente()";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    //put your code here
}
