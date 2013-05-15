<?php
// FMStudio v2 - do not remove comment, needed for DreamWeaver support
# FileName="Connection_php_FMStudio2.htm"
# Type="FMStudio2"
# FMStudio2="true"
$path = preg_replace("#^(.*[/\\\\])[^/\\\\]*[/\\\\][^/\\\\]*$#",'\1',__FILE__);
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once('FileMaker.php');
require_once('FMStudio_v2/FMStudio_Tools.php');
//$hostname_filemaker = "10.10.0.7"; // For Filemaker 11 Development
//$hostname_filemaker = "10.9.0.19"; // For Filemaker Production
$hostname_filemaker = "10.10.0.16"; // For Filemaker 12 Development

//$hostname_filemaker = "10.9.0.232"; // For Filemaker 12 Development

date_default_timezone_set('Europe/London');

$database_filemaker = "netconnect";
$username_filemaker = "nc2";
$password_filemaker = "ghayasta12";
$filemaker = new FileMaker($database_filemaker, $hostname_filemaker, $username_filemaker, $password_filemaker);

// In case of Connecting with other databases in the script

$database_order = "Orders";
$database_action = "Actions";
?>