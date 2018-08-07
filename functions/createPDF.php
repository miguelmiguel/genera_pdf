<?php

function createPDF($pdf_folder, $template_file_name, $temp_full_path, $mapped_data){
    
    //Copy the Template file to the Result Directory
    copy($template_file_name, $temp_full_path);
    
    // add class Zip Archive
    $zip_val = new ZipArchive;
 
    //Docx file is nothing but a zip file. Open this Zip File
    if($zip_val->open($temp_full_path) == true)
    {
        // In the Open XML Wordprocessing format content is stored.
        // In the document.xml file located in the word directory.
         
        $key_file_name = 'word/document.xml';
        $message = $zip_val->getFromName($key_file_name);                
                     
        $timestamp = date('d-M-Y H:i:s');
         
        // this data Replace the placeholders with actual values
        foreach ($mapped_data as $key => $value) {
            var_dump($key . ' ' . $value);
            $message = str_replace($key, $value,  $message);
        }        
         
        //Replace the content with the new content created above.
        $zip_val->addFromString($key_file_name, $message);
        $zip_val->close();
    }
    var_dump('libreoffice --headless --convert-to pdf '.$temp_full_path.' --outdir '.$pdf_folder);
    
    shell_exec('libreoffice --headless --convert-to pdf '.$temp_full_path.' --outdir '.$pdf_folder);
    
}