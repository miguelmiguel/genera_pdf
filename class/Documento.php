<?php

class OutputDocumento {

    var $id;
    var $proceso;
    var $archivo_salida;
    var $estado_archivo;
    var $fecha_hora_generado;
    
    function __construct($id=NULL, $proceso=NULL, $archivo_salida=NULL, $estado_archivo=NULL,  
                         $fecha_hora_generado=NULL)
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

    function getProceso(){
        return $this->proceso;
    }
    
    function getArchivoSalida(){
        return $this->nombre_archivo_salida;
    }

    function getEstadoArchivo(){
        return $this->estado_archivo;
    }

    function getFechaHoraGenerado(){
        return $this->fecha_hora_generado;
    }
    
}

?>