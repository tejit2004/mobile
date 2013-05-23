<?php
require_once('../Connections/Login.php');
require_once('functions.php');
$errMsg = '';
$successMsg = 'false';

$efm_cli = $_REQUEST['efm_cli']; // Get username from object
$efm_postcode = $_REQUEST['efm_postcode']; // Get password from object*/
$efm_cli = str_replace(" ", "", $efm_cli);

if($efm_cli != '')
	$InputData = $efm_cli;
else	
	$InputData = $efm_postcode;

$param = "<InputData>" . $InputData . "</InputData><Email>joomla@cerberusmail.co.uk</Email><Via>NC2</Via>";

$find = $filemaker->newFindAnyCommand('PHP');
$find->setScript("TalkTalk Check Availability",$param);

try
{
	$result = $find->execute(); 
	if(!isset($result) || empty($result))
	{
		throw new Exception("There has been an expected error. Please try again.");
	}
	
	else if ($filemaker->isError($result)) 
	{		
		printf($result->getMessage());		
		exit;		
	}
}		
catch(Exception $e)
{
	echo 'Message: ' .$e->getMessage();
	die;
}

$record = $result->getFirstRecord();
$result_string = $record->getField('PHPTemp');
$result_string = htmlspecialchars_decode($result_string);

$result_string = str_replace('xmlns:schemaLocation', 'nothing', $result_string);
$result_array = xmlstr_to_array($result_string);

$two_pair = '';
$four_pair = '';

if(is_array($result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:ErrorCode']) || $result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:ErrorCode'] == 0)
{
	$successMsg = true;
}
else
{	
	$errMsg = $result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:ErrorMsg'];
}

if($successMsg == true && $errMsg == '')
{
	if($result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:TwoPairIndicative'] == "N/A")
	{
		$two_pair = 'N/A';
		$two_color = "#FF0019";
	}
	else
	{
		$two_pair = $result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:TwoPairIndicative'].' Mbps';
		$two_color = "#00B050";
	}
	
	if($result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:FourPairIndicative'] == "N/A")
	{
		$four_pair = 'N/A';
		$four_color = "#FF0019";
	}
	else
	{
		$four_pair = $result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:FourPairIndicative'].' Mbps';
		$four_color = "#00B050";
	}
	
	/*$feedback = '<table bgcolor="#eeeeee" width="100%">
				  <tr>
						<td width="45%">Input Data</td>
						<td width="5%">&nbsp;</td>
						<td width="50%"><font color="#00B050">'. $result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:InputData'] .'</font></td>                                        

				  </tr> 
				  <tr>
						<td width="45%">Lead Time </td>
						<td width="5%">&nbsp;</td>
						<td width="50%"><font color="#00B050">'.$result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:LeadTime'].'</font></td>
				  </tr>                                 
				  <tr>
						<td width="45%">2-pair Indicative Speed </td>
						<td width="5%">&nbsp;</td>
						<td width="50%"><font color="'.$two_color.'">'.$two_pair.'</font></td>
				  </tr>
				  <tr>
						<td width="45%">4-pair Indicative Speed</td>
						<td width="5%">&nbsp;</td>
						<td width="50%"><font color="'.$four_color.'">'.$four_pair.'</font></td>
				  </tr>
			   </table>';	*/
			   
	$feedback = '<h3>EFM Availability Result</h3>
				<div class="ui-grid-a ui-responsive">
					<div class="ui-block-a"><div class="ui-body ui-body-d">Input Data</div></div>
					<div class="ui-block-b"><div class="ui-body ui-body-d"><font color="#00B050">'. $result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:InputData'] .'</font></div></div>
				</div>
				<div>&nbsp</div>
				<div class="ui-grid-a ui-responsive">
					<div class="ui-block-a"><div class="ui-body ui-body-d">Lead Time</div></div>
					<div class="ui-block-b"><div class="ui-body ui-body-d"><font color="#00B050">'. $result_array['soap:Body']['ns:SearchResponse']['ns:SearchResponseDetail']['ns:LeadTime'] .'</font></div></div>
				</div>
				<div>&nbsp</div>
				<div class="ui-grid-a ui-responsive">
					<div class="ui-block-a"><div class="ui-body ui-body-d">2-pair Indicative Speed</div></div>
					<div class="ui-block-b"><div class="ui-body ui-body-d"><font color="'.$two_color.'">'. $two_pair .'</font></div></div>
				</div>
				<div>&nbsp</div>
				<div class="ui-grid-a ui-responsive">
					<div class="ui-block-a"><div class="ui-body ui-body-d">4-pair Indicative Speed</div></div>
					<div class="ui-block-b"><div class="ui-body ui-body-d"><font color="'.$four_color.'">'. $four_pair .'</font></div></div>
				</div>';		   						   
	
}
else
{
	$feedback = '<div class="ui-grid-a ui-responsive"><div class="ui-block-a">Unfortunately, no EFM services are available on this '.$type.'. Please enter another '.$type.' or contact the sales team for more information on 0845 257 1333.</div></div>';	
}

echo $feedback;

?>