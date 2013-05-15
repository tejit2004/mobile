<?php
// FMStudio v2 - do not remove comment, needed for DreamWeaver support
# FileName="Connection_php_FMStudio2.htm"
# Type="FMStudio2"
# FMStudio2="true"
$path = preg_replace("#^(.*[/\\\\])[^/\\\\]*[/\\\\][^/\\\\]*$#",'\1',__FILE__);
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once('FileMaker.php');
require_once('FMStudio_v2/FMStudio_Tools.php');
$hostname_filemaker = "10.10.0.7";
$database_filemaker = "NetConnect";
$username_filemaker = "Tejas Trivedi";
$password_filemaker = "Tjsvd@123";
$Services = new FileMaker($database_filemaker, $hostname_filemaker, $username_filemaker, $password_filemaker); 
?>