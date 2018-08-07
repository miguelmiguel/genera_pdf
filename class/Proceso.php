<?php

class OutputProceso {

    var $id;
    var $fecha_proceso;
    var $archivo_entrada;
    var $plantilla;
    var $cliente;
    
    function __construct($id=NULL, $fecha_proceso=NULL, $archivo_entrada=NULL, $plantilla=NULL, $cliente=NULL)
    {

        $this->id = $id;
        $this->proceso = $proceso;
        $this->archivo_salida = $archivo_salida;
        $this->estado_archivo = $estado_archivo;
        $this->fecha_hora_generado = $fecha_hora_generado;
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

    function getPlantilla(){
        return $this->plantilla;
    }

    function getCliente(){
        return $this->cliente;
    }
    
}

?>