
<?php
require_once '../negocio/Venta.clase.php';
require_once '../util/funciones/Funciones.clase.php';
require_once '../datos/Conexion.clase.php';
class Venta extends Conexion{
    
    private $codigoTipoComprobante;
    private $numeroSerie;
    private $codigoCliente;
    private $fechaVenta;
    private $codigoUsuario;
    
    private $detalleVenta; //JSON
    
    
    function getCodigoTipoComprobante() {
        return $this->codigoTipoComprobante;
    }

    function getNumeroSerie() {
        return $this->numeroSerie;
    }

    function getCodigoCliente() {
        return $this->codigoCliente;
    }

    function getFechaVenta() {
        return $this->fechaVenta;
    }

    function getCodigoUsuario() {
        return $this->codigoUsuario;
    }

    function getDetalleVenta() {
        return $this->detalleVenta;
    }

    function setCodigoTipoComprobante($codigoTipoComprobante) {
        $this->codigoTipoComprobante = $codigoTipoComprobante;
    }

    function setNumeroSerie($numeroSerie) {
        $this->numeroSerie = $numeroSerie;
    }

    function setCodigoCliente($codigoCliente) {
        $this->codigoCliente = $codigoCliente;
    }

    function setFechaVenta($fechaVenta) {
        $this->fechaVenta = $fechaVenta;
    }

    function setCodigoUsuario($codigoUsuario) {
        $this->codigoUsuario = $codigoUsuario;
    }

    function setDetalleVenta($detalleVenta) {
        $this->detalleVenta = $detalleVenta;
    }


    
    public function agregar() {
        try {
            $sql = "select * from f_registrar_venta(:p_tipo_com, :p_serie, :p_codigo_cliente, :p_fecha, :p_codigo_usuario, :p_detalle_json) as fv";
            $sentencia = $this->dblink->prepare($sql);
            $sentencia->bindParam(":p_tipo_com", $this->getCodigoTipoComprobante());
            $sentencia->bindParam(":p_serie", $this->getNumeroSerie());
            $sentencia->bindParam(":p_codigo_cliente", $this->getCodigoCliente());
            $sentencia->bindParam(":p_fecha", $this->getFechaVenta());
            $sentencia->bindParam(":p_codigo_usuario", $this->getCodigoUsuario());
            $sentencia->bindParam(":p_detalle_json", $this->getDetalleVenta());
            $sentencia->execute();
            $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (Exception $exc) {
            throw $exc;
        }
    }
    
   
    
    
    
}
