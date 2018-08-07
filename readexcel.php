<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
//require __DIR__ . '/../Header.php';
//$inputFileName = __DIR__ . '/sampleData/example1.xls';
$inputFileName = 'modelo_demanda_externa.xlsx';

$inputFileType = IOFactory::identify($inputFileName);

$reader = IOFactory::createReader($inputFileType);
$spreadsheet = $reader->load($inputFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
var_dump($sheetData);
