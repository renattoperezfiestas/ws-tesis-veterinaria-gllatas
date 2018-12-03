<?php

require_once '../datos/Conexion.clase.php';

class Cita extends Conexion {
    private $id_cita;
    private $fecha_cita;
    private $total;
    private $estado;
    private $diagnostico;
    private $peso;
    private $fecha_registro;
    private $id_mascota;
    private $temperatura;
    private $frec_cardiaca;
    private $mucosas;
    private $frec_respiratoria;
    private $tratamiento;
    private $dni_veterinario;
    
     private $detalleCita; //JSON
     function getDetalleCita() {
         return $this->detalleCita;
     }

     function setDetalleCita($detalleCita) {
         $this->detalleCita = $detalleCita;
     }

         function getId_cita() {
        return $this->id_cita;
    }

    function getFecha_cita() {
        return $this->fecha_cita;
    }

    function getTotal() {
        return $this->total;
    }

    function getEstado() {
        return $this->estado;
    }

    function getDiagnostico() {
        return $this->diagnostico;
    }

    function getPeso() {
        return $this->peso;
    }

    function getFecha_registro() {
        return $this->fecha_registro;
    }

    function getId_mascota() {
        return $this->id_mascota;
    }

    function getDni_veterinario() {
        return $this->dni_veterinario;
    }

    function setId_cita($id_cita) {
        $this->id_cita = $id_cita;
    }

    function setFecha_cita($fecha_cita) {
        $this->fecha_cita = $fecha_cita;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setDiagnostico($diagnostico) {
        $this->diagnostico = $diagnostico;
    }

    function setPeso($peso) {
        $this->peso = $peso;
    }

    function setFecha_registro($fecha_registro) {
        $this->fecha_registro = $fecha_registro;
    }

    function setId_mascota($id_mascota) {
        $this->id_mascota = $id_mascota;
    }

    function setDni_veterinario($dni_veterinario) {
        $this->dni_veterinario = $dni_veterinario;
    }
    function getTemperatura() {
        return $this->temperatura;
    }

    function getFrec_cardiaca() {
        return $this->frec_cardiaca;
    }

    function getMucosas() {
        return $this->mucosas;
    }

    function getFrec_respiratoria() {
        return $this->frec_respiratoria;
    }

    function getTratamiento() {
        return $this->tratamiento;
    }

    function setTemperatura($temperatura) {
        $this->temperatura = $temperatura;
    }

    function setFrec_cardiaca($frec_cardiaca) {
        $this->frec_cardiaca = $frec_cardiaca;
    }

    function setMucosas($mucosas) {
        $this->mucosas = $mucosas;
    }

    function setFrec_respiratoria($frec_respiratoria) {
        $this->frec_respiratoria = $frec_respiratoria;
    }

    function setTratamiento($tratamiento) {
        $this->tratamiento = $tratamiento;
    }

            public function agregar() {
        $this->dblink->beginTransaction();
        try {
            $sql = "select * from f_generar_correlativo('cita') as nc";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->execute();
            $resultado = $sentencia->fetch();
            
            if ($sentencia->rowCount()){
                $nuevoNumeroVenta = $resultado["nc"];
                $this->setId_cita($nuevoNumeroVenta);
                
                
                $sql = "
                        INSERT INTO cita
                            (
                                     id_cita,
                                     fecha_cita,
                                     total,
                                      estado,
                                       diagnostico,
                                        peso,
                                         fecha_registro, 
                                           id_mascota,
                                            dni_veterinario,
                                            temperatura,
                                            frec_cardiaca,
                                            mucosas,
                                            frec_respiratoria,
                                            tratamiento
                            )
                        VALUES 
                            (
                                     :p_id_cita,
                                     :p_fecha_cita,
                                     :p_total,
                                      :p_estado,
                                       :p_diagnostico,
                                        :p_peso,
                                         :p_fecha_registro, 
                                           :p_id_mascota,
                                            :p_dni_veterinario,
                                            :p_temperatura,
                                            :p_frec_cardiaca,
                                            :p_mucosas,
                                            :p_frec_respiratoria,
                                            :p_tratamiento
                            );
                    ";
                
                //Preparar la sentencia
                $sentencia = $this->dblink->prepare($sql);
                
                //Asignar un valor a cada parametro
                $sentencia->bindParam(":p_id_cita", $this->getId_cita());
                $sentencia->bindParam(":p_fecha_cita", $this->getFecha_cita());
                $sentencia->bindParam(":p_total", $this->getTotal());
//                $sentencia->bindParam(":p_estado","D");
                $sentencia->bindParam(":p_estado", $this->getEstado());
                $sentencia->bindParam(":p_diagnostico", $this->getDiagnostico());
                $sentencia->bindParam(":p_peso", $this->getPeso());
                $sentencia->bindParam(":p_fecha_registro", $this->getFecha_registro());
                $sentencia->bindParam(":p_id_mascota", $this->getId_mascota());
                $sentencia->bindParam(":p_dni_veterinario", $this->getDni_veterinario());
                $sentencia->bindParam(":p_temperatura", $this->getTemperatura());
                $sentencia->bindParam(":p_frec_cardiaca", $this->getFrec_cardiaca());
                $sentencia->bindParam(":p_mucosas", $this->getMucosas());
                $sentencia->bindParam(":p_frec_respiratoria", $this->getFrec_respiratoria());
                $sentencia->bindParam(":p_tratamiento", $this->getTratamiento());
                
                
                //Ejecutar la sentencia preparada
                $sentencia->execute();
                
                
                /*INSERTAR EN LA TABLA VENTA_DETALLE*/
                $detalleVentaArray = json_decode( $this->getDetalleCita() ); //Convertir de formato JSON a formato array
                
                
                $item = 0;
                
                foreach ($detalleVentaArray as $key => $value) { //permite recorrer el array
                    
                    $sql = "select stock, descripcion from producto_servicio where id_producto_servicio = :p_id_producto_servicio";
                    $sentencia = $this->dblink->prepare($sql);
                    $sentencia->bindParam(":p_id_producto_servicio", $value->id_producto_servicio);
		    $sentencia->execute();
                    $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
                    if ($resultado["stock"] < $value->cantidad){
                        throw new Exception("No hay stock suficiente" . "\n" . "Producto: " . $value->id_producto_servicio . " - " . $resultado["descripcion"] . "\n" . "Stock actual: " . $resultado["stock"] . "\n" . "Cantidad de venta: " . $value->cantidad);
                    }
                    

                    $sql = "
                            INSERT INTO cita_detalle
                            (       id_cita, 
                                    id_producto_servicio,
                                    item,                                      
                                    precio, 
                                    descripcion, 
                                    cantidad
                                    
                            )
                                VALUES 
                            (
                                    :p_id_cita, 
                                    :p_id_producto_servicio,
                                    :p_item,                                      
                                    :p_precio, 
                                    :p_descripcion, 
                                    :p_cantidad
                            )
                        ";
                    
                    
                    //Preparar la sentencia
                    $sentencia = $this->dblink->prepare($sql);
                    
                    $item++;
                    
                    //Asignar un valor a cada parametro
                    $sentencia->bindParam(":p_id_cita", $this->getId_cita());
                    $sentencia->bindParam(":p_id_producto_servicio", $value->id_producto_servicio);
                    $sentencia->bindParam(":p_item", $item);
                    $sentencia->bindParam(":p_precio", $value->precio);
                    $sentencia->bindParam(":p_descripcion", $value->descripcion);
                    $sentencia->bindParam(":p_cantidad", $value->cantidad);
                    
                   
                    
                    //Ejecutar la sentencia preparada
                    $sentencia->execute();
                    
                    
                    /*ACTUALIZAR EL STOCK DE CADA ARTICULO VENDIDO*/
                    $sql = "update producto_servicio 
                            set stock = stock - :p_cantidad 
                            where id_producto_servicio = :p_id_producto_servicio";
                    
                    $sentencia = $this->dblink->prepare($sql);
                    $sentencia->bindParam(":p_id_producto_servicio", $value->id_producto_servicio);
                    $sentencia->bindParam(":p_cantidad", $value->cantidad);
                    $sentencia->execute();
                    /*ACTUALIZAR EL STOCK DE CADA ARTICULO VENDIDO*/
                    
                    
                }
                /*INSERTAR EN LA TABLA VENTA_DETALLE*/
                
                
                //Actualizar el correlativo en +1
                $sql = "update correlativo set numero = numero + 1 where tabla = 'cita'";
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
    public function listar_app( ) {
        try {
            $sql = "
               select 
                c.id_cita,
                c.fecha_cita,                
                m.nombre as mascota,
                c.id_mascota,                
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
