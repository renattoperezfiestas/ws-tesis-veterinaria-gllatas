<?php

require_once '../datos/Conexion.clase.php';

class Cliente2 extends Conexion {
    private $id_cliente;
    private $apellidos;
    private $nombres;
    private $direccion;
    private $telef_fijo; 
    private $num_cel1;
    private $email;
    private $clave;
    private $estado;
    private $tipo_usuario;
    
    
    function getDireccion() {
        return $this->direccion;
    }

    function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    
    
    function getTipo_usuario() {
        return $this->tipo_usuario;
    }

    function setTipo_usuario($tipo_usuario) {
        $this->tipo_usuario = $tipo_usuario;
    }

        
    function getId_cliente() {
        return $this->id_cliente;
    }

    function getApellidos() {
        return $this->apellidos;
    }

    function getNombres() {
        return $this->nombres;
    }

    

    function getTelef_fijo() {
        return $this->telef_fijo;
    }

    function getNum_cel1() {
        return $this->num_cel1;
    }

    function getEmail() {
        return $this->email;
    }

    function getClave() {
        return $this->clave;
    }

    function getEstado() {
        return $this->estado;
    }

    function setId_cliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    function setApellidos($apellidos) {
        $this->apellidos = $apellidos;
    }

    function setNombres($nombres) {
        $this->nombres = $nombres;
    }

   

    function setTelef_fijo($telef_fijo) {
        $this->telef_fijo = $telef_fijo;
    }

    function setNum_cel1($num_cel1) {
        $this->num_cel1 = $num_cel1;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setClave($clave) {
        $this->clave = $clave;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    
    
        
    public function listar2() {
        try {
            $sql = "
                select 
                id_cliente,
                (nombres ||' '||apellidos ) ::character varying as nombre_completo,
                direccion,
                telef_fijo,
                num_cel1,
                email,
                (case when estado=1 then 'activo' else 'inactivo' end)::character varying as estado
                from
                cliente
                where
                estado=1


                    ";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
            
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    public function eliminar( $p_id_cliente ){
        $this->dblink->beginTransaction();
        try {
            $sql = "update cliente set estado = 2 where id_cliente = :p_id_cliente";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_id_cliente", $p_id_cliente);
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
            $sql = "select * from f_generar_correlativo('cliente') as nc";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetch();
            
            if ($sentencia->rowCount()){
                $nuevoCodigoArticulo = $resultado["nc"];
                $this->setId_cliente($nuevoCodigoArticulo);
                
                $sql = "
                        INSERT INTO cliente
                        (
                                id_cliente, 
                                apellidos, 
                                nombres, 
                                direccion, 
                                telef_fijo,
                                num_cel1,
                                email,
                                clave,
                                estado,
                                tipo_usuario
                        )
                        VALUES 
                        (
                                :p_id_cliente, 
                                :p_apellidos, 
                                :p_nombres, 
                                :p_direccion, 
                                :p_telef_fijo,
                                :p_num_cel1,
                                :p_email,
                                :p_clave,
                                :p_estado,
                                :p_tipo_usuario
                                
                        );
                    ";
                
                //Preparar la sentencia
                $sentencia = $this->dblink->prepare($sql);
                
                //Asignar un valor a cada parametro
                $sentencia->bindParam(":p_id_cliente", $this->getId_cliente());
                $sentencia->bindParam(":p_apellidos", $this->getApellidos());
                $sentencia->bindParam(":p_nombres", $this->getNombres());
                $sentencia->bindParam(":p_direccion", $this->getDireccion());
                $sentencia->bindParam(":p_telef_fijo", $this->getTelef_fijo());
                $sentencia->bindParam(":p_num_cel1", $this->getNum_cel1());
                $sentencia->bindParam(":p_email", $this->getEmail());
                $sentencia->bindParam(":p_clave", $this->getClave());
                $sentencia->bindParam(":p_estado", $this->getEstado());
                $sentencia->bindParam(":p_tipo_usuario", $this->getTipo_usuario());
                
                //Ejecutar la sentencia preparada
                $sentencia->execute();
                
                
                //Actualizar el correlativo en +1
                $sql = "update correlativo set numero = numero + 1 where tabla = 'cliente'";
                $sentencia = $this->dblink->prepare($sql);
                $sentencia->execute();
                
                $this->dblink->commit();
                
                return true; //significa que todo se ha ejecutado correctamente
                
            }else{
                throw new Exception("No se ha configurado el correlativo para la tabla artículo");
//                return true;
            }
            
        } catch (Exception $exc) {
            $this->dblink->rollBack(); //Extornar toda la transacción
            throw $exc;
        }
        
        return false;
            
    }
    
    public function cargarDatosCliente($nombre) {
        try {
            $sql = "
		select 
                id_cliente,
                (nombres ||' '||apellidos ) ::character varying as nombre_completo,
                direccion,
                telef_fijo,
                num_cel1,
                email,
                (case when estado=1 then 'activo' else 'inactivo' end)::character varying as estado
                from
                cliente              
                
		where 
                estado=1 and
		    lower(nombres ||' '||apellidos ) like :p_nombre";
            $sentencia = $this->dblink->prepare($sql);
            $nombre = '%'.  strtolower($nombre).'%';
            $sentencia->bindParam(":p_nombre", $nombre);
            $sentencia->execute();
            $resultado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
            
    }
}


