<?php
ini_set("display_errors",0);
if(!session_id()) session_start();
require_once('../Connections/Login.php');
$action = $_REQUEST['action']; // We dont need action for this tutorial, but in a complex code you need a way to determine ajax action nature

if($action == 'login')
{
	/*$formData = json_decode($_REQUEST['formData'], true); // Decode JSON object into readable PHP object
	
	$username = $formData->{'username'}; // Get username from object
	$password = $formData->{'password'}; // Get password from object*/
	
	$username = $_REQUEST['username']; // Get username from object
	$password = $_REQUEST['password']; // Get password from object*/
	
	
	$Clients2ProductSets_Flag = 'No';
		
	$Lusername_Login = trim($username);
	$Lpassword_Login = trim($password);
	
	
	if($Lusername_Login != '' && $Lpassword_Login != '' && fmsCheckFirstLogin('login','index.php',$filemaker) === false)
	{
		$ContactLogin_find = $filemaker->newFindCommand('Contacts PHP');
		
		$ContactLogin_findCriterions = array('nc_Username'=>'=='.$Lusername_Login,'nc_Password'=>'=='.$Lpassword_Login);
		foreach($ContactLogin_findCriterions as $key=>$value) 
		{
			$ContactLogin_find->AddFindCriterion($key,$value);
		}
		
		$ContactLogin_result = $ContactLogin_find->execute(); 
		
		if($filemaker->isError($ContactLogin_result))
		{
			//session_start();
			//fmsTrapError($ContactLogin_result,"error.php"); 
			$_SESSION["login_login"]["user"]	= "";
			$_SESSION["login_login"]["pass"]	= "";
			$_SESSION["login_login"]["first"]	= "";
			
			unset($_SESSION["login_login"]["user"]);
			unset($_SESSION["login_login"]["pass"]);
			unset($_SESSION["login_login"]["first"]);
			unset($_SESSION["login_login"]);
			
			session_unset(); 
			$responseVar = array('ret'=>false);
			echo $json_encode = json_encode($responseVar);	
			die;	
		}
		else 
		{	
			$ContactLogin_row = current($ContactLogin_result->getRecords());
			
			foreach($ContactLogin_result->getRecords() as $ContactLogin_row)
			{ 
				$contactID = trim($ContactLogin_row->getField('_k_ContactID'));
				$clientID =  trim($ContactLogin_row->getField('_kf_ParentID')) ;
				$_SESSION["login_login"]["user"] = trim($ContactLogin_row->getField('nc_Username'));	
				$_SESSION["login_login"]["pass"] = trim($ContactLogin_row->getField('nc_Password'));	
				$_SESSION["login_login"]["clientID"] = $clientID;						
				$_SESSION["login_login"]["contactID"] = $contactID;			
				$_SESSION["login_login"]["AccountManagerEmail"] = trim($ContactLogin_row->getField('ClientsInContacts::AccountManagerEmail'));					
				$_SESSION["login_login"]["CompanyName"] = trim($ContactLogin_row->getField('CompanyName'));						
				
				$_SESSION["login_login"]["FullName"] = trim($ContactLogin_row->getField('FullName'));
				
				
				$responseVar = array('ret'=>true,'contactID'=>$contactID,'clientID'=>$clientID, 'CompanyName'=>$_SESSION["login_login"]["CompanyName"], 'FullName'=>$_SESSION["login_login"]["FullName"]);
				echo $json_encode = json_encode($responseVar);
				die;
			}

			
		}
	}
	else
	{
		$responseVar = array('ret'=>false);
		echo $json_encode = json_encode($responseVar);	
		die;	
	}
}
else if($action == 'logout')
{
	if(isset($_SESSION["login_login"]) && count($_SESSION["login_login"]) > 0)	
	{
		foreach($_SESSION["login_login"] as $key => $value)
		{
			unset($_SESSION["login_login"][$key]);
		}
		unset($_SESSION["login_login"]);
	}	
}

?>