<?php
require_once('../Connections/Login.php');
require_once('functions.php');
$errMsg = '';
$successMsg = 'false';
$general_error = 'Not able to fetch the result';

$Name			= isset($_REQUEST['name'])?trim($_REQUEST['name']):'';
$CompanyName 	= isset($_REQUEST['company'])?trim($_REQUEST['company']):'';
$CompanyTelNo 	= isset($_REQUEST['telephone'])?trim($_REQUEST['telephone']):'';
$Email	 		= isset($_REQUEST['email'])?trim($_REQUEST['email']):'';

$telno = str_replace(" ", "", $CompanyTelNo);

$param = "<dsl>" . $telno . "</dsl>";
		
$find = $filemaker->newFindAnyCommand('PHP');
$find->setScript("Check Line Speed for Avatar",$param);

try
{
	$result = $find->execute();
			
	$http_code = $filemaker->_impl->getHTTPCode();
		
	if($http_code != 200)
	{
		$BE50thPercSpeed = $general_error;
	}
		
	else if(!isset($result) || empty($result))
	{
		throw new Exception("There has been an expected error. Please try again.");
	}	
	else if (FileMaker::isError($result))
	{
		printf("Error %s: %s\n", $result->getCode());
		"<br>";
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


$BE50thPercSpeed = ExtractString($result_string,'&lt;downspeed&gt;','&lt;/downspeed&gt;');
$UPSPEED = ExtractString($result_string,'&lt;upspeed&gt;','&lt;/upspeed&gt;');
$BEstatus = ExtractString($result_string,'&lt;status&gt;','&lt;/status&gt;');

if($BEstatus == 0)
{
	$BEResult = 'PASS';
}
else if($BEstatus == 1 || $BEstatus == 8)
{
	$BEResult = 'MAC';
}
else
{
	$BEResult = 'FAIL';
}

	/* ====================== SamKnows API Ends ===================================*/
   
  
   /* ======================================= Invoke Cable & Wireless Script ==========================*/

$url = "http://nc.cerberusnetworks.co.uk/cwchecker.php?cli=".$telno;
$ch = curl_init();			

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);	
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$result = curl_exec($ch);

$FTTC_string = '';
$WBC_string = '';

if(!curl_errno($ch))
{
	if(substr($result,0,5) == '<?xml')
	{
$string = <<<XML
$result
XML;
		$xml = simplexml_load_string(trim($string));
		$xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
		
		foreach ($xml->xpath('//LineCheckResponse') as $item)
		{
			$array_FTTC['RAG'] = (string)$item->DNChecker->ADSLCheckerOutput->WBCFTTC->RAG;
			$array_FTTC['DownSpeed'] = (string)$item->DNChecker->ADSLCheckerOutput->WBCFTTC->DownSpeed;
			$array_FTTC['UpSpeed'] = (string)$item->DNChecker->ADSLCheckerOutput->WBCFTTC->UpSpeed;
			$array_FTTC['ReadyDate'] = (string)$item->DNChecker->ADSLCheckerOutput->WBCFTTC->ReadyDate;
			$array_FTTC['ExchState'] = (string)$item->DNChecker->ADSLCheckerOutput->WBCFTTC->ExchState;
			
			$tech_code = (string)$item->technologyCode;
			$tech_message = (string)$item->technologyMessage;
			$LINE = (string)$item->lineLength;
			$PC = (string)$item->postCode;
			$DSLNO = (string)$item->lineNumber;
			$NAME = (string)$item->exchangeName;
			$CODE = (string)$item->exchangeCode;
			
			$adsl_max_status = (string)$item->DNChecker->ADSLCheckerOutput->Max->RAG;
			$adsl_max_exchange = (string)$item->DNChecker->ADSLCheckerOutput->Max->ExchState;
			$adsl_max_speed = (string)$item->DNChecker->ADSLCheckerOutput->Max->Speed;
			
			$SPEED = '';
			if($adsl_max_status == 'G' && $adsl_max_exchange == 'E')
			{
				if($tech_code == 'A' || $tech_code == 'L')
				{
					$BTResult = 'MAC';
					$SPEED = $adsl_max_speed;
				}				  
				else if($tech_code == 'Z')
				{
					$BTResult = 'PASS';
					$SPEED = $adsl_max_speed;
				}
				else
				{
					$BTResult = 'FAIL';
				}

			}
			else
			{
				$BTResult = 'FAIL';
			}
			
			$array_WBC['RAG'] = (string)$item->DNChecker->ADSLCheckerOutput->WBC->RAG;
			$array_WBC['AnnexMRAG'] = (string)$item->DNChecker->ADSLCheckerOutput->WBC->AnnexMRAG;
			$array_WBC['ExchState'] = (string)$item->DNChecker->ADSLCheckerOutput->WBC->ExchState;
		}
			
		$exchange_array = array("E" => "Enabled" , "F" => "Exchanges under Future review", "H" => "Exchange Reviewed but Not Viable (Uses Not planned Message Set)", "L" => "Exchange Activate - Exchange Live", "N" => "Not planned", "P" => "Planned (See ReadyDate Parameter for Specific Date)", "R" => "Part of the Roll-Out programme", "S" => "Exchange Activate - Exchange Planned(See ReadyDate Parameter for Specific Date)");
		
		$FTTC_availability = '';
		$WBC_availability = '';
		
		if(isset($array_FTTC) && count($array_FTTC) > 0)
		{
			if(strtoupper($array_FTTC['RAG']) == 'G' && strtoupper($array_FTTC['ExchState']) == 'E')
			{
				
				  if($tech_code == 'A' || $tech_code == 'L')
				  {
					  $FTTC_availability .= "Available";
					  $FTTC = 'MAC';
				  }				  
				  else
				  {
					  $FTTC_availability .= "Available";
				  }	
					
				  $FTTC_up = $array_FTTC['UpSpeed'];
				  $FTTC_down = $array_FTTC['DownSpeed'];						
			}
			else if(strtoupper($array_FTTC['RAG']) == 'G' && (strtoupper($array_FTTC['ExchState']) == 'P' || strtoupper($array_FTTC['ExchState']) == 'S'))
			{
				$FTTC_availability .= "Not available";
				$FTTC_ReadyDate = $array_FTTC['ReadyDate'];
				$FTTC_Exchange = 'Pending';
			}
			else
			{
				$FTTC_availability .=	'Not available';
			}
		}
		else
		{
			$FTTC_availability .= 'Not available';
		}
		
		if(isset($array_WBC) && count($array_WBC) > 0)
		{
			if(isset($array_WBC['RAG']) && strtoupper($array_WBC['RAG']) == 'G')
			{
				$WBC_availability .=	'Available';
			}
			else
			{
				$WBC_availability .=	'Not available';
			}
			if(isset($array_WBC['AnnexMRAG']) && strtoupper($array_WBC['AnnexMRAG']) == 'G')
			{
				$WBC_AnnexM = 'Available';
			}
			else
			{
				$WBC_AnnexM = 'Not available';
			}
		}
		else
		{
			$WBC_availability .=	'Not avaialble';
		}		
	}
	else
	{
		$FTTC_availability = 'no service';
		$WBC_availability = 'no service';
	}
	$FTTC_speed = '';
	$WBC_AnnexM_Availability = '';
	
	if(isset($array_FTTC['ExchState']) && $array_FTTC['ExchState'] == 'E')
	{
		if(isset($array_FTTC['DownSpeed']) && $array_FTTC['DownSpeed'] != 0)
		{
			$FTTC_speed = '<tr><td>FTTC Download*:</td><td>'.($array_FTTC['DownSpeed'] / 1000).'Mbps</td></tr><tr><td>FTTC Upload*:</td><td>'.($array_FTTC['UpSpeed'] / 1000).'Mbps</td></tr>';
		}
		else
		{
			$FTTC_speed = '<tr><td>FTTC Download:</td><td>N/A</td></tr><tr><td>FTTC Upload:</td><td>N/A</td></tr>';
		}
	}
	else if(isset($array_FTTC['ExchState']) && ($array_FTTC['ExchState'] == 'P' || $array_FTTC['ExchState'] == 'S'))
	{
		$FTTC_speed = '<tr><td>Exchange State:</td><td>Pending</td></tr><tr><td>Ready Date:</td><td>'.$FTTC_ReadyDate.'</td></tr>';				
	}
	
	if(isset($WBC_AnnexM))
	{
		$WBC_AnnexM_Availability = '<tr><td>WBC AnnexM:</td><td>'.$WBC_AnnexM.'</td></tr>';
	}
}// End of if(!curl_errno($ch))
else
{
	$FTTC_availability = 'no service';
	$WBC_availability = 'no service';
}
curl_close($ch);

/* ======================================= Invoke Cable & Wireless Script Ends ==========================*/	   


/* ====================== To get the Address Fields Calling SamKnows API ===================================*/

$url = "http://api.samknows.com/checker.do?user=checker@cerberusnetworks.co.uk&pass=stonewall12&checks=adsl,adslmax,bulldog,tiscali&options=adslmaxlinecheck&phone=".$telno;
$ch = curl_init();
	
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);	
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$result = curl_exec($ch);   

if(!curl_errno($ch))
{
	if(substr($result,0,5) == '<?xml')
	{
$string = <<<XML
$result
XML;
		$xml = simplexml_load_string(trim($string));
		foreach ($xml->xpath('//Exchange') as $item)
		{
			
			$ADDRESS1 = (string)$item->Address1;
			$ADDRESS2 = (string)$item->Address2;
			$ADDRESS3 = (string)$item->Address3;
			$ADDRESS4 = (string)$item->Address4;
			$POSTCODE = (string)$item->Postcode;
		}
		
		if($xml->xpath('//Checks/Check[@id="bulldog"]'))
		{
			foreach ($xml->xpath('//Checks/Check[@id="bulldog"]') as $item)
			{
				$CWStatus = (string)$item->Status;
				
				if(trim(strtolower($CWStatus)) == 'enabled')
				{
					if($tech_code == 'A' || $tech_code == 'L')
					{						
						$CWResult = 'MAC';						
					}				  
					else if($tech_code == 'Z')
					{
						$CWResult = 'PASS';						
					}
					else
					{
						$CWResult = 'FAIL';
					}
				}
				else
				{
					$CWResult = 'FAIL';
				}
			}
		}
		else
		{
			$CWResult = 'FAIL';
		}
		
	}
	else
	{
		$CWResult = $general_error;
	}
}
else
{
	$CWResult = $general_error;
} 
curl_close($ch);

/* ====================== SamKnows API Ends ===================================*/

/*$message_body = '<table>
	
<tr><td colspan="2">Below is the result of your feedback form.  It was submitted by ('.$Email.')<br>'.date('l jS \of F Y h:i:s A').'</td></tr>

<tr><td colspan="2">-------------------------------------------------------------------------------------</td></tr>

<tr><td>Name:</td><td>'.$Name.'</td></tr>

<tr><td>Company:</td><td>'.$CompanyName.'</td></tr>

<tr><td>Telephone:</td><td>'.$CompanyTelNo.'</td></tr>

<tr><td>Email:</td><td>'.$Email.'</td></tr>

<tr><td>DSLNO:</td><td>'.$DSLNO.'</td></tr>

<tr><td>ExchangeNAME:</td><td>'.$NAME.'</td></tr>

<tr><td>ExchangeCODE:</td><td>'.$CODE.'</td></tr>

<tr><td>POSTCODE:</td><td>'.$POSTCODE.'</td></tr>

<tr><td>LINE:</td><td>'.$LINE.'</td></tr>

<tr><td>SPEED:</td><td>'.$SPEED.'</td></tr>

<tr><td>BE:</td><td>'.$BEResult.'</td></tr>

<tr><td>CW:</td><td>'.$CWResult.'</td></tr>

<tr><td>BT:</td><td>'.$BTResult.'</td></tr>

<tr><td>FTTC:</td><td>'.$FTTC_availability.'</td></tr>

'.$FTTC_speed.'

<tr><td>WBC ADSL2+:</td><td>'.$WBC_availability.'</td></tr>

'.$WBC_AnnexM_Availability.'

<tr><td colspan="2">-------------------------------------------------------------------------------------</td></tr>

<tr><td>REMOTE ADDR: </td><td>'.$_SERVER['REMOTE_ADDR'].'</td></tr>

<tr><td>HTTP_USER_AGENT: </td><td>'.$_SERVER['HTTP_USER_AGENT'].'</td></tr>

</table>';


$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
$mail->IsSMTP(); // telling the class to use SMTP
try 
{

  //$mail->Host       = "smtp3.cerberusmail.co.uk"; // SMTP server
  $mail->Host       = "smtp.apm-internet.net"; // SMTP server

  $mail->SMTPDebug  = false;                     // enables SMTP debug information (for testing)

  $mail->SMTPAuth   =  true;                  // enable SMTP authentication	  

  $mail->Port       = 25;                    

 $mail->Username   = "joomla@cerberusmail.co.uk"; // SMTP account username
 //$mail->Username   = ""; // SMTP account username

 $mail->Password   = "333heads?1";        // SMTP account password	  

 //$mail->Password   = "";        // SMTP account password	 

 //$mail->AddAddress('website-enquiries@cerberusnetworks.co.uk', 'Website Enquiries');
 //$mail->AddAddress('int-sales@cerberusnetworks.co.uk', 'int-sales');
  $mail->AddAddress('tejas.trivedi@cerberusnetworks.co.uk', 'Tejas Trivedi');	  
  $mail->AddCC('tejas.trivedi@cerberusnetworks.co.uk', 'Tejas Trivedi');	  
  

  $mail->SetFrom('joomla@cerberusmail.co.uk');

  $mail->AddReplyTo('info@cerberusnetworks.co.uk', 'Cerberus Networks');

  $mail->Subject = 'WWW Form Submission';

  $mail->AltBody = $message_body;

  $mail->MsgHTML($message_body);

  $mail->Send();	  

} 
catch (phpmailerException $e) 
{

  echo $e->errorMessage(); //Pretty error messages from PHPMailer

} 
catch (Exception $e) 
{
  echo $e->getMessage(); //Boring error messages from anything else!
}*/

$successMsg = true;

if($successMsg == true)
{		  
	  if($BEResult == 'FAIL' && $BTResult == 'FAIL' && $CWResult == 'FAIL' && $WBC_availability != 'Available')
	  {
			$ADSL_available = false;
	  }
	  else
	  {
			$ADSL_available = true;
	  }
	  
	  if(strtolower($FTTC_availability) == 'not available' || $FTTC_availability == 'no service')
	  {
			$FTTC_available = false;
	  }
	  else
	  {
			$FTTC_available = true;
	  }
	  
	  $msg = '';
	  
	  if($ADSL_available == true && $FTTC_available == true)
	  {
			$msg .= 'Cerberus FTTC & ADSL Services are available in your area!';
	  }
	  else if($ADSL_available == true && $FTTC_available == false)
	  {
			$msg .= 'Cerberus ADSL Services are available in your area!';
	  }
	  else if($ADSL_available == false && $FTTC_available == true)
	  {
			$msg .= 'Cerberus FTTC Services are available in your area!';
	  }
	  else if($ADSL_available == false && $FTTC_available == false)
	  {
			$msg .= 'Cerberus FTTC & ADSL Services are not available in your area!';
	  }
	?>
	
	
    <table data-role="table" id="my-table" data-mode="reflow" class="ui-responsive table-stroke ui-table ui-table-reflow">
    	<tbody>
        	<tr>
            	<td class="ui-btn-text-adsl" width="50%">Number Tested</td>
                <td class="ui-btn-text-adsl" width="50%"><font color="#00B050"><?php echo $CompanyTelNo; ?></font></td>
            </tr>
            
            <tr>
            	<td class="ui-btn-text-adsl" width="50%">Cerberus FTTC
                	<?php
					if(isset($array_FTTC['ExchState']) && $array_FTTC['ExchState'] == 'E')
					{
						if(isset($FTTC_down))
						{
						?>
							<br />Download*
						<?php
						}
						if(isset($FTTC_up))
						{
						?>	
							<br />Upload*
						<?php
						}
					}
					else if(isset($array_FTTC['ExchState']) && ($array_FTTC['ExchState'] == 'P' || $array_FTTC['ExchState'] == 'S'))
					{
						if(isset($FTTC_Exchange))
						{
						?>
							<br />Exchange State
						<?php
						}
						if(isset($FTTC_ReadyDate))
						{
						?>	
							<br />Ready Date
						<?php
						}
					}
				?> 
                </td>
                <td class="ui-btn-text-adsl" width="50%">
                <?php
					if(isset($FTTC_availability) && ($FTTC_availability == 'Available' || $FTTC_availability == 'Available'))
					{
						echo "<font color='#00B050'>".$FTTC_availability."</font>";												
					}
					else
					{
						if($FTTC_availability == 'no service')
							$FTTC_availability = 'Currently not able to check the availability';
						echo "<font color='#FF0019'>".$FTTC_availability."</font>";
					}

					if(isset($array_FTTC['ExchState']) && $array_FTTC['ExchState'] == 'E')
					{
						if(isset($FTTC_down))
						{
							?>
								<br /><?php echo ($FTTC_down/1000) ?>Mbps
							<?php
						}
						if(isset($FTTC_up))
						{
						?>	
							<br /><?php echo ($FTTC_up/1000)?>Mbps
						<?php
						}
					}
					else if(isset($array_FTTC['ExchState']) && ($array_FTTC['ExchState'] == 'P' || $array_FTTC['ExchState'] == 'S'))
					{
						if(isset($FTTC_Exchange))
						{
						?>
							<br /><?php echo $FTTC_Exchange?>
						<?php
						}
						if(isset($FTTC_ReadyDate))
						{
						?>	
							<br /><?php echo $FTTC_ReadyDate ?>
						<?php
						}
					}
					
					?>
                </td>
            </tr>
            
            <tr>
            	<td class="ui-btn-text-adsl" width="50%">Cerberus ADSL2+</td>
                <td class="ui-btn-text-adsl" width="50%">
                <?php 
				if($BEResult == "FAIL")
				{
					echo "<font color='#FF0019'>Not available</font>";
				}
				else
				{
					echo "<font color='#00B050'>Available</font>";
				}					
				?>
                </td>
            </tr>
            
            <tr>
            	<td class="ui-btn-text-adsl" width="50%">Cerberus Connect ADSL2+</td>
                <td class="ui-btn-text-adsl" width="50%">
                <?php 											
				if($CWResult == "FAIL")
				{
					echo "<font color='#FF0019'>Not available</font>";
				}
				else
				{
					echo "<font color='#00B050'>Available</font>";
				}
				
				?>
                </td>
            </tr>
            
            <tr>
            	<td class="ui-btn-text-adsl" width="50%">Cerberus WBC ADSL2+
                <?php
				if(isset($WBC_AnnexM))
				{
				?>	
					<br />Annex M
				<?php
				}
				?>
                </td>
                <td class="ui-btn-text-adsl" width="50%">
                <?php
				if(isset($WBC_availability) && $WBC_availability == 'Available')
				{
					echo "<font color='#00B050'>".$WBC_availability."</font>";												
				}
				else
				{
					if($WBC_availability == 'no service')
						$WBC_availability = 'Currently not able to check the availability';
					echo "<font color='#FF0019'>".$WBC_availability."</font>";
				}
				
				
				if(isset($WBC_AnnexM))
				{
				?>
					<br />
					<?php 
					if(isset($WBC_AnnexM) && $WBC_AnnexM == 'Available')
					{
						echo "<font color='#00B050'>".$WBC_AnnexM."</font>";												
					}
					else
					{
						echo "<font color='#FF0019'>".$WBC_AnnexM."</font>";												
					}					
					?>					
				<?php
				}													 
				?>
                </td>
            </tr>
            
            
            <tr>
            	<td class="ui-btn-text-adsl" width="50%">Cerberus ADSL MAX</td>
                <td class="ui-btn-text-adsl" width="50%">
                <?php 
				if($BTResult == "FAIL")
				{
					echo "<font color='#FF0019'>Not available</font>";
				}
				else
				{
					echo "<font color='#00B050'>Available</font>";
				}				
				?>
                </td>
            </tr>
            
        </tbody>
    </table>
    
    <p class="ui-btn-text-adsl">
		<?php 
		if(strtoupper($BEResult) == 'MAC' || strtoupper($BTResult) == 'MAC' || strtoupper($CWResult) == 'MAC' || (isset($FTTC) && strtoupper($FTTC) == 'MAC'))
		{
		?>                                 	
			Please note: MAC Required. If you wish to migrate from your current broadband supplier you will need to obtain a MAC (Migration Authorisation Code). Cerberus can then arrange for the service to be migrated.
	  	<?php
		}
	  	?>   
	</p>
    
    <p class="ui-btn-text-adsl">
		<?php
		if((isset($FTTC_down) && $FTTC_down != 0) || (isset($FTTC_up) && $FTTC_up != 0))
		{
			echo "* Estimated speeds only.";
		}
		?> 
	</p>
    
					
<?php
}
?>