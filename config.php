<?php
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');

define("ROOT", __DIR__ . DIRECTORY_SEPARATOR );

$conf = array();

if ($argc > 1){
    if (file_exists($argv[1]) && is_readable($argv[1]))
    {
        $conf = parse_ini_file($argv[1],TRUE);
    }
    
}

if (!empty($conf)) {
    $gen_conf = $conf['CONF GENERAL'];
}
else{
    $gen_conf = NULL;
}

$decimal_symbol = ",";
$milliard_symbol = ".";
$currency_symbol = "$";
$date_config = "d/m/Y";
if ( $gen_conf ){
    
    if(array_key_exists("separador_decimal",$gen_conf)){
        define("DECIMAL_SYMBOL", $gen_conf["separador_decimal"]);
    }
    else{
        define("DECIMAL_SYMBOL", $decimal_symbol);
    }
    
    if(array_key_exists("separador_millares",$gen_conf)){
        define("MILLIARD_SYMBOL", $gen_conf["separador_millares"]);
    }
    else{
        define("MILLIARD_SYMBOL", $milliard_symbol);
    }
    
    if(array_key_exists("simbolo_moneda",$gen_conf)){
        define("CURRENCY_SYMBOL", $gen_conf["simbolo_moneda"]);
    }
    else{
        define("CURRENCY_SYMBOL", $currency_symbol);
    }
    
    if(array_key_exists("formato_fecha",$gen_conf)){
        define("DATE_CONFIG", $gen_conf["formato_fecha"]);
    }
    else{
        define("DATE_CONFIG", $date_config);
    }
}
else{
    define("DECIMAL_SYMBOL", $decimal_symbol);
    define("CURRENCY_SYMBOL", $currency_symbol);
    define("MILLIARD_SYMBOL", $milliard_symbol);
    define("DATE_CONFIG", $date_config);
}


if ( $gen_conf && array_key_exists("base_datos_app",$gen_conf) && array_key_exists("user_db",$gen_conf) && 
        array_key_exists("pass_db",$gen_conf) && array_key_exists("server_db",$gen_conf) )
{
    define("DB_USER", $gen_conf["user_db"] );
    define("DB_PASS", $gen_conf["pass_db"] );
    define("DB_NAME", $gen_conf["base_datos_app"] );
    define("SERVER", $gen_conf["server_db"] );
}
else
{

}