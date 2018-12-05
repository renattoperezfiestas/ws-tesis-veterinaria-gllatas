<?php

require_once '../datos/Conexion.clase.php';

class Cita_as extends Conexion {
    private $id_cita_as;
    private $fecha_cita;
   
    private $estado;
   
    private $id_mascota;
  
    
     
    function getId_cita_as() {
        return $this->id_cita_as;
    }

    function setId_cita_as($id_cita_as) {
        $this->id_cita_as = $id_cita_as;
    }

    
    function getEstado() {
        return $this->estado;
    }

    function getId_mascota() {
        return $this->id_mascota;
    }

    

    function setFecha_cita($fecha_cita) {
        $this->fecha_cita = $fecha_cita;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setId_mascota($id_mascota) {
        $this->id_mascota = $id_mascota;
    }

    
   

     public function agregar_as() {
        $this->dblink->beginTransaction();
        try {
            $sql = "select * from f_generar_correlativo('cita_as') as nc";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetch();
            
            if ($sentencia->rowCount()){
                $nuevoNumeroVenta = $resultado["nc"];
                $this->setId_cita_as($nuevoNumeroVenta);
                
                
                $sql = "
                        INSERT INTO cita_as
                            (
                                     id_cita_as,
                                     fecha_cita,                                     
                                      estado,                                       
                                      id_mascota
                                            
                            )
                        VALUES 
                            (
                                     :p_id_cita_as,
                                     :p_fecha_cita,                                     
                                      :p_estado,                                       
                                       :p_id_mascota
                                            
                            );
                    ";
                
                //Preparar la sentencia
                $sentencia = $this->dblink->prepare($sql);
                
                //Asignar un valor a cada parametro
                $sentencia->bindParam(":p_id_cita", $this->_fecha());
                $sentencia->bindParam(":p_fecha_cita", $this->getFecha_cita());
                
                $sentencia->bindParam(":p_estado", $this->getEstado());
//                
                $sentencia->bindParam(":p_id_mascota", $this->getId_mascota());
//                
                
                
                //Ejecutar la sentencia preparada
                $sentencia->execute();
                
                
                
             
                /*INSERTAR EN LA TABLA VENTA_DETALLE*/
                
                
                //Actualizar el correlativo en +1
                $sql = "update correlativo set numero = numero + 1 where tabla = 'cita_as'";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();
                
               
                
                //Terminar la transacción
                $this->dblink->commit();
                
                return true;
            }
            
        } catch (Exception $exc) {
            $this->dblink->rollBack(); //Extornar toda la transacción
            throw $exc;
        }
        
        return false;
        
    }
     public function listar( ) {
        try {
            $sql = "
               select 
                c.id_cita,
                c.fecha_cita,
                Coalesce(c.diagnostico,'-')as diagnostico,
                m.nombre as mascota,
                c.peso,
                (vet.nombres ||' '||vet.apellidos)as nombre_veterinario,
                (case when c.estado='A' then 'Anulado' else (case when c.estado='P' then 'Pagado' else 'Por cobrar' end)end)::character varying as estado
                from cita c
                inner join mascota m on (m.id_mascota = c.id_mascota)
                inner join veterinario vet on (vet.dni_veterinario=c.dni_veterinario)
                where c.estado!='A'
                order by 1

                    ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
            
        } catch (Exception $exc) {
            throw $exc;
        }
    }
     public function listarHistoria( ) {
        try {
            $sql = "
               select 
                c.id_cita,
                c.fecha_cita,
               
                m.nombre as mascota,
                c.peso,
                c.temperatura,
                c.frec_cardiaca,               
                c.frec_respiratoria,
                 (case when c.mucosas = 'S' then 'Sì' else 'No' end) ::character varying as mucosas,             
                
                 Coalesce(c.diagnostico,'-')as diagnostico,
                (vet.nombres ||' '||vet.apellidos)as nombre_veterinario
                
                from cita c
                inner join mascota m on (m.id_mascota = c.id_mascota)
                inner join veterinario vet on (vet.dni_veterinario=c.dni_veterinario)
                where c.estado!='A'
                order by 1

                    ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
            
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
     public function listar_cita_as( ) {
        try {
            $sql = "
                select 
		c.id_cita_as,
                c.fecha_cita,   
                (case when c.estado='A' then 'Atendido' else  'Por atender' end)::character varying as estado ,  
                (cli.nombres ||' '||cli.apellidos ) ::character varying as nombre_completo  ,      
                m.nombre as mascota

                from cita_as c 
                inner join mascota m on (m.id_mascota = c.id_mascota)
                inner join cliente cli on (m.id_cliente = cli.id_cliente)
                 where c.estado!='A'
                order by 1

                    ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
            
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
//    public function anular($numeroVenta) {
//        $this->dblink->beginTransaction();
//        try {
//            $sql = "update venta set estado = 'A' where numero_venta = :p_numero_venta";
//            $sentencia = $this->dblink->prepare($sql);
//            $sentencia->bindParam(":p_numero_venta", $numeroVenta);
//            $sentencia->execute();
//            
//            $sql = "select codigo_articulo, cantidad from venta_detalle where numero_venta = :p_numero_venta";
//            $sentencia = $this->dblink->prepare($sql);
//            $sentencia->bindParam(":p_numero_venta", $numeroVenta);
//            $sentencia->execute();
//            
//            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
//            
//            for ($i = 0; $i < count($resultado); $i++) {
//                $sql = "update articulo set stock = stock + :p_cantidad where codigo_articulo = :p_codigo_articulo";
//                $sentencia = $this->dblink->prepare($sql);
//                $sentencia->bindParam(":p_cantidad", $resultado[$i]["cantidad"]);
//                $sentencia->bindParam(":p_codigo_articulo", $resultado[$i]["codigo_articulo"]);
//                $sentencia->execute();
//            }
//            
//            //Terminar la transacción
//            $this->dblink->commit();
//            
//            return true;
//                    
//        } catch (Exception $exc) {
//            $this->dblink->rollBack(); //Extornar toda la transacción
//            throw $exc;
//        }
//    }

}
