<?php
//index.php
$debug=true;
if($debug){
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
}
//require_once(dirname(__DIR__) . "/db.php");
require_once(dirname(__DIR__) . "/include/functions.php");

displayPreview();
?>