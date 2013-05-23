<?php
if(!session_id()) session_start();
require_once('../Connections/Login.php');
require_once('functions.php');
$type	= isset($_REQUEST['type'])?trim($_REQUEST['type']):'';
$inventoryid 	= isset($_REQUEST['add_inventoryid'])?trim($_REQUEST['add_inventoryid']):'';
$clientID  = isset($_REQUEST['clientID'])?trim($_REQUEST['clientID']):'';

$errMsg = '';
$successMsg = '';

if($type == 'save')
{
	
	$add_srno 			= isset($_REQUEST['add_srno'])?trim($_REQUEST['add_srno']):'';
	$add_name 			= isset($_REQUEST['add_name'])?trim($_REQUEST['add_name']):'';
	$add_make_model 	= isset($_REQUEST['add_make_model'])?trim($_REQUEST['add_make_model']):'';
	$add_description = isset($_REQUEST['add_description'])?trim($_REQUEST['add_description']):'';
	$subject = isset($_REQUEST['subject'])?trim($_REQUEST['subject']):'';	

	$param = "<ClientID>" . $clientID . "</ClientID><Subject>" . $subject . "</Subject><Description>" . $add_description . "</Description><InventoryID>" . $_POST["add_inventoryid"] . "</InventoryID><UserName>" . $_SESSION["login_login"]["user"] . "</UserName><FileName>". $name . "</FileName><Via>NC2</Via><Script>nc Submit New Case</Script>";
	
	$find = $filemaker->newFindAnyCommand('PHP');
	$find->setScript("MasterScript for PHP",$param);
	$result = $find->execute();
	$record = $result->getFirstRecord();
	$result_string = $record->getField('PHPTemp');		
	$result_arr = explode("||", $result_string);
	
	if($result_arr[0] == true)
	{
	}
	
}


else if($type == 'fetch')
{
	$param = "<ClientID>" . $clientID . "</ClientID>";
	
	$find = $filemaker->newFindAnyCommand('PHP');
	$find->setScript("nc get Inventory ValueList PHP",$param);
	$result = $find->execute();
	$record = $result->getFirstRecord();
	$result_string = $record->getField('PHPTemp');
	echo $result_string;
}
?>
