<?php

require_once '../datos/Conexion.clase.php';

class Mascota extends Conexion {
    private $id_mascota;
    private $nombre;
    private $color;
    private $sexo;
    private $fecha_nacimiento;
    private $castrado;
    private $estado;
    private $id_cliente;
    private $id_raza; 
    
    function getColor() {
        return $this->color;
    }

    function getSexo() {
        return $this->sexo;
    }

    function getFecha_nacimiento() {
        return $this->fecha_nacimiento;
    }

    function getCastrado() {
        return $this->castrado;
    }

    function getEstado() {
        return $this->estado;
    }

    function setColor($color) {
        $this->color = $color;
    }

    function setSexo($sexo) {
        $this->sexo = $sexo;
    }

    function setFecha_nacimiento($fecha_nacimiento) {
        $this->fecha_nacimiento = $fecha_nacimiento;
    }

    function setCastrado($castrado) {
        $this->castrado = $castrado;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

        
    function getId_mascota() {
        return $this->id_mascota;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getId_cliente() {
        return $this->id_cliente;
    }

    function getId_raza() {
        return $this->id_raza;
    }

    function setId_mascota($id_mascota) {
        $this->id_mascota = $id_mascota;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setId_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    function setId_raza($id_raza) {
        $this->id_raza = $id_raza;
    }

    
        
    public function listar_for_cliente( $p_id_cliente ) {
        try {
            $sql = "
                SELECT 
                    mascota.id_mascota, 
                    mascota.nombre, 
                    raza.descripcion as raza,
                    mascota.color,
                    (case when mascota.sexo='H' then 'hembra' else 'macho' end)::character varying as sexo,
                    mascota.fecha_nacimiento,
                    mascota.castrado
                  FROM 
                    public.mascota, 
                    public.raza, 
                    public.cliente
                  WHERE 
                    raza.id_raza = mascota.id_raza AND
                    cliente.id_cliente = mascota.id_cliente
                    and cliente.id_cliente=:p_id_cliente
                    order by 1


                    ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_id_cliente", $p_id_cliente);
            
            $sentencia->execute();
            
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            
            return $resultado;
            
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
     public function listar_for_veterinario( ) {
        try {
            $sql = "
                SELECT 
                    mascota.id_mascota, 
                    mascota.nombre, 
                    raza.descripcion as raza,
                    mascota.color,
                    (case when mascota.sexo='H' then 'hembra' else 'macho' end)::character varying as sexo,
                    mascota.fecha_nacimiento,
                    mascota.castrado,
                    (cliente.nombres ||' '|| cliente.apellidos)::character varying as nombre_cliente
                  FROM 
                    public.mascota, 
                    public.raza, 
                    public.cliente
                  WHERE 
                    raza.id_raza = mascota.id_raza AND
                    cliente.id_cliente = mascota.id_cliente and
                    mascota.estado=1
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
     
    
    public function eliminar( $p_id_mascota ){
        $this->dblink->beginTransaction();
        try {
            $sql = "update mascota set estado = 2 where id_mascota = :p_id_mascota";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_id_mascota", $p_id_mascota);
            $sentencia->execute();
            
            $this->dblink->commit();
            
            return true;
        } catch (Exception $exc) {
            $this->dblink->rollBack();
            throw $exc;
        }
        
        return false;
    }
    
    public function agregar() {
        $this->dblink->beginTransaction();
        
        try {
            $sql = "select * from f_generar_correlativo('mascota') as nc";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetch();
            
            if ($sentencia->rowCount()){
                $nuevoCodigoArticulo = $resultado["nc"];
                $this->setId_mascota($nuevoCodigoArticulo);
                
                $sql = "
                        INSERT INTO mascota
                        (
                                id_mascota, 
                                nombre, 
                                color, 
                                sexo, 
                                fecha_nacimiento,
                                castrado,
                                estado,
                                id_cliente,
                                id_raza
                        )
                        VALUES 
                        (
                                :p_id_mascota, 
                                :p_nombre, 
                                :p_color, 
                                :p_sexo, 
                                :p_fecha_nacimiento,
                                :p_castrado,
                                :p_estado,
                                :p_id_cliente,
                                :p_id_raza
                                
                        );
                    ";
                
                //Preparar la sentencia
                $sentencia = $this->dblink->prepare($sql);
                
                //Asignar un valor a cada parametro
                $sentencia->bindParam(":p_id_mascota", $this->getId_mascota());
                $sentencia->bindParam(":p_nombre", $this->getNombre());
                $sentencia->bindParam(":p_color", $this->getColor());
                $sentencia->bindParam(":p_sexo", $this->getSexo());
                $sentencia->bindParam(":p_fecha_nacimiento", $this->getFecha_nacimiento());
                $sentencia->bindParam(":p_castrado", $this->getCastrado());
                $sentencia->bindParam(":p_estado", $this->getEstado());
                $sentencia->bindParam(":p_id_cliente", $this->getId_cliente());
                $sentencia->bindParam(":p_id_raza", $this->getId_raza());
                
                
                //Ejecutar la sentencia preparada
                $sentencia->execute();
                
                
                //Actualizar el correlativo en +1
                $sql = "update correlativo set numero = numero + 1 where tabla = 'mascota'";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();
                
                $this->dblink->commit();
                
                return true; //significa que todo se ha ejecutado correctamente
                
            }else{
                throw new Exception("No se ha configurado el correlativo para la tabla mascota");
//                return true;
            }
            
        } catch (Exception $exc) {
            $this->dblink->rollBack(); //Extornar toda la transacción
            throw $exc;
        }
        
        return false;
            
    }
}


