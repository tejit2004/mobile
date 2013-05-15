<?php
ini_set("display_errors",0);
if(!session_id()) session_start();
require_once('../Connections/Login.php');
$action = $_REQUEST['action']; // We dont need action for this tutorial, but in a complex code you need a way to determine ajax action nature

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
		echo "false||Incorrect user name or password.";			
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
			
			$Flag_NetSTATS = trim($ContactLogin_row->getField('Flag_NetSTATS'));
			$Flag_secondaryNetSTATS = trim($ContactLogin_row->getField('Flag_secondaryNetSTATS'));
			
			if($Flag_NetSTATS == 'Yes')
			{
				$_SESSION["login_login"]["Enable_NetSTATS"] = 'Yes';
				$_SESSION["login_login"]["nstUsername"] = trim($ContactLogin_row->getField('nst_Username'));
				$_SESSION["login_login"]["nstPassword"] = trim($ContactLogin_row->getField('nst_Password'));
			}
			else if($Flag_secondaryNetSTATS == 'Yes')
			{
				$_SESSION["login_login"]["Enable_NetSTATS"] = 'Yes';
				$_SESSION["login_login"]["nstUsername"] = trim($ContactLogin_row->getField('ContactsInNetCONNECTforPrimaryNetstats::nst_Username'));
				$_SESSION["login_login"]["nstPassword"] = trim($ContactLogin_row->getField('ContactsInNetCONNECTforPrimaryNetstats::nst_Password'));
			}
			else
			{
				$_SESSION["login_login"]["Enable_NetSTATS"] = '';
			}
			
			$_SESSION["login_login"]["EditlineProfile"] = trim($ContactLogin_row->getField('Flag_EditLineProfile'));
			$_SESSION["login_login"]["FullName"] = trim($ContactLogin_row->getField('FullName'));
			$_SESSION["login_login"]["flag_OneBill"] = trim($ContactLogin_row->getField('ClientsInContacts::acc_flagOneBill'));
			$_SESSION["login_login"]["Flag_EnabledForNetCONNECTOrders"] = trim($ContactLogin_row->getField('Flag_EnabledForNetCONNECTOrders'));
			$_SESSION["login_login"]["Flag_EditLineProfile"] = trim($ContactLogin_row->getField('Flag_EditLineProfile'));
			$_SESSION["login_login"]["Flag_DSLReseller"] = trim($ContactLogin_row->getField('ClientsInContacts::Flag_DSLReseller'));
			$_SESSION["login_login"]["Flag_APMOrders"] = trim($ContactLogin_row->getField('Flag_APMOrders'));
			$_SESSION["login_login"]["flag_APMAdmin"] = trim($ContactLogin_row->getField('Flag_APMAdmin'));
			$_SESSION["login_login"]["APM_InvoiceMode"] = trim($ContactLogin_row->getField('ClientsInContacts::APM_InvoiceMode'));
			
			if($ContactLogin_row->getField('Flag_Admin') == 'Yes' || $ContactLogin_row->getField('Flag_SuperAdmin') == 'Yes')
			{
				$_SESSION["login_login"]["Flag_AdminUser"] = trim($ContactLogin_row->getField('Flag_Admin'));
				$_SESSION["login_login"]["Flag_SuperAdmin"] = trim($ContactLogin_row->getField('Flag_SuperAdmin'));
				$_SESSION["login_login"]["Change_User"] = 'Yes';
			}
			
			if(trim($ContactLogin_row->getField('nc2_Login')) == '')
			{
				$date = date('d/m/y H:i:s');
				$ContactLogin_row->setField('nc2_Login', $date);  
				$result = $ContactLogin_row->commit();
			}
			
			foreach(fmsRelatedSet($ContactLogin_row,'Clients2ProductSets') as $Clients2ProductSets)
			{ 
				if($Clients2ProductSets->getField('Clients2ProductSets::_kf_ProductSetID') == '000034')
				{
					$Clients2ProductSets_Flag = 'Yes';														
				}
				
				if($Clients2ProductSets->getField('Clients2ProductSets::_kf_ProductSetID') == '000033')
				{
					$_SESSION["login_login"]["DNS"] = 'Yes';														
				}
			}
			
			$installation_markup = $ContactLogin_row->getField('ClientsInContacts::Ethernet_Installation_Markup');
			
			$min_margin = $ContactLogin_row->getField('ClientsInContacts::Ethernet_Min_Margin');
			
			$rental_markup = $ContactLogin_row->getField('ClientsInContacts::Ethernet_Rental_Markup');
			
			if($installation_markup != '' && $min_margin != '' && $rental_markup != '' && $Clients2ProductSets_Flag == 'Yes')
			{
				$_SESSION["login_login"]["Ethernet_Quotes"] = 'Yes';
			}
			
		}
		echo "true";
		exit;
	}
}
else
{
	echo "false||Incorrect user name or password.";			
}


?>