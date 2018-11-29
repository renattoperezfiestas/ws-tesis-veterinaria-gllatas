<?php

require_once '../datos/Conexion.clase.php';

class SerieComprobante extends Conexion {
    
    public function cargarSerieComprobante($p_tipoComprobante) {
        try {
            $sql = "
                    select 
                            numero_serie
                    from
                            serie_comprobante
                    where
                            codigo_tipo_comprobante = :p_tipoComprobante 
                ";
            
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_tipoComprobante", $p_tipoComprobante);
            $sentencia->execute();
            
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
    
    
    
}
