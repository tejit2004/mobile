<?php // FMStudio v1.0 - do not remove comment, needed for DreamWeaver support 
// Copyright 2007 FMWebschool Inc. this file is part of FMStudio

require_once('FMStudio2.lib.php');

if(!session_id()) session_start();

fmsGET2POST();
function fmsGetMIMEType($url,&$ext) {
	global $fmsMIME_TYPES;
	$ext = preg_replace('#^.*data\.([^\?]*)\?.*$#','\1',$url);
	$ext = strtolower(trim($ext));
	if($ext == '') return 'text/plain';
	switch($ext){
	case 'gif.php':
	case 'gif':
		return 'image/gif';
	case 'png.php':
	case 'png':
		return 'image/png';
	case 'jpg.php':
	case 'jpg':
		return 'image/jpeg';
	case '.cnt':
	
	case isset($fmsMIME_TYPES[$ext]):
		return $fmsMIME_TYPES[$ext];
	default:
		return 'text/plain';
	}
}

function fmsShowImage(&$fm,$layout,$field,$recid) {
	$rec = $fm->getRecordById($layout,$recid);
	$url = $rec->getField($field);
	if($url != '') {
		if(!isset($_SESSION['fmsShowImage_CACHE'])) $_SESSION['fmsShowImage_CACHE'] = array();
		if(isset($_SESSION['fmsShowImage_CACHE'][$url.$rec->getModificationId()])) {
			$headers = $_SESSION['fmsShowImage_CACHE'][$url.$rec->getModificationId()][0];
			$data = $_SESSION['fmsShowImage_CACHE'][$url.$rec->getModificationId()][1];
			foreach($headers as $header) header($header);
			echo $data;
			exit();
		}
		$headers = array();
		$data = $fm->getContainerData($url);
		$headers[] = 'Content-Type: '.fmsGetMIMEType($url,$ext);
		$headers[] = 'Content-Disposition: inline; filename='.$field.'-'.$recid.'.'.$ext;
		$headers[] = "Content-Transfer-Encoding: binary";
		$headers[] = "Content-Length: ".strlen($data);
		$_SESSION['fmsShowImage_CACHE'][$url.$rec->getModificationId()] = array($headers,$data);
		foreach($headers as $header) header($header);
		echo $data;
	}else{
		$data = pack("H*",'47494638396101000100a10100000000ffffffffffffffffff21f904010a0001002c00000000010001000002024c01003b');
		header('Content-Type: image/gif');
		header('Content-Disposition: inline; filename=blank.gif');
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($data));
		echo $data;
	}
	exit();
}

function fmsImageURL($field,$recid) {
	$self = array_shift(explode('?',$_SERVER['PHP_SELF'],2));
	return $self.'?image='.$field.'&recid='.$recid;
}

function fmsServerRequestURL($hostspec,$username,$password,$url) {
	$hostspec = explode('://',$hostspec,2);
	$prefix = 'http';
	if(count($hostspec) == 2) {
		$prefix = $hostspec[0];
		$hostspec = $hostspec[1];
	}else{
		$hostspec = $hostspec[0];
	}
	return $prefix.'://'.urlencode($username).':'.urlencode($password).'@'.$hostspec.$url;
}

$fmsCRYPT_SALT = "FMStudio!";		// Feel free to change this line to a unique value for your server
function fmsServeFile(&$fm,$layout,$field,$recid) {
	global $fmsCRYPT_SALT;
	if(bin2hex(substr(crypt(md5($field.$fmsCRYPT_SALT.$recid),$fmsCRYPT_SALT),4,12)) != $_GET['h']) die('security check failed');
	$rec = $fm->getRecordById($layout,$recid);
	$url = $rec->getFieldUnencoded($field);
	if($url != '') {
		$properties = $fm->getProperties();
		$fullUrl = fmsServerRequestURL($properties['hostspec'],$properties['username'],$properties['password'],$url);
		$ch = curl_init($fullUrl);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array('X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk='));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_HEADER, TRUE);
		$data = curl_exec($ch) or die(curl_error($ch));
		$body = substr($data, strpos($data, "\r\n\r\n") + 4);
       	$headers = fmsHeadersToArray(substr($data, 0, -strlen($body)));
		if(isset($headers['content-type'])) header($headers['content-type'][0]);
		if(isset($headers['content-disposition'])) header($headers['content-disposition'][0]);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); 
		header("Cache-Control:  maxage=1");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($body));
		echo $body;
	}else{
		die('no file is associated with this record');
	}
	exit();
}

function fmsGetFileData($fm, $url) {
	$ret = array(
		'name'=>null,
		'data'=>null,
		'size'=>0,
		'headers'=>array(),
		'mime'=>'application/octet-stream',
	);
	if($url != '') {
		if($fm !== null) {
			$properties = $fm->getProperties();
			$fullUrl = fmsServerRequestURL($properties['hostspec'],$properties['username'],$properties['password'],$url);
		}else{
			$fullUrl = $url;
		}
		$ch = curl_init($fullUrl);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array('X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk='));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_HEADER, TRUE);
		$data = curl_exec($ch) or die(curl_error($ch));

		$ret['data'] = substr($data, strpos($data, "\r\n\r\n") + 4);
       	$ret['headers'] = fmsHeadersToArray(substr($data, 0, -strlen($ret['data'])));
		$ret['size'] = strlen($ret['data']);
		
		if(isset($ret['headers']['content-type'])) $ret['mime'] = $ret['headers']['content-type'][1];
		if(isset($ret['headers']['content-disposition'])) {
			$ret['name'] = $ret['headers']['content-disposition'][1];
			$ret['name'] = preg_replace('#^.*filename=([^ \t,]+).*$#i','$1',$ret['name']);
		}else{
			$ext = preg_replace('#^.*data\.([^\?]*)\?.*$#','\1',$url);
			$ext = strtolower(trim($ext));
			$ret['name'] = substr(md5($ret['data']),0,6).'.'.$ext;
		}
		
		return $ret;
	}else{
		return false;
	}

}

function fmsFileURL($field,$recid) {
	global $fmsCRYPT_SALT;
	$self = array_shift(explode('?',$_SERVER['PHP_SELF'],2));
	$md5 = md5($field.$fmsCRYPT_SALT.$recid);
	return $self.'?file='.$field.'&recid='.$recid.'&h='.bin2hex(substr(crypt($md5,$fmsCRYPT_SALT),4,12));
}

function fmsHeadersToArray($headers) {
	$headers = str_replace("\r","\n",$headers);
	$headers = str_replace("\n\n","\n",$headers);
	$headers = explode("\n",$headers);
	$ret = array();
	foreach($headers as $header) {
		preg_match('#^([^:\ ]+)[:\ ](.*)$#',$header,$matches);
		if(isset($matches[1]) && isset($matches[2])) {
			$ret[strtolower($matches[1])] = array($header,trim($matches[2]));
		}
	}
	return $ret;
}


function fmsSetPage(&$rs,$name,$max) {
	$skip = 0;
	if(isset($_REQUEST[$name.'_page']))$skip = ($_REQUEST[$name.'_page']-1)*$max;
	else if(isset($_REQUEST['-skip'])) {
		$skip = ($_REQUEST['-skip']);
		$_REQUEST[$name.'_page'] = floor($skip / $max)+1;
		if($_REQUEST[$name.'_page'] < 1) $_REQUEST[$name.'_page'] = 1;
	}
	if($skip < 0) $skip = 0;
	if($max < 0) $max = 10;
	$rs->setRange($skip,$max);
}

function fmsSetLastPage(&$result,$name,$max) {
	global ${$name.'_last_page'};
	$pages = 1;
	if($max < 0) $max = 10;
	if(!FileMaker::isError($result)) {
		$totalFound = $result->getFoundSetCount();
		$pages = ceil($totalFound / $max);
	}
	${$name.'_last_page'} = $pages;
}

function fmsGetPage($name) {
	if(isset($_REQUEST[$name.'_page']) && (int)$_REQUEST[$name.'_page'] > 1) {
		return (int)$_REQUEST[$name.'_page'];
	}else{
		return 1;
	}
}

function fmsGetPageCount($name) {
	global ${$name.'_last_page'};
	return ${$name.'_last_page'};
}

function fmsFirstPage($name,$max = -1) {
	return fmsPageURL($name,1,$max);
}

function fmsPrevPage($name,$max = -1) {
	$page = 1;
	if(isset($_REQUEST[$name.'_page'])) $page = (int)$_REQUEST[$name.'_page']-1;
	if($page < 1) $page = 1;
	return fmsPageURL($name,$page,$max);
}

function fmsNextPage($name,$max = -1) {
	$page = 2;
	if(isset($_REQUEST[$name.'_page'])) $page = (int)$_REQUEST[$name.'_page']+1;
	if($page < 2) $page = 2;
	if($page > fmsGetPageCount($name)) $page = fmsGetPageCount($name);
	return fmsPageURL($name,$page,$max);
}

function fmsLastPage($name,$max = -1) {
	return fmsPageURL($name,fmsGetPageCount($name),$max);
}

function fmsPageURL($name,$page,$max) {
	global $self_url;
	$ret_url = $self_url;
	$ret_url = fmsUrlVar($ret_url,$name.'_page',$page);
	
	$p2g = fmsPOST2GET();
	if($p2g != '') {
		$ret_url.= '&'.$p2g;
	}
	return $ret_url;
}

/*function fmsNavBar($name, $settings) {
	if(fmsGetPageCount($name) == 1) return;
	
	$settings = fmsDecodeAdvDialogValues($settings);
	
	$sep = $settings[5];
	
	$page = fmsGetPage($name);
	$total = fmsGetPageCount($name);
	$settings[2] = str_replace(array('#page#','#total#'),array($page,$total),$settings[2]);
	
	$ret = '';
	if($page != 1) {
		$ret.='<a href="'.fmsFirstPage($name).'" class="fms_nav_first">'.$settings[0].'</a>'.$sep;
		$ret.='<a href="'.fmsPrevPage($name).'" class="fms_nav_prev">'.$settings[1].'</a>'.$sep;
	}
	$ret.= $settings[2];
	
	if($page != $total) {
		$ret.=$sep.'<a href="'.fmsNextPage($name).'" class="fms_nav_next">'.$settings[3].'</a>'.$sep;
		$ret.='<a href="'.fmsLastPage($name).'" class="fms_nav_last">'.$settings[4].'</a>';
	}
	
	return '<span class="fms_nav_bar">'.$ret.'</span>';
}*/


function fmsNavBar($name) {
	global $max;
	if(fmsGetPageCount($name) == 1) return;
	
	$page = fmsGetPage($name);
	$total = fmsGetPageCount($name);
	$sep = '&nbsp;';
	
	$ret = '';
	if($page != 1) {		
		$ret.='<a href="'.fmsPrevPage($name).'" onclick="javascript:spinImage();"> < Prev</a>';	
	}
	
	for($i=1;$i<=$total;$i++)
	{
		if($i == $page)
			$active = ' active';
		else 
			$active = '';
		$ret.='<a href="'.fmsPageURL($name, $i, $max).'" class="graybutton pagelink'.$active.'" onclick="javascript:spinImage();">'.$i.'</a>';
	
	}
	
	if($page != $total) {				
		$ret.='<a href="'.fmsNextPage($name).'" onclick="javascript:spinImage();">Next > </a>';
	}
	
	return $ret;
}

function fmsSortLink($name, $field, $label, $settings) {
	global $self_url;
	$settings = fmsDecodeAdvDialogValues($settings);
	
	$varName = $name.'_sort';
	
	if(fmsGET($varName) && fmsGET($varName) == $field.'.ascending') {
		$varValue = $field.'.descending';
		$label.=$settings[0];
		$settings[3].=';'.$settings[4];
	}else if(fmsGET($varName) && fmsGET($varName) == $field.'.descending') {
		$varValue = $field.'.ascending';
		$label.=$settings[1];
		$settings[3].=';'.$settings[4];
	}else{
		$varValue = $field.'.ascending';
	}
	
	$url = fmsUrlVar($self_url, $varName, $varValue);
	$link = $url;
	
	if($label === NULL) return $link;
	
	return '<a href="'.$link.'" class="'.$settings[2].'" style="'.$settings[3].'">'.$label.'</span></a>';
}

function fmsSortLink_Process($name) {
	$varName = $name.'_sort';
	$findName = $name.'_find';
	$setting = fmsGET($varName);
	if(!$setting) return;
	$setting = explode('.',$setting,2);
	
	global $$findName;
	
	if($setting[1] == 'ascending') {
		$setting[1] = FILEMAKER_SORT_ASCEND;
	}else{
		$setting[1] = FILEMAKER_SORT_DESCEND;
	}
	$$findName->addSortRule($setting[0],1,$setting[1]);
}

function fmsUrlVar($url,$var,$value) {
	if(strpos($url,'?') == false) {
		return $url.'?'.$var.'='.$value;
	}else{
		if(strpos($url,$var) === false) {
			return $url.'&'.$var.'='.$value;
		}else{
			$var_pos = strpos($url,$var);
			if(preg_match('/'.$var.'=[^&]*/',$url)) {
				return preg_replace('/'.$var.'=[^&]*/',"{$var}={$value}",$url);
			}else{
				return preg_replace('/'.$var.'/',"{$var}={$value}",$url);
			}
		}
	}
}

function fmsRedirect($url) {
	if($url == -1) $url = $_SERVER['HTTP_REFERER'];
	header('Location: '.$url);
	exit();
}

function fmsTrapError($result,$redirect) {
	$redirect = fmsUrlVar($redirect,'errorCode',urlencode($result->code));
	$redirect = fmsUrlVar($redirect,'errorMsg',urlencode($result->getErrorString()));
	fmsRedirect($redirect);
}

function fmsEscape($text, $quoted = false) {
	$escape_chars = '/([@*#?!=<>"])/';
	$text = preg_replace($escape_chars,'\\\${1}',$text);
	if($quoted) {
		return '"'.$text.'"';
	}else{
		return $text;
	}
}

function fmsCheckLogin($connName,$login_page) {
	global $self_url;
	if(!session_id()) session_start();	
	fmsCheckLogout();	
	if(fmsPOST($connName.'_user')) {

		$user = fmsPOST($connName.'_user');
		$pass = fmsPOST($connName.'_pass');
		$_SESSION[$connName.'_login'] = array('user'=>$user,'pass'=>$pass,'first'=>true);
		return;
	}
	if(!isset($_SESSION[$connName.'_login'])) {
		$_SESSION['login_conn'] = $connName;
		$_SESSION['login_from'] = $self_url;
		session_write_close();
		header('Location: '.$login_page);
		exit();
	}
}

function fmsCheckTableLogin($connName,$login_page) {
	global $self_url;
	if(!session_id()) session_start();
	fmsCheckLogout();
	if(fmsGET($connName.'_user')) {
		$user = fmsGET($connName.'_user');
		$pass = fmsGET($connName.'_pass');
		$_SESSION[$connName.'_tableLogin'] = array('user'=>$user,'pass'=>$pass,'first'=>true);
		return;
	}

	if(!isset($_SESSION[$connName.'_tableLogin'])) {
		$_SESSION['login_conn'] = $connName;
		$_SESSION['login_from'] = $self_url;
		$_SESSION['login_type'] = 'table';
		session_write_close();
		header('Location: '.$login_page);
		exit();
	}

}

function fmsCheckFirstLogin($connName,$login_page,$conn) {
	global $self_url;
	if(!session_id()) session_start();
	//if($_SESSION[$connName.'_login']['first'] === true) {		
		$result = $conn->listLayouts();
		if(FileMaker::isError($result)) {
			$_SESSION['login_conn'] = $connName;
			$_SESSION['login_from'] = $self_url;
			
			
			$_SESSION["login_login"]["user"]	= "";
			$_SESSION["login_login"]["pass"]	= "";
			$_SESSION["login_login"]["first"]	= "";
			
			unset($_SESSION["login_login"]["user"]);
			unset($_SESSION["login_login"]["pass"]);
			unset($_SESSION["login_login"]["first"]);

			unset($_SESSION[$connName.'_login']);
			
			session_unset(); 
 
			
			//session_write_close();
			//header('Location: '.$login_page.'?errorMsg='.urlencode('Incorrect user name or password'));
			//exit();
			$return = true;
			return $return;
		}else{			
			return $_SESSION[$connName.'_login']['first'] = $return = false;
			return $return;
		}
	//}
}

function fmsCheckFirstTableLogin($connName,$login_page,$conn) {
	global $self_url;
	
	$settings = 'TableLogin_'.$connName;
	global $$settings;
	$settings = $$settings;
	
	if(!session_id()) session_start();
	if($_SESSION[$connName.'_tableLogin']['first'] === true) {		
		$find = $conn->newFindCommand($settings[0]);
		$find->addFindCriterion($settings[1], '=='.fmsEscape($_SESSION[$connName.'_tableLogin']['user']));
		$find->addFindCriterion($settings[2], '=='.fmsEscape($_SESSION[$connName.'_tableLogin']['pass']));
		$result = $find->execute();
		if(FileMaker::isError($result)) {
			$_SESSION['login_conn'] = $connName;
			$_SESSION['login_from'] = $self_url;
			$_SESSION['login_type'] = 'table';
			unset($_SESSION[$connName.'_login']);
			session_write_close();
			header('Location: '.$login_page.'?errorMsg='.urlencode('Incorrect user name or password'));
			exit();
		}else{
			$_SESSION[$connName.'_login']['first'] = false;
		}
	}
}

function fmsCheckLogout() {
	global $self_url_clean;
	
	if(isset($_GET['logout'])) 
	{
		$conn = fmsGET('logout');
		if(isset($_SESSION[$conn.'_login'])) {
			unset($_SESSION[$conn.'_login']);
		}
		if(isset($_SESSION[$conn.'_tableLogin'])) {
			unset($_SESSION[$conn.'_tableLogin']);
		}
		session_write_close();
		if(isset($_GET['redirect']) && $_GET['redirect'] != '') {
			header('Location: '.$_GET['redirect']);
		}else{
			header('Location: '.$self_url_clean);
		}
		exit();
	}
}

function fmsPerformLogin() {
	if(!session_id()) session_start();
	fmsCheckLogout();
	
	if(isset($_POST['login_user'])) 
	{
		$user = fmsPOST('login_user');
		$pass = fmsPOST('login_pass');
		if($user == '' || $pass == '') return 'User Name or Password cannot be blank';
		$conn = $_SESSION['login_conn'];
		
		if(isset($_SESSION['login_from']) && $_SESSION['login_from'] != '') {
			$from = $_SESSION['login_from'];
		}else if(isset($_POST['defaultURL']) && $_POST['defaultURL'] != '') {
			$from = $_POST['defaultURL'];
		}else{
			$from = 'index.php';
		}
		if(isset($_SESSION['login_type']) && $_SESSION['login_type'] == 'table') {
			$_SESSION[$conn.'_tableLogin'] = array('user'=>$user,'pass'=>$pass,'first'=>true);		
		}else{
			$_SESSION[$conn.'_login'] = array('user'=>$user,'pass'=>$pass,'first'=>true);
		}
		
		session_write_close();		
		//header('Location: '.$from);
		//exit();
	}
	if(isset($_GET["errorMsg"]) && $_GET["errorMsg"] != '') {
		return $_GET["errorMsg"];
	}else{
		return '';
	}
}



function fmsStoreCache($args, &$cache, $data) {
	$args = md5(serialize($args));
	$cache[$args] = $data;
	return $data;
}

function fmsGetCache($args, &$cache) {
	$args = md5(serialize($args));
	if(isset($cache[$args])) return $cache[$args]; else return false;
}

function fmsLogoutLink($conn, $redirect) {
	global $self_url_clean;
	echo $self_url_clean.'?logout='.urlencode($conn);
	if($redirect != '') {
		echo '&redirect='.urlencode($redirect);
	}
}

function fmsPOST($var) {
	if(!isset($_POST[$var])) return false;
	if(get_magic_quotes_gpc()) {
		return stripslashes($_POST[$var]);
	}else{
		return $_POST[$var];
	}
}

function fmsGET($var) {
	if(!isset($_GET[$var])) return false;
	if(get_magic_quotes_gpc()) {
		return stripslashes($_GET[$var]);
	}else{
		return $_GET[$var];
	}
}

function fmsPOST2GET() {
	if(!count($_POST)) return '';
	unset($_POST['submit']);
	return 'post_data='.base64_encode(serialize($_POST));
}

function fmsGET2POST() {
	$post = fmsGET('post_data');	
	if($post !== false) {
		$post = base64_decode($post);
		$post = unserialize($post);
		if(is_array($post) && count($post) > 0)
		{
			foreach($post as $key=>$var) {
				if(!isset($_POST[$key])) $_POST[$key] = $var;
			}
		}
	}
}



function fmsRelatedRecord($row, $related_name) {
	$records = $row->getRelatedSet($related_name);
	if(FileMaker::isError($records) || count($records) == 0) {
		return new fmsDummyRecord();
	}else{
		return array_shift($records);
	}
}

function fmsRelatedSet($row, $related_name, $empty_record_if_blank = false) {
	$records = $row->getRelatedSet($related_name);
	if(FileMaker::isError($records) || count($records) == 0) {
		if($empty_record_if_blank) {
			return array(new fmsDummyRecord());
		}else{
			return array();
		}
	}else{
		return $records;
	}
}

class fmsDummyRecord {
	function getField() {
		return '';
	}
}

function fmsCompareSet($list_item, $set) {
	if(!is_array($set)) {
		$set = str_replace("\n","\r",$set);
		$set = str_replace("\r\r","\r",$set);
		$set = explode("\r",$set);
	}
	return in_array($list_item, $set);
}

function fmsCheckboxCombine($var) {
	if(is_array($var)) {
		return implode("\r",$var);
	}else{
		return $var;
	}
}



function fmsDecodeAdvDialogValues($input) {
	$input = explode('/;/',$input);
	foreach($input as $key=>$value) {
		$input[$key] = urldecode($value);
	}
	return $input;
}

function fmsUTF8HTMLEntities($str) {
	return htmlentities($str, ENT_COMPAT, "UTF-8");
}

function fmsPrintDate($format, $fmDate) { fmsPrintDateTime($format, $fmDate); }
function fmsPrintDateTime($fmDate, $format) {
	if(preg_match("#^([0-9]+)/([0-9]+)/([0-9]+) ([0-9]+):([0-9]+):([0-9]+)$#", $fmDate, $m)) {
		$time = mktime($m[4], $m[5], $m[6], $m[1], $m[2], $m[3]);
	}else if(preg_match("#^([0-9]+)/([0-9]+)/([0-9]+)$#", $fmDate, $m)){
		$time = mktime(0, 0, 0, $m[1], $m[2], $m[3]);
	}else if(preg_match("#^([0-9]+):([0-9]+):([0-9]+)$#", $fmDate, $m)){
		$time = mktime($m[1], $m[2], $m[3]);
	}else{
		return '';
	}
	return date($format, $time);
}

function fmsPortalFieldName($record, $row, $related_set, $field) {
	$data = array($related_set,$record->getRecordId(),$field);
	//print_r(serialize($data));
	//print_r();
	return "__FMWS_Portals[".fmsEncodeActionArray($data)."]";
}

function fmsPortalNewFieldName($related_set, $field) {
	$data = array($related_set,$field);
	//print_r(serialize($data));
	//print_r();
	return "__FMWS_Portals_New[".fmsEncodeActionArray($data)."][]";
}

function fmsEngineAction($action,$conn_name,$parent,$portal_name) {
	$action_array = array();
	global $self_url;
	
	switch($action) {
		case 'edit_portal':
			$action_array['conn'] = $conn_name;
			$action_array['action'] = 'edit_portal';
			$action_array['portal'] = $portal_name;
			$action_array['parent_layout'] = $parent->getLayout()->getName();
			$action_array['parent_recid'] = $parent->getRecordId();
			$action_array['return_url'] = $self_url;
			break;
	}
	
	
	if(!count($action_array)) return false;
	$action_array = fmsEncodeActionArray($action_array);

	echo "<input type=\"hidden\" name=\"__FMWS_ACTION[]\" value=\"{$action_array}\">";
	return true;
}

$fmsKey = "sertsdnk iodysof  983h id ho hyoa oy g uag h oph  ";

// This function signs an FMStudio command, this prevents the user from tampering with the command and changing things like the record id during an edit
function fmsEncodeActionArray($action_array) {
	global $fmsKey;
	
	$action_array = serialize($action_array);
	$md5 = md5($fmsKey.$action_array);
	return $md5.base64_encode($action_array);
}


// This function checks the signature of a signed FMStudio command
function fmsDecodeActionArray($action_array) {
	global $fmsKey;
	$md5 = substr($action_array,0,32);
	if(strlen($md5) != 32) die('Security alert: command validation failed, hash missing');
	$action_array = base64_decode(substr($action_array,32));
	$md5_to_check = md5($fmsKey.$action_array);
	if($md5 !== $md5_to_check) die('Security alert: command validation failed, invalid hash');
	return unserialize($action_array);
}

function fmsREQUEST($var) {
	if(!isset($_REQUEST[$var])) return false;
	if(get_magic_quotes_gpc()) {
		return stripslashes($_REQUEST[$var]);
	}else{
		return $_REQUEST[$var];
	}
}

function fmsRequestField($field) {
	$value = fmsREQUEST($field);
	if($field == '-lop') {
		if(strtolower($value) == 'or') return 'or'; else return 'and';
	}
	if($value === false) return '';
	if(is_array($value)) $value = implode("\r",$value);
	$value = trim($value);
	$op = fmsREQUEST($field.'_op');
	if(!strlen($value)) return '';
	if($op !== false) {
		switch($op) {
			case 'cn':
				return '*'.$value.'*';
			case 'bw':
				return '=='.$value.'*';
			case 'ew':
				return '==*'.$value;
			case 'eq':
				return '=='.$value;
			case 'lt':
				return '<'.$value;
			case 'lte':
				return '<='.$value;
			case 'gt':
				return '>'.$value;
			case 'gte':
				return '>='.$value;
		}
		return $op.$value;
	}
	return $value;
}

$fmsActionsDecoded = false;
function fmsDecodeActions() {
	global $fmsActionsDecoded;
	$ret = array();
	if($fmsActionsDecoded === false) {
		$actions = fmsREQUEST('__FMWS_ACTION');
		if(is_array($actions)) {
			foreach($actions as $action) {
				$ret[] = fmsDecodeActionArray($action);
			}
		}
		$fmsActionsDecoded = $ret;
	}
	return $fmsActionsDecoded;
}

function fmsConnectionLoaded($connObj,$connName) {
	$actions= fmsDecodeActions();
	if(count($actions)) {
		foreach($actions as $action) {
			if($action['conn'] == $connName) {
				fmsProcessAction($action,$connObj);
			}
		}
		die('Processed actions');
	}
}

function fmsProcessAllActionsAndLoadConnectionsIfNeeded() {
	$actions= fmsDecodeActions();
	if(count($actions)) {
		foreach($actions as $action) {
			if(!isset(${$action['conn']})) {
				$connName = $action['conn'];
				global ${'hostname_'.$connName};
				global ${'username_'.$connName};
				global ${'password_'.$connName};
				global ${'Lusername_'.$connName};
				global ${'Lpassword_'.$connName};
				global ${'TableLogin_'.$connName};
				require_once('../Connections/'.$action['conn'].'.php');
			}
			$connObj = ${$action['conn']};
			fmsProcessAction($action,$connObj);
		}
	}
}

function fmsProcessAction($action,$connObj) {
	switch($action['action']) {
		case 'edit_portal':
			fmsProcessAction_edit_portal($action,$connObj);
			break;
		default:
			die('Unknown action: '.$action['action']);
	}	
	if(isset($action['return_url'])) fmsRedirect($action['return_url']);
}

function fmsProcessAction_edit_portal($action,$connObj) {
	$portals = fmsREQUEST('__FMWS_Portals');
	$portals_new = fmsREQUEST('__FMWS_Portals_New');
	
//			$action_array['action'] = 'edit_portal';
//		$action_array['portal'] = $portal_name;
//	$action_array['parent_layout'] = $parent->getLayout()->getName();
	//$action_array['parent_recid'] = $parent->getRecordId();
	
	$portalToFind = $action['portal'];
	
	$portalData = array();
	if(is_array($portals)) {
		foreach($portals as $portalName=>$portalValue) {
			$portalName = fmsDecodeActionArray($portalName);
			if($portalName[0] == $portalToFind) {
				$rowid = $portalName[1];
				if(!isset($portalData[$rowid])) $portalData[$rowid] = array();
				$portalData[$rowid][$portalName[2]] = $portalValue;
			}
		}
	}
	
	$portalData_New = array();
	if(is_array($portals_new)) {
		foreach($portals_new as $portalName=>$portalValues) {
			$portalName = fmsDecodeActionArray($portalName);
			if($portalName[0] == $portalToFind) {
				foreach($portalValues as $new_row=>$portalValue) {
					if(!isset($portalData_New[$new_row])) $portalData_New[$new_row] = array();
					$portalData_New[$new_row][$portalName[1]] = $portalValue;
				}
			}
		}
		foreach($portalData_New as $row=>$values) {
			$empty = true;
			foreach($values as $value) if($value !== "") $empty = false;
			if($empty) unset($portalData_New[$row]);
		}
	}
	
	$layout = $action['parent_layout'];
	$parent_recid = $action['parent_recid'];
		
	$find = $connObj->newFindCommand($layout);
	$find->addFindCriterion('-recid',$parent_recid);
		
	$result = $find->execute();
		
	if(FileMaker::isError($result)) {
		die('Failed editing portals, seems that the parent record is gone');
	}
	$records = $result->getRecords();
	$record = $records[0];
		
	if(count($portalData)) {
		$portal_rows = $record->getRelatedSet($portalToFind);
		
		foreach($portalData as $rowid=>$fields) {
			$portal_row = NULL;
			foreach($portal_rows as $potential_row) {
				if($potential_row->getRecordId() == $rowid) {
					$portal_row = $potential_row;
					break;
				}
			}
			if(!$portal_row){
				die('Failed editing portals, seems that a portal row is missing');
			}
			$changed = false;
			if(isset($fields['__DELETE__']) && $fields['__DELETE__']) {
				$portal_row->delete();
			}else{
				foreach($fields as $fieldName=>$fieldValue) {
					if($portal_row->getField($fieldName) != $fieldValue) {
						$portal_row->setField($fieldName, $fieldValue);
						$changed = true;
					}
				}
				if($changed) $portal_row->commit();
			}
		}
	}
	
	if(count($portalData_New)) {
		
		foreach($portalData_New as $rowid=>$fields) {
			$portal_row = $record->newRelatedRecord($portalToFind);
			if(FileMaker::isError($portal_row)){
				die('Failed editing portals, could not create a new portal row');
			}
			foreach($fields as $fieldName=>$fieldValue) {
				$portal_row->setField($fieldName, $fieldValue);
			}
			$portal_row->commit();
		}
	}
}


$VALUE_LIST_CACHE = array();
function fmsValueListItems2($conn, $layout, $list, $recid = null, $empty = null, $currentValue = null) {
	if($recid == "") $recid = null;
	global $VALUE_LIST_CACHE;
	
	$cache = fmsGetCache(func_get_args(),$VALUE_LIST_CACHE);
	if($cache !== false) return $cache;
	
	$layoutValueLists = fmsGetCache(array($conn,$layout,$recid),$VALUE_LIST_CACHE);
	if($layoutValueLists === false) {
		$properties = $conn->getProperties();
		$request = '/fmi/xml/FMPXMLLAYOUT.xml?-db='.urlencode($properties['database']).'&-lay='.urlencode($layout).'&-view';
		if($recid && (int)$recid>0) $request.='&-recid='.urlencode((int)$recid);
		
		$ret = fmsExecuteXMLRequest($conn,$request);
		if(isset($ret['error'])) {
			//die('Failed getting value list ('. $list .'). Error: '.$ret['error']); // Uncomment this line to debug
			return fmsStoreCache(func_get_args(),$VALUE_LIST_CACHE,array());
		}
		$ret = $ret['data'];
		$layoutValueLists = fmsXMLGetValueByPath($ret,'/FMPXMLLAYOUT/VALUELISTS');
		fmsStoreCache(array($conn,$layout,$recid),$VALUE_LIST_CACHE,$layoutValueLists);
	}
	
	foreach($layoutValueLists as $valueList) {
		if($valueList['name'] != "VALUELIST") continue;
		if($valueList['attributes']['NAME'] != $list) continue;
		$retVL = array();
		
		if($empty !== null) {
			$retVL[] = array("",htmlspecialchars($empty));
		}
		
		foreach($valueList as $key=>$valueListValue) {
			if(!is_int($key) || $valueListValue['name'] != "VALUE") continue;
			$value = "";
			if(isset($valueListValue['value'])) $value = $valueListValue['value'];
			$display = $value;
			if(isset($valueListValue['attributes']) && isset($valueListValue['attributes']['DISPLAY'])) $display = $valueListValue['attributes']['DISPLAY'];
			$retVL[] = array(htmlspecialchars($value),htmlspecialchars($display));
		}
		if($currentValue !== null) {
			$found = false;
			foreach($retVL as $value) {
				if($value[0] == $currentValue) {
					$found = true;
					break;
				}
			}
			if(!$found && count($retVL)) $retVL[] = array(htmlspecialchars($currentValue),htmlspecialchars($currentValue));
		}
		return fmsStoreCache(func_get_args(),$VALUE_LIST_CACHE,$retVL);
	}
	
	return fmsStoreCache(func_get_args(),$VALUE_LIST_CACHE,array());	
}

// Left here for compatability purposes
function fmsValueListItems($conn, $layout, $list, $recid = null) {
	$ret = fmsValueListItems2($conn, $layout, $list, $recid);
	if(!$ret) return array();
	$clean = array();
	foreach($ret as $item) $clean[] = $item[0];
	return $clean;
}


function fmsExecuteXMLRequest($conn,$requestString, $postData = NULL) {
	$properties = $conn->getProperties();
	$fullUrl = fmsServerRequestURL($properties['hostspec'],$properties['username'],$properties['password'],$requestString);
	$ch = curl_init($fullUrl);
	curl_setopt($ch,CURLOPT_HTTPHEADER, array('X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk='));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
	//curl_setopt($ch,CURLOPT_HEADER, TRUE);
	$data = curl_exec($ch);
	if(!$data) {
		return array('error'=>curl_error($ch));
	}
	
	return array('data'=>fmsXMLToArray($data));
}

function fmsXMLToArray($xmlString)
{
	// Retrieved from PHP.net on May 21, 2009
    $xml_values = array();
    $contents = $xmlString;
    $parser = xml_parser_create('');
    if(!$parser)
        return false;

    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
        return array();
   
    $xml_array = array();
    $last_tag_ar =& $xml_array;
    $parents = array();
    $last_counter_in_tag = array(1=>0);
    foreach ($xml_values as $data)
    {
        switch($data['type'])
        {
            case 'open':
                $last_counter_in_tag[$data['level']+1] = 0;
                $new_tag = array('name' => $data['tag']);
                if(isset($data['attributes']))
                    $new_tag['attributes'] = $data['attributes'];
                if(isset($data['value']) && trim($data['value']))
                    $new_tag['value'] = trim($data['value']);
                $last_tag_ar[$last_counter_in_tag[$data['level']]] = $new_tag;
                $parents[$data['level']] =& $last_tag_ar;
                $last_tag_ar =& $last_tag_ar[$last_counter_in_tag[$data['level']]++];
                break;
            case 'complete':
                $new_tag = array('name' => $data['tag']);
                if(isset($data['attributes']))
                    $new_tag['attributes'] = $data['attributes'];
                if(isset($data['value']) && trim($data['value']))
                    $new_tag['value'] = trim($data['value']);

                $last_count = count($last_tag_ar)-1;
                $last_tag_ar[$last_counter_in_tag[$data['level']]++] = $new_tag;
                break;
            case 'close':
                $last_tag_ar =& $parents[$data['level']];
                break;
            default:
                break;
        };
    }
    return $xml_array;
}

function fmsXMLGetValueByPath($__xml_tree, $__tag_path)
{
    $tmp_arr =& $__xml_tree;
    $tag_path = explode('/', $__tag_path);
    foreach($tag_path as $tag_name)
    {
		if(!strlen($tag_name)) continue;
        $res = false;
        foreach($tmp_arr as $key => $node)
        {
            if(is_int($key) && $node['name'] == $tag_name)
            {
                $tmp_arr = $node;
                $res = true;
                break;
            }
        }
        if(!$res)
            return false;
    }
    return $tmp_arr;
}

$fmsValueListCustomChoice_Ran = false;
function fmsValueListCustomChoice($label) {
	global $FMStudioV2;
	global $fmsValueListCustomChoice_Ran;
	echo "<option value='__FMWS__CUSTOM__'>".htmlspecialchars($label)."</option>";
	if(!$fmsValueListCustomChoice_Ran) {
		$fmsValueListCustomChoice_Ran = true;
		$root_path = $FMStudioV2->root_path_using_caller(debug_backtrace(),__FILE__);
		$FMStudioV2->inject_js_file($root_path."FMStudio_v2/lib/jquery.js");
		$FMStudioV2->inject_js_file($root_path."FMStudio_v2/lib/jquery.form.js");
		$FMStudioV2->inject_js_file($root_path."FMStudio_v2/js/value_lists.js");
	}
}

/*
Creates a live value list and binds it to the record from the recordset
$type = "dropdown","checkbox","radio"
*/
function fmsLiveInputField($conn, $layout, $field, $list, $recid, $type, $current_value, $empty_label, $other_label, $cssClass, $separator) {
	global $FMStudioV2;
	$start = microtime(true);
	
	if($separator !== null && !strlen($separator)) $separator = null;
	if($cssClass !== null && !strlen($cssClass)) $cssClass = null;


	$root_path = $FMStudioV2->root_path_using_caller(debug_backtrace(),__FILE__);
	$FMStudioV2->inject_js_file($root_path."FMStudio_v2/lib/jquery.js");
	$FMStudioV2->inject_js_file($root_path."FMStudio_v2/lib/jquery.form.js");
	$FMStudioV2->inject_js_file($root_path."FMStudio_v2/lib/jquery-ui.js");
	$FMStudioV2->inject_js_file($root_path."FMStudio_v2/js/value_lists.js");

	$FMStudioV2->inject_js_line("var __FMS_Server_Engine = '{$root_path}FMStudio_v2/engine.php';",true);
	$FMStudioV2->inject_js_line("var __FMS_VL_LOOKUP = new Object();",true);
	
	$connectionName = $FMStudioV2->connection_name_from_object($conn);
	$action = $FMStudioV2->get_action_reference($connectionName, $layout, FMSTUDIOV2_ACTION_EDIT,$recid);
	$action->allow_field($field);
	if($list) $action->allow_value_list($list);

	$field_id = $FMStudioV2->uniqueid();

	$FMStudioV2->inject_js_line("var {$field_id}_ACTION = '{$action->ref}';",true);
	
	$FMStudioV2->inject_js_line("__FMS_VL_LOOKUP['{$action->ref}'] = new Array();",true);
	
	if($empty_label === null) $empty_label_js = 'null'; else $empty_label_js = '\''.$empty_label.'\'';
	if($other_label === null) $other_label_js = 'null'; else $other_label_js = '\''.$other_label.'\'';
	if($separator === null) $separator_js = 'null'; else $separator_js = '\''.$separator.'\'';
	$FMStudioV2->inject_js_line("__FMS_VL_LOOKUP['{$action->ref}'].push(Array('{$field_id}','{$list}',$empty_label_js,$other_label_js,$separator_js,'$type','$field'));",true);

	
	
	switch($type) {
		case "static_text":
			echo "<span id=\"{$field_id}\"";
			if($cssClass !== null) {
				echo " class=\"__FMS_LiveValueList __FMS_LiveValueList_StaticText {$cssClass}\"";
			}else{
				echo " class=\"__FMS_LiveValueList __FMS_LiveValueList_StaticText\"";
			}
			echo ">";
			if($current_value !== null) echo htmlspecialchars($current_value);
			echo "</span>";
			break;
		case "text":
			echo "<input name=\"$field\" id=\"{$field_id}\"";
			if($cssClass !== null) {
				echo " class=\"__FMS_LiveValueList {$cssClass}\"";
			}else{
				echo " class=\"__FMS_LiveValueList\"";
			}
			if($current_value !== null) echo " value=\"".htmlspecialchars($current_value)."\"";
			echo ">";
			break;
		case "textarea":
			echo "<textarea name=\"$field\" id=\"{$field_id}\"";
			if($cssClass !== null) {
				echo " class=\"__FMS_LiveValueList {$cssClass}\"";
			}else{
				echo " class=\"__FMS_LiveValueList\"";
			}
			echo ">";
			if($current_value !== null) echo htmlspecialchars($current_value);
			echo "</textarea>";
			break;
		case "dropdown":
			$items = fmsValueListItems2($conn,$layout,$list,$recid,$empty_label,$current_value);
			echo "<select name=\"$field\" id=\"{$field_id}\"";
			if($cssClass !== null) {
				echo " class=\"__FMS_LiveValueList {$cssClass}\"";
			}else{
				echo " class=\"__FMS_LiveValueList\"";
			}
			echo ">";
			foreach($items  as $list_item) {
				if(html_entity_decode($list_item[0]) == $current_value) {
					echo "<option value=\"{$list_item[0]}\" selected=\"selected\">{$list_item[1]}</option>";
				} else {
					echo "<option value=\"{$list_item[0]}\">{$list_item[1]}</option>";
				}
			}
			if($other_label !== null) {
				if(!count($items)) {
					echo "<option value=\"\"></option>";
				}
				echo '<option value="__FMWS__CUSTOM__">'.htmlspecialchars($other_label).'</option>';
			}
			echo "</select>";
			break;
		case "checkbox":
			$items = fmsValueListItems2($conn,$layout,$list,$recid,null,null);
			$name = $field.'[]';
			if($cssClass !== null) {
				echo "<span id=\"{$field_id}\" class=\"__FMS_LiveValueList $cssClass\">";
			}else{
				echo "<span id=\"{$field_id}\" class=\"__FMS_LiveValueList\">";
			}
			foreach($items  as $index => $list_item) {
				$id = $FMStudioV2->uniqueid();
				echo "<input type=\"checkbox\" name=\"$name\" id=\"{$id}\"";
				echo " value=\"{$list_item[0]}\"";
				
				if(fmsCompareSet(html_entity_decode($list_item[0]),$current_value)) {
					echo " checked=\"checked\"";
				}
				echo "> ";
				echo "<label for=\"{$id}\">".$list_item[1]."</label>";
				if($index+1 != count($items) && $separator !== null) {
					echo $separator;
				}
			}
			echo "</span>";
			break;
		case "radio":
			$items = fmsValueListItems2($conn,$layout,$list,$recid,null,null);
			$name = $field;
			if($cssClass !== null) {
				echo "<span id=\"{$field_id}\" class=\"__FMS_LiveValueList $cssClass\">";
			}else{
				echo "<span id=\"{$field_id}\" class=\"__FMS_LiveValueList\">";
			}
			foreach($items  as $index => $list_item) {
				$id = $FMStudioV2->uniqueid();
				echo "<input type=\"radio\" name=\"$name\" id=\"{$id}\"";
				echo " value=\"{$list_item[0]}\"";
				
				if(fmsCompareSet(html_entity_decode($list_item[0]),$current_value)) {
					echo " checked=\"checked\"";
				}
				echo "> ";
				echo "<label for=\"{$id}\">".$list_item[1]."</label>";
				if($index+1 != count($items) && $separator !== null) {
					echo $separator;
				}
			}
			echo "</span>";
			break;
	}
	
}

function fmsStatusLinks($recordsetName) {
	global ${$recordsetName.'_result'};
	$result = ${$recordsetName.'_result'};
	
	global ${$recordsetName.'_find'};
	$find = ${$recordsetName.'_find'};
	
	
	$foundCount = $result->getFoundSetCount();
	$range = $find->getRange();
	$max = $range['max'];
	
	$ret = array(
	'first'=>'First',
	'prev'=>'Prev',
	'records'=>array('rangestart'=>$range['skip']+1,'rangeend'=>$range['skip']+$result->getFetchCount(),'foundcount'=>$foundCount),
	'next'=>'Next',
	'last'=>'Last',
	);
	
	if($ret['records']['rangeend'] > $ret['records']['foundcount']) $ret['records']['rangeend'] = $ret['records']['foundcount'];
	
	
	
	$pages = fmsGetPageCount($recordsetName);
	$currentPage = fmsGetPage($recordsetName);
	if($currentPage < 1) $currentPage = 1;
	
	if($currentPage > 1) {
		$ret['first'] = '<a href="'.htmlspecialchars(fmsFirstPage($recordsetName,$max)).'">First</a>';
		$ret['prev'] = '<a href="'.htmlspecialchars(fmsPrevPage($recordsetName,$max)).'">Prev</a>';
	}
	if($currentPage < $pages) {
		$ret['next'] = '<a href="'.htmlspecialchars(fmsNextPage($recordsetName,$max)).'">Next</a>';
		$ret['last'] = '<a href="'.htmlspecialchars(fmsLastPage($recordsetName,$max)).'">Last</a>';
	}
	
	return $ret;
}
?>