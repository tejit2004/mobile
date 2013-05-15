<?php 

require_once('FMStudio_Tools.php');
if(fmsREQUEST('__FMS_ACTION_ID')) {
	$FMStudioV2->engine_process_request();

	die('stop');
}

fmsProcessAllActionsAndLoadConnectionsIfNeeded();


// FMStudio v2 - do not remove comment, needed for DreamWeaver support ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>FMStudio v2 PHP Engine</title>
</head>

<body>
This is the page of the secure FMStudio v2 PHP Engine. Normally a user should not be seeing this page, please report it as a bug.
</body>
</html>
