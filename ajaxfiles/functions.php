<?php

function redirect($redirect_file)
{
	header("location: ".$redirect_file);
}

function UpdateSessionExpiry()
{
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT)) 
	{
	    // last request was more than 30 minates ago
	    if(isset($_SESSION["login_login"]))
	    	unset($_SESSION["login_login"]);
	    	
	    if(isset($_SESSION["provision"]))
	    	unset($_SESSION["provision"]);
	    		
	    session_destroy();   // destroy session data in storage
	    session_unset();     // unset $_SESSION variable for the runtime
	    redirect('./index.php');
	}
	
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
}

function CheckSessionExpiryforModalBox()
{
	if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT)) 
	{
	    // last request was more than 30 minates ago
	    if(isset($_SESSION["login_login"]))
	    	unset($_SESSION["login_login"]);
	    	
	    if(isset($_SESSION["provision"]))
	    	unset($_SESSION["provision"]);
	    		
	    session_destroy();   // destroy session data in storage
	    session_unset();     // unset $_SESSION variable for the runtime

		echo '<script type="text/javascript">';
		echo "$(function() 
		  { 
		  	alert('Session Expired');
			$.nmTop().close();		  	
			parent.window.location.reload();
		  })";
		echo '</script>';
	}
	else
	{
		$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	}
}

function ExtractString($str,$start,$end)
{

	$str_low = strtolower($str);	
	$pos_start = strpos($str_low,$start);	
	$pos_end = strpos($str_low,$end,($pos_start + strlen($start)));	
	$pos1 = $pos_start + strlen($start);
	$pos2 = $pos_end - $pos1;	
	return substr($str, $pos1, $pos2);
}

function myTruncate($string, $limit, $break=".", $pad="...")
{
  // return with no change if string is shorter than $limit
  if(strlen($string) <= $limit) return $string;

  // is $break present between $limit and the end of the string?
  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
    if($breakpoint < strlen($string) - 1) {
      $string = substr($string, 0, $breakpoint) . $pad;
    }
  }
    
  return $string;
}

function validateandReturnTwoDecimals($number)
{
   if($number == '')
   {
   		return '0.00';
   }
   $num_arr = explode(".", $number);
   if(is_array($num_arr) && isset($num_arr[1]))
   {
	   if(strlen($num_arr[1]) < 2)
	   {
		 $pre_number = $num_arr[1].'0';
		 return $num_arr[0].".".$pre_number;
	   }
	   else if(strlen($num_arr[1]) > 2)
	   {		 
		 	return str_replace(",", "", number_format($number,2));
	   }
	   else 
	   {
	   		return $number;
	   }
   }
   return $number.".00";
}

function convertUKformat($date, $format_provided = '')
{
	/*if($date == '' || !(preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,4}/', $date)))
		return $date;
		
	if(preg_match('/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,4}/', $date))
	{	
		$panned_dt_arr = explode("/", $date);
		$ukdate = $panned_dt_arr[1].'/'.$panned_dt_arr[0].'/'.$panned_dt_arr[2];
	}
	else 
	{
		$split_space = explode(" ", $date); 
		$panned_dt_arr = explode("/", $split_space[0]);
		$ukdate = $panned_dt_arr[1].'/'.$panned_dt_arr[0].'/'.$panned_dt_arr[2].' '.$split_space[1];		
	}
	
	return $ukdate;*/
	
	if(preg_match("#^([0-9]+)/([0-9]+)/([0-9]+) ([0-9]+):([0-9]+):([0-9]+)$#", $date, $m)) {
		$format = 'd/m/Y H:i:s';
		$time = mktime($m[4], $m[5], $m[6], $m[1], $m[2], $m[3]);
	}else if(preg_match("#^([0-9]+)/([0-9]+)/([0-9]+)$#", $date, $m)){
		$format = 'd/m/Y';
		$time = mktime(0, 0, 0, $m[1], $m[2], $m[3]);
	}else if(preg_match("#^([0-9]+):([0-9]+):([0-9]+)$#", $date, $m)){
		$format = 'H:i:s';
		$time = mktime($m[1], $m[2], $m[3]);
	}else{
		return $date;
	}
	
	if($format_provided != '')
	{
		$format = $format_provided;
	}
	return date($format, $time);
}

function get_data($url)
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
function get_url_name($input_field)
{
	$regExpLogin = "/[\`~!@#$%^&*()_+=,\/:;\,<>?\[\]{|}\\\"\s\.']/U";

	$input_field = trim($input_field);
	$string = preg_replace($regExpLogin, "-", $input_field);
	$string = str_replace("----", "-", $string);
	$string = str_replace("---", "-", $string);
	$string = str_replace("--", "-", $string);
	$string = strtolower($string);
	return $string;
}

/*-----------------------------------------------------------*/
function func_resize_and_save($src, $dst, $resize_to)
{
	
	if (exif_imagetype($src) == IMAGETYPE_GIF){
		$src_im = imagecreatefromgif($src); 
	}
	elseif (exif_imagetype($src) == IMAGETYPE_JPEG)	{
		$src_im = imagecreatefromjpeg($src); 
	}
	else{
		$src_im = imagecreatefromgif($src);
	}
	
	if (!$src_im){
		$err_msg = "Error - failed in getting an image resource handle for $src";
		return false;
	}

	$dst_dim = func_get_resized_dim(imagesx($src_im), imagesy($src_im), $resize_to);
	$dst_im = imagecreatetruecolor($dst_dim[0],$dst_dim[1]); #calculate the new dimensions 

	if (!$dst_im)
	{
		$err_msg =  "Error - failed in getting an image resource handle for resizing";
		return false;
	}

	imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $dst_dim[0], $dst_dim[1], imagesx($src_im), imagesy($src_im));



	//save the resized image to the file  
	if (exif_imagetype($src) == IMAGETYPE_GIF)
	{
		imagegif($dst_im,$dst); 
	}
	elseif (exif_imagetype($src) == IMAGETYPE_JPEG) 
	{
		imagejpeg($dst_im,$dst);
	}
	else
	{
		imagepng($dst_im,$dst);
	}
	
	//Explicitly destroy the image resource handles we have used.  This
	//part is really important, If you do not do this, you will soon hit  php's memory limit
	imagedestroy($src_im);imagedestroy($dst_im);

	return true;
}
function func_get_resized_dim($w, $h, $resize_to)
{
	if ($w > $h)
		$ratio = (double)($resize_to/$w);
	else
		$ratio = (double) ($resize_to/$h);
		
	return array($w * $ratio, $h * $ratio);  
}
/*-----------------------------------------------------------*/

function func_get_image_size($filename) {
   list($width, $height, $type) = getimagesize($filename);
    switch($type) {
        case "1": $type = "image/gif";
				break;
        case "2": $type = "image/pjpeg";
				break;
        case "3": $type = "image/png";
				break;
		case "4": $type = "application/x-shockwave-flash";
				break;
		case "5": $type = "image/psd";
				break;
		case "6": $type = "image/bmp";
				break;
        default:  $type = "";
    }
	if (!empty($type))
	    return array(@filesize($filename),$width,$height,$type);
	else
		return false;
}
/*-----------------------------------------------------------*/

/*-----------------------------------------------------------*/

function check_security()
{
	if(!isset($_SESSION["int_adminid"]) && $_SESSION["int_adminid"] == "")
	{
		header("Location:index.php?file=default");
		exit;
	}
}

function expert_security()
{
	if(!isset($_SESSION["session_expert_id"]) && $_SESSION["session_expert_id"] == "")
	{
		header("Location:index.php?file=default");
		exit;
	}
}

function downloadFile( $fullPath, $errorstr = '' ){ 

  // Must be fresh start 
  if( headers_sent() ) 
    die('Headers Sent'); 

  // Required for some browsers 
  if(ini_get('zlib.output_compression')) 
    ini_set('zlib.output_compression', 'Off'); 

  // File Exists? 
  if( file_exists($fullPath) ){ 
    
    // Parse Info / Get Extension 
    $fsize = filesize($fullPath); 
    $path_parts = pathinfo($fullPath); 
    $ext = strtolower($path_parts["extension"]); 
    
    // Determine Content Type 
    switch ($ext) { 
      case "pdf": $ctype="application/pdf"; break; 
      case "exe": $ctype="application/octet-stream"; break; 
      case "zip": $ctype="application/zip"; break; 
      case "doc": $ctype="application/msword"; break; 
      case "xls": $ctype="application/vnd.ms-excel"; break; 
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break; 
      case "gif": $ctype="image/gif"; break; 
      case "png": $ctype="image/png"; break; 
      case "jpeg": 
      case "jpg": $ctype="image/jpg"; break; 
      default: $ctype="application/force-download"; 
    } 

    header("Pragma: public"); // required 
    header("Expires: 0"); 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
    header("Cache-Control: private",false); // required for certain browsers 
    header("Content-Type: $ctype"); 
    header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" ); 
    header("Content-Transfer-Encoding: binary"); 
    header("Content-Length: ".$fsize); 
    ob_clean(); 
    flush(); 
    readfile( $fullPath ); 

  } 
  else 
  {
    //if($errorstr == '')
		die('File Not Found'); 
	//else
		//die($errorstr);	
  }

}

function GetXmlValueByTag($xml, $tag)
{
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $xml, $arrVals);
	xml_parser_free($parser);
	foreach ($arrVals as $keyArr )
	{
		if($keyArr['tag']==$tag)
		{
			$tagValue[] = $keyArr['attributes'];
		}
	}
	return $tagValue;
}

function xmlstr_to_array($xmlstr) 
{
	
  $xmlstr = str_replace('&', '&amp;', $xmlstr);  
  $doc = new DOMDocument();
  if($doc->loadXML($xmlstr) == true)
  {
	 return domnode_to_array($doc->documentElement);
  }
  else
  {
	 return false;
  }
	  
}
function domnode_to_array($node) {
  $output = array();
  
  switch ($node->nodeType) {
   case XML_CDATA_SECTION_NODE:
   case XML_TEXT_NODE:
    $output = trim($node->textContent);
   break;
   case XML_ELEMENT_NODE:
    for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
     $child = $node->childNodes->item($i);
     $v = domnode_to_array($child);
     if(isset($child->tagName)) {
       $t = $child->tagName;
       if(!isset($output[$t])) {
        $output[$t] = array();
       }
       $output[$t][] = $v;
     }
     elseif($v) {
      $output = (string) $v;
     }
    }
    if(is_array($output)) {
     if($node->attributes->length) {
      $a = array();
      foreach($node->attributes as $attrName => $attrNode) {
       $a[$attrName] = (string) $attrNode->value;
      }
      $output['@attributes'] = $a;
     }
     foreach ($output as $t => $v) {
      if(is_array($v) && count($v)==1 && $t!='@attributes') {
       $output[$t] = $v[0];
      }
     }
    }
   break;
  }
  return $output;
}

/*$xml = simplexml_load_string($Services_auto);
$json = json_encode($xml);
$array = json_decode($json,TRUE);*/
								
function curPageURL() 
{
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} 
	else 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function getTextBetweenTags($string, $tagname) 
{
    $pattern = "/<$tagname>(.*?)<\/$tagname>/";
    preg_match($pattern, $string, $matches);
    return $matches[1];
}

function msort($array, $key, $sort_flags = SORT_REGULAR) {
    if (is_array($array) && count($array) > 0) {
        if (!empty($key)) {
            $mapping = array();
            foreach ($array as $k => $v) {
                $sort_key = '';
                if (!is_array($key)) {
                    $sort_key = $v[$key];
                } else {
                    // @TODO This should be fixed, now it will be sorted as string
                    foreach ($key as $key_key) {
                        if(isset($v[$key_key]))
							$sort_key .= $v[$key_key];
                    }
                    $sort_flags = SORT_STRING;
                }
                $mapping[$k] = $sort_key;
            }
            asort($mapping, $sort_flags);
            $sorted = array();
            foreach ($mapping as $k => $v) {
                $sorted[] = $array[$k];
            }
            return $sorted;
        }
    }
    return $array;
}

function html_special_chars($str)
{
     return preg_replace(array('/&/', '/"/'), array('&amp;', '&quot;'), $str);
}

function dns_execute($xml, $config_path)
{
	$private_key = $config_path['opensrs_private_key'];
 	$username = $config_path['opensrs_username'];
 	$host = $config_path['opensrs_host'];
 	$port = $config_path['opensrs_port'];
	
 	$result = '';
 	$signature = md5(md5($xml.$private_key).$private_key);
	$url = "/";
	$header = "";
	$header .= "POST $url HTTP/1.0\r\n";
	$header .= "Content-Type: text/xml\r\n";
	$header .= "X-Username: " . $username . "\r\n";
	$header .= "X-Signature: " . $signature . "\r\n";
	$header .= "Content-Length: " . strlen($xml) . "\r\n\r\n";
	# ssl:// requires OpenSSL to be installed
	try 
	{
		$fp = @fsockopen ("ssl://$host", $port, $errno, $errstr, 30);
		if (!$fp) 
		{
			return "Service is currently not available.Please try again later.";
		} 
		else 
		{
			# post the data to the server
			fputs ($fp, $header . $xml);
			while (!feof($fp)) 
			{
				$res = fgets ($fp, 1024);
				$result .= htmlEntities($res);
			}
			fclose ($fp);
			$result_arr = explode("text/xml", $result);
	
			//$arr = xmlstr_to_array(html_entity_decode(trim($result_arr[1])));
			return $result_arr[1];
		}
	}
	catch (Exception $e) 
	{
	  return "Service is currently not available.Please try again later.";
	}	
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function getCountries()
{
	$array_country = array("GB"=>"United Kingdom (GB)",
"AF"=>"Afghanistan",
"AL"=>"Albania",
"DZ"=>"Algeria",
//"AS"=>"American Samoa",
"AD"=>"Andorra",
"AO"=>"Angola",
//"AI"=>"Anguilla",
"AQ"=>"Antarctica",
//"AG"=>"Antigua and Barbuda",
"AR"=>"Argentina",
"AM"=>"Armenia",
"AW"=>"Aruba",
"AC"=>"Ascension Island",
"AU"=>"Australia",
"AT"=>"Austria",
"AZ"=>"Azerbaijan",
//"BS"=>"Bahamas",
"BH"=>"Bahrain",
"BD"=>"Bangladesh",
//"BB"=>"Barbados",
"BY"=>"Belarus",
"BE"=>"Belgium",
"BZ"=>"Belize",
"BJ"=>"Benin",
//"BM"=>"Bermuda",
"BT"=>"Bhutan",
"BO"=>"Bolivia",
"BA"=>"Bosnia and Herzegovina",
"BW"=>"Botswana",
"BV"=>"Bouvet Island",
"BR"=>"Brazil",
"IO"=>"British Indian Ocean Territory",
"BN"=>"Brunei Darussalam",
"BG"=>"Bulgaria",
"BF"=>"Burkina Faso",
"BI"=>"Burundi",
"KH"=>"Cambodia",
"CM"=>"Cameroon",
"CA"=>"Canada",
"CV"=>"Cape Verde Islands",
//"KY"=>"Cayman Islands",
"CF"=>"Central African Republic",
"TD"=>"Chad",
"CL"=>"Chile",
"CN"=>"China",
"CX"=>"Christmas Island",
"CC"=>"Cocos (Keeling) Islands",
"CO"=>"Colombia",
"KM"=>"Comoros",
"CD"=>"Congo, Democratic Republic of",
"CG"=>"Congo, Republic of",
"CK"=>"Cook Islands",
"CR"=>"Costa Rica",
"CI"=>"Cote d\'Ivoire",
"HR"=>"Croatia/Hrvatska",
"CY"=>"Cyprus",
"CZ"=>"Czech Republic",
"DK"=>"Denmark",
"DJ"=>"Djibouti",
//"DM"=>"Dominica",
//"DO"=>"Dominican Republic",
"TP"=>"East Timor",
"EC"=>"Ecuador",
"EG"=>"Egypt",
"SV"=>"El Salvador",
"GQ"=>"Equatorial Guinea",
"ER"=>"Eritrea",
"EE"=>"Estonia",
"ET"=>"Ethiopia",
"FK"=>"Falkland Islands",
"FO"=>"Faroe Islands",
"FJ"=>"Fiji",
"FI"=>"Finland",
"FR"=>"France",
"GF"=>"French Guiana",
"PF"=>"French Polynesia",
"TF"=>"French Southern Territories",
"GA"=>"Gabon",
"GM"=>"Gambia",
"GE"=>"Georgia",
"DE"=>"Germany",
"GH"=>"Ghana",
"GI"=>"Gibraltar",
"GR"=>"Greece",
"GL"=>"Greenland",
//"GD"=>"Grenada",
"GP"=>"Guadeloupe",
//"GU"=>"Guam",
"GT"=>"Guatemala",
"GG"=>"Guernsey",
"GN"=>"Guinea",
"GW"=>"Guinea-Bissau",
"GY"=>"Guyana",
"HT"=>"Haiti",
"HM"=>"Heard and McDonald Islands",
"HN"=>"Honduras",
"HK"=>"Hong Kong",
"HU"=>"Hungary",
"IS"=>"Iceland",
"IN"=>"India",
"ID"=>"Indonesia",
"IQ"=>"Iraq",
"IE"=>"Ireland",
"IM"=>"Isle of Man",
"IL"=>"Israel",
"IT"=>"Italy",
//"JM"=>"Jamaica",
"JP"=>"Japan",
"JE"=>"Jersey",
"JO"=>"Jordan",
"KZ"=>"Kazakhstan",
"KE"=>"Kenya",
"KI"=>"Kiribati",
"KR"=>"Korea, Republic of (South Korea)",
"KV"=>"Kosovo",
"KW"=>"Kuwait",
"KG"=>"Kyrgyzstan",
"LA"=>"Laos",
"LV"=>"Latvia",
"LB"=>"Lebanon",
"LS"=>"Lesotho",
"LR"=>"Liberia",
"LY"=>"Libya",
"LI"=>"Liechtenstein",
"LT"=>"Lithuania",
"LU"=>"Luxembourg",
"MO"=>"Macau",
"MK"=>"Macedonia",
"MG"=>"Madagascar",
"MW"=>"Malawi",
"MY"=>"Malaysia",
"MV"=>"Maldives",
"ML"=>"Mali",
"MT"=>"Malta",
"MH"=>"Marshall Islands",
"MQ"=>"Martinique",
"MR"=>"Mauritania",
"MU"=>"Mauritius",
"YT"=>"Mayotte Island",
"MX"=>"Mexico",
"FM"=>"Micronesia",
"MD"=>"Moldova",
"MC"=>"Monaco",
"MN"=>"Mongolia",
"ME"=>"Montenegro",
//"MS"=>"Montserrat",
"MA"=>"Morocco",
"MZ"=>"Mozambique",
"MM"=>"Myanmar",
"NA"=>"Namibia",
"NR"=>"Nauru",
"NP"=>"Nepal",
"NL"=>"Netherlands",
"AN"=>"Netherlands Antilles",
"NC"=>"New Caledonia",
"NZ"=>"New Zealand",
"NI"=>"Nicaragua",
"NE"=>"Niger",
"NG"=>"Nigeria",
"NU"=>"Niue",
"NF"=>"Norfolk Island",
//"MP"=>"Northern Mariana Islands",
"NO"=>"Norway",
"OM"=>"Oman",
"PK"=>"Pakistan",
"PW"=>"Palau",
"PS"=>"Palestinian Territories",
"PA"=>"Panama",
"PG"=>"Papua New Guinea",
"PY"=>"Paraguay",
"PE"=>"Peru",
"PH"=>"Philippines",
"PN"=>"Pitcairn Island",
"PL"=>"Poland",
"PT"=>"Portugal",
"PR"=>"Puerto Rico",
"QA"=>"Qatar",
"RE"=>"Reunion Island",
"RO"=>"Romania",
"RU"=>"Russian Federation",
"RW"=>"Rwanda",
"SH"=>"Saint Helena",
//"KN"=>"Saint Kitts and Nevis",
//"LC"=>"Saint Lucia",
"PM"=>"Saint Pierre and Miquelon",
//"VC"=>"Saint Vincent and the Grenadines",
"SM"=>"San Marino",
"ST"=>"Sao Tome and Principe",
"SA"=>"Saudi Arabia",
"SN"=>"Senegal",
"RS"=>"Serbia",
"SC"=>"Seychelles",
"SL"=>"Sierra Leone",
"SG"=>"Singapore",
"SK"=>"Slovak Republic",
"SI"=>"Slovenia",
"SB"=>"Solomon Islands",
"SO"=>"Somalia",
"ZA"=>"South Africa",
"GS"=>"South Georgia and South Sandwich Islands",
"ES"=>"Spain",
"LK"=>"Sri Lanka",
"SR"=>"Suriname",
"SJ"=>"Svalbard and Jan Mayen Islands",
"SZ"=>"Swaziland",
"SE"=>"Sweden",
"CH"=>"Switzerland",
"TW"=>"Taiwan",
"TJ"=>"Tajikistan",
"TZ"=>"Tanzania",
"TH"=>"Thailand",
"TL"=>"Timor-Leste",
"TG"=>"Togo",
"TK"=>"Tokelau",
"TO"=>"Tonga Islands",
//"TT"=>"Trinidad and Tobago",
"TN"=>"Tunisia",
"TR"=>"Turkey",
"TM"=>"Turkmenistan",
//"TC"=>"Turks and Caicos Islands",
"TV"=>"Tuvalu",
"UG"=>"Uganda",
"UA"=>"Ukraine",
"AE"=>"United Arab Emirates",
"US"=>"United States",
"UY"=>"Uruguay",
"UM"=>"US Minor Outlying Islands",
"UZ"=>"Uzbekistan",
"VU"=>"Vanuatu",
"VA"=>"Vatican City",
"VE"=>"Venezuela",
"VN"=>"Vietnam",
//"VG"=>"Virgin Islands (British)",
//"VI"=>"Virgin Islands (USA)",
"WF"=>"Wallis and Futuna Islands",
"EH"=>"Western Sahara",
"WS"=>"Western Samoa",
"YE"=>"Yemen",
"ZM"=>"Zambia",
"ZW"=>"Zimbabwe");

return $array_country;
}

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	$error = '';
	
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        $error .=  "<b>My ERROR</b> [$errno] $errstr<br />\r\n";
        $error .= "  Fatal error on line $errline in file $errfile";
        break;

    case E_USER_WARNING:
        $error .= "<b>My WARNING</b> [$errno] $errstr on line $errline in file $errfile<br />\r\n";
        break;

    case E_USER_NOTICE:
        $error .= "<b>My NOTICE</b> [$errno] $errstr on line $errline in file $errfile<br />\r\n";
        break;

    default:
        $error .= "Unknown error type: [$errno] $errstr on line $errline in file $errfile<br />\r\n";
        break;
    }
	
	global $log_file;

    $fd = fopen($log_file, 'a');
    if(!$fd)
    {
        echo $error;
    }
    else
    {
        if(!fwrite($fd, date('Y-m-d H:i:s')." ERR : \n$error\n\n"))
        {
            echo "<pre>$error</pre>";
        }
        fclose($fd);
    }    
	header('Location:index.php?f=error');
	exit;
    /* Don't execute PHP internal error handler */
    //return true;
}

// function to test the error handling
function scale_by_log($vect, $scale)
{
    if (!is_numeric($scale) || $scale <= 0) {
        trigger_error("log(x) for x <= 0 is undefined, you used: scale = $scale", E_USER_ERROR);
    }

    if (!is_array($vect)) {
        trigger_error("Incorrect input vector, array of values expected", E_USER_WARNING);
        return null;
    }

    $temp = array();
    foreach($vect as $pos => $value) {
        if (!is_numeric($value)) {
            trigger_error("Value at position $pos is not a number, using 0 (zero)", E_USER_NOTICE);
            $value = 0;
        }
        $temp[$pos] = log($scale) * $value;
    }

    return $temp;
}

// set to the user defined error handler
//$old_error_handler = set_error_handler("myErrorHandler");

?> 