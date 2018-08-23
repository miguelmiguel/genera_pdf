<?php

class OutputProceso {

    var $id;
    var $fecha_proceso;
    var $archivo_entrada;
    var $ruta_salida;
    var $plantilla;
    var $cliente;
    var $estado_proceso;
    
    function __construct($id=NULL, $fecha_proceso=NULL, $archivo_entrada=NULL, $ruta_salida=NULL, $plantilla=NULL, $cliente=NULL, $estado_proceso=NULL)
    {

        $this->id = $id;
        $this->fecha_proceso = $fecha_proceso;
        $this->archivo_entrada = $archivo_entrada;
        $this->ruta_salida = $ruta_salida;
        $this->plantilla = $plantilla;
        $this->cliente = $cliente;
        $this->estado_proceso = $estado_proceso;
    }

    function getId(){
       return $this->id;
    }

    function getFechaProceso(){
        return $this->fecha_proceso;
    }
    
    function getArchivoEntrada(){
        return $this->archivo_entrada;
    }
    
    function getRutaSalida(){
        return $this->ruta_salida;
    }

    function getPlantilla(){
        return $this->plantilla;
    }

    function getCliente(){
        return $this->cliente;
    }

    function getEstadoProceso(){
        return $this->estado_proceso;
    }
    
}

?>