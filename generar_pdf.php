<?php
include_once("functions/readsheet.php");
include_once("functions/createPDF.php");
include_once("functions/pseudorandom.php");
include_once("FachadaBD.php");
include_once("config.php");

function normalizePath($path) {
    return array_reduce(explode('/', $path), create_function('$a, $b', '
        if($a === 0)
                $a = "/";

        if($b === "" || $b === ".")
                return $a;

        if($b === "..")
                return dirname($a);

        return preg_replace("/\/+/", "/", "$a/$b");
    '), 0);
}

$start_time = date("Y-m-d H:i:s");

$config = array();

if ($argc > 1){
    if (file_exists($argv[1]) && is_readable($argv[1]))
    {
        $config = parse_ini_file($argv[1],TRUE);
    }
}
else{
    exit("NO SE INGRESO ARCHIVO DE CONFIGURACION EN LOS ARGUMENTOS\n");
}

if (!empty($config)) {
    $mapping_variables = $config['MAPEOS'];
    $general_config = $config['CONF GENERAL'];
    $pdf_filename_format = $config['FORMATO_NOMBRE_PDF'];
}
else{
    $mapping_variables = NULL;
    $general_config = NULL;
    $pfd_filename_format = array('fijo_1'=>'result','index'=>'');
}

// var_dump($mapping_variables);
// var_dump($general_config);
// var_dump($pdf_filename_format);


if ($general_config != NULL && $mapping_variables != NULL){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        var_dump('This is a server using Windows!');
        if (array_key_exists("soffice_path",$general_config)){
            $soffice_path = $general_config["soffice_path"];
            if (file_exists($soffice_path) && is_readable($soffice_path) && !is_dir($soffice_path) && is_executable($soffice_path) ){
                var_dump('"soffice_path" ' . $soffice_path . ' EXISTE');
            }
            else{
                var_dump('"soffice_path" ' . $soffice_path . ' NO EXISTE O NO PUEDE EJECUTARSE');
                exit();
            }
        }
        else{
            var_dump('"soffice_path" NO ESTA DEFINIDO EN EL ARCHIVO DE CONFIG');
            exit();
        }
    }
    else{
        var_dump('This is a server not using Windows!');
        $soffice_path = NULL;
    }
    
    if (array_key_exists("cliente_archivo",$general_config)){
        $cliente_archivo = $general_config["cliente_archivo"];
    }
    else{
        $cliente_archivo = NULL;
    }

    if (array_key_exists("ruta_in",$general_config)){
        $in_folder_name = $general_config["ruta_in"];
        if (!file_exists($in_folder_name) || !is_dir($in_folder_name)){
            var_dump('"ruta_in" ' . $in_folder_name . ' NO EXISTE ESE DIRECTORIO');
            exit();
        }
        elseif (!is_readable($in_folder_name)){
            var_dump('"ruta_in" ' . $in_folder_name . ' NO PUEDE LEERSE ESE DIRECTORIO');
            exit();
        }
    }
    else{
        $in_folder_name = ROOT;
    }
    
    if (array_key_exists("ruta_out",$general_config)){
        $out_folder_name = $general_config["ruta_out"];
        if (!file_exists($out_folder_name))
        {
            $created = mkdir($out_folder_name);
            if (!$created){
                exit('"ruta_out" '. $out_folder_name . " NO PUDO SER CREADO ESTE DIRECTORIO\n");
            }
        }  
        elseif (!is_dir($out_folder_name)){
            exit('"ruta_out" ' . $out_folder_name . " NO ES UN DIRECTORIO\n");
        }
        elseif (!is_readable($out_folder_name)){
            exit('"ruta_out" ' . $out_folder_name . " NO PUEDE LEERSE ESTE DIRECTORIO\n");
        }
        elseif (!is_writable($out_folder_name)){
            exit('"ruta_out" ' . $out_folder_name . " NO SE PUEDE ESCRIBIR EN ESTE DIRECTORIO\n");
        }
    }
    else{
        #$temp_folder = "temp_" . generateRandomString();
        $out_folder_name = ROOT . "pdf_results";
    }
    
    
    
    if (array_key_exists("archivo_bd",$general_config)){
        $input_file_name =  $in_folder_name . DIRECTORY_SEPARATOR . $general_config["archivo_bd"];
        if (file_exists($input_file_name) && is_readable($input_file_name) && !is_dir($input_file_name) ){
            $input_data = readSheetFile(realpath($input_file_name));
            #var_dump($input_data);
            $mapped_data = mapSheetData($mapping_variables, $input_data, $pdf_filename_format);
            #var_dump($mapped_data);
        }
        else{
            exit('"archivo_bd" ' . $input_file_name . " NO EXISTE O NO SE PUEDE LEER ESTE ARCHIVO\n");
        }
    }

    if (array_key_exists("plantilla",$general_config)){
        $template_file_name = $in_folder_name . DIRECTORY_SEPARATOR . $general_config["plantilla"];
        if (!file_exists($template_file_name) || !is_readable($template_file_name)  && !is_dir($template_file_name) ){
            exit('"plantilla" ' . $template_file_name . " NO EXISTE O NO SE PUEDE LEER ESTE ARCHIVO\n");
        }
    }
    
    # TODO: Define format when pdf filename format is missing
    if (array_key_exists("formato_nombres_variables",$general_config)){
        var_dump($general_config["formato_nombres_variables"]);
    }
    
    $ignore_first_line = TRUE;
    if (array_key_exists("cabecera",$general_config)){
        $header_on_file = $general_config["cabecera"];
        if ( $header_on_file == 'NO'){
            $ignore_first_line = FALSE;
        }
    }
    
}
else{
    exit("EL ARCHIVO DE CONFIGURACION " . $argv[1] . "ESTA INCOMPLETO Y NO PUEDE SER PROCESADO\n");
}

if (isset($mapped_data)){
    
    $pdf_folder = $out_folder_name;
    $temp_folder = $out_folder_name . DIRECTORY_SEPARATOR . "temp_" . generateRandomString();
    
    $fachada = FachadaBD::getInstance();
     
    if (!file_exists($temp_folder))
    {
        mkdir($temp_folder);
    }  
    
    if (!file_exists($pdf_folder))
    {
        mkdir($pdf_folder);
    }  
    
    $proceso = $fachada->insertarProceso($input_file_name, $template_file_name, $cliente_archivo);
    var_dump( "ID PROCESO: ".$proceso);
    
    $first = TRUE;
    $created_pdf = 0;
    $not_created_pdf = 0;
    foreach ($mapped_data as $key => $mapped_row){
        
        if($first){
            $first = FALSE;
            if ($ignore_first_line){
                continue;
            }
        }

        $fileName = "results_" . $key . ".docx";
        $pdfName = "results_" . $key . ".pdf";
    
        if (array_key_exists('$filename',$mapped_row)){
            $fileName = $mapped_row['$filename'] . ".docx";
            $pdfName = $mapped_row['$filename'] . ".pdf";
            unset($mapped_row['$filename']);
        }
        
        $full_path = realpath($temp_folder) . DIRECTORY_SEPARATOR . $fileName;
        $pdf_path = realpath($pdf_folder) . DIRECTORY_SEPARATOR . $pdfName;
        
        $pdf_created = createPDF(realpath($pdf_folder), realpath($template_file_name), $full_path, $mapped_row, $soffice_path);
        var_dump("PDF PATH: " . $pdf_path);
        if ( file_exists( $pdf_path ) ) {
            $result = $fachada->insertarDocumento( $proceso, $pdfName, "CREADO");
            var_dump( "ID documento: " . $result . " CREADO");
            $created_pdf++;
    
        }
        else{
            $result = $fachada->insertarDocumento( $proceso, $pdfName, "ERROR: NO CREADO");
            var_dump( "ID documento: " . $result . " NO CREADO");
            $not_created_pdf++;
        }
        var_dump(file_exists( $pdf_path ) );
        
    }
    
    array_map('unlink', glob($temp_folder . DIRECTORY_SEPARATOR . "*"));
    rmdir($temp_folder);
    
    var_dump('CREADOS: ' . $created_pdf);
    var_dump('NO CREADOS: ' . $not_created_pdf);
    
    $end_time = date("Y-m-d H:i:s");
    var_dump("Start: " . $start_time);
    var_dump("End: " . $end_time);
    
}

?>