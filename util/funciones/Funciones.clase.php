<?php

require_once 'json.hpack.php';

class Funciones {
    
    public static $DIRECCION_WEB_SERVICE = "https://ws-tesis-veterinaria-gllatas.herokuapp.com/webservice/";

    public static function mensaje($mensaje, $tipo, $archivoDestino="", $tiempo=0) {
        $estiloMensaje = "";

        if ($archivoDestino==""){
            $destino = "javascript:window.history.back();";
        }else{
            $destino = $archivoDestino;
        }

        $menuEntendido = '<div><a href="'.$destino.'">Entendido</a></div>';


        if ($tiempo==0){
            $tiempoRefrescar = 5;
        }else{
            $tiempoRefrescar = $tiempo;
        }

        switch ($tipo) {
            case "s":
                $estiloMensaje = "alert callout-success";
                $titulo = "Hecho";
                break;

            case "i":
                $estiloMensaje = "callout-info";
                $titulo = "Información";
                break;

            case "a":
                $estiloMensaje = "callout-warning";
                $titulo = "Cuidado";
                break;

            case "e":
                $estiloMensaje = "callout-danger";
                $titulo = "Error";
                break;

            default:
                $estiloMensaje = "callout-info";
                $titulo = "Información";
                break;
        }

        $html_mensaje = '
                <html>
                    <head>
                        <title>Mensaje del sistema</title>
                        <meta charset="utf-8">
                        <meta http-equiv="refresh" content="'.$tiempoRefrescar.';'.$destino.'">

                        <link href="../util/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
                        <!-- Theme style -->
                        <link href="../util/lte/css/AdminLTE.css" rel="stylesheet" type="text/css" />


                    </head>
                    <body>
                        <div class="containter">
                            <section class="content">
                                <div class="callout '.$estiloMensaje.'">
                                    <h4>'.$titulo.'!</h4>
                                    <p>'.$mensaje.'</p>
                                </div>
                                '.$menuEntendido.'
                            </section>
                    </body>
                </html>
            ';

        echo $html_mensaje;

        exit;
            
    }
    
    public static function imprimeJSON($estado, $mensaje, $datos){
        //header("HTTP/1.1 ".$estado." ".$mensaje);
        //header("HTTP/1.1 ".$estado);
        header('Content-Type: application/json');
        
        $response["estado"]	= $estado;
        $response["mensaje"]	= $mensaje;
        $response["datos"]	= $datos;
	
        echo json_encode($response);
    }

    public static function cargarArchivo($nombreArchivo, $ruta){
        try {
            if($nombreArchivo != ""){
                move_uploaded_file($nombreArchivo, $ruta);
            }            
        } catch (Exception $e) {
            throw $e;
        }        
    }

    public static function eliminarArchivo($nombreArchivo){
        try {
            if (file_exists($nombreArchivo)) {
                unlink($nombreArchivo);
            }     
        } catch (Exception $e) {
            throw $e;
        }        
    }

    public static function generaPDF($file='', $html='', $paper='a4', $format, $download=false) {
        require_once '../dompdf/dompdf_config.inc.php';
        try{
            $dompdf = new DOMPDF();
            $dompdf->set_paper($paper, $format);
            $dompdf->load_html($html);
            ini_set("memory_limit","32M");
            $dompdf->render();
            file_put_contents($file, $dompdf->output());        

            if ($download){
                $dompdf->stream($file);
            }
        }catch(Exception $exc){
            echo "Error: ".$exc->getMessage();
        }
    }

    public static function limpiarString($string){
        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                 "#", "@", "|", "!", "\"",
                 "·", "$", "%", "&", "/",
                 "(", ")", "?", "'", "¡",
                 "¿", "[", "^", "`", "]",
                 "+", "}", "{", "¨", "´",
                 ">", "< ", ";", ",", ":",
                 ".", " "),
            '',
            $string
        );
        return $string;
    }
}
