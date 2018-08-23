<?php

include_once("class/Proceso.php");
include_once("class/Documento.php");
include_once("config.php");

class FachadaBD {

    private static $instance;

    public static function getInstance() {

        if( self::$instance == null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Privado se previene la creacion via new.
    private function __construct() {

    }

    // Para evitar la clonacion de este objeto.
    private function __clone() {
        throw new Exception('No se puede clonar');
    }

    public function __wakeup() {
        throw new Exception("No se puede deserializar una instancia de ". get_class($this) ." class.");
    }

    public function __sleep() {
        throw new Exception("No se puede serializar una instancia de ". get_class($this) ." class.");
    }

    function desconectar($link) {                                                              
        mysql_close($link);
    }
    
    function conectar() {
        $db = new mysqli(SERVER, DB_USER, DB_PASS, DB_NAME);
        $db->set_charset("utf8");
        return $db;
    }

    function insertarDocumento( $proceso, $archivo_salida, $estado_archivo ) {

        $db = $this->conectar();
        $id = 0;
        
        if ($stmt = $db->prepare(
                "INSERT INTO ot_documento (proceso, archivo_salida, estado_archivo, fecha_hora_generado) 
                VALUES (?,?,?,CURRENT_TIMESTAMP())") )
        {
            $stmt->bind_param('iss', $proceso, $archivo_salida, $estado_archivo);
            $stmt->execute();
            #echo "error:".$db->error;
            #var_dump("error:".$db->error);

            $id = $db->insert_id;
            $stmt->close();
        }
        
        return $id;
    }
    
    function insertarProceso( $archivo_entrada, $ruta_salida, $plantilla, $cliente, $estado_proceso) {

        $db = $this->conectar();
        $id = 0;
        
        if ($stmt = $db->prepare(
                "INSERT INTO ot_proceso (fecha_proceso, archivo_entrada, ruta_salida, plantilla, cliente, estado_proceso) 
                VALUES (CURRENT_TIMESTAMP(),?,?,?,?,?)") )
        {
            $stmt->bind_param('sssss', $archivo_entrada, $ruta_salida, $plantilla, $cliente, $estado_proceso);
            $stmt->execute();
            #echo "error:".$db->error;

            $id = $db->insert_id;
            $stmt->close();
        }
        
        return $id;
    }
    
    function consultarProceso($id){ 
        $db = $this->conectar();

        if ($stmt = $db->prepare(
            "SELECT id, fecha_proceso, archivo_entrada, ruta_salida, plantilla, cliente, estado_proceso
            FROM ot_proceso WHERE id = ?") )
        {
            // Bind your variable to replace the ?
            $stmt->bind_param('i', $clave);
            $clave = $id;

            $stmt->bind_result($id, $fecha_proceso, $archivo_entrada, $ruta_salida, $plantilla, $cliente, $estado_proceso);
            $stmt->execute();
            $stmt -> fetch();
            $result = new OutputProceso($id, $fecha_proceso, $archivo_entrada, $ruta_salida, $plantilla, $cliente, $estado_proceso);

            $stmt->close();
        }

        return $result;
    }
    
    function modificarEstadoProceso($proceso, $estado) {

        $db = $this->conectar();

        if ($stmt = $db->prepare("UPDATE ot_proceso SET estado_proceso = ? WHERE id=?") ){
            // Bind your variable to replace the ?
            $stmt->bind_param('si', $estado, $proceso->id);
            $stmt->execute();

            $result = $stmt->affected_rows;
            $stmt->close();
        }
        return $result;
    }
}

?>