<?php ob_start(); SESSION_START();
$condb = new mysqli("localhost","root","","sooq") or die ("not connect");
///$condb = new mysqli("localhost","abdalmonem","m99ur1","arbsb") or die ("not connect");

    if($condb->connect_error)
    {
    die('Connect Error:'.$condb->connect_error);
    } 

$condb->query("set character_set_server='utf8'");
$condb->query("set names 'utf8'");


/// Define Abstract Path
define("ABSPATH",dirname(__FILE__));
define("DevolopersAuth","43232RRELKLX678");

?>
