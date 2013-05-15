<?php require_once('../../Connections/FMS10.php'); ?>
<?php require_once('../../Connections/MainTest.php'); ?>
<?php
$profile_find = $FMS10->newFindCommand('Visitor Profile');
$profile_findCriterions = array('visitorId'=>'=='.fmsEscape($_REQUEST['id']),);
foreach($profile_findCriterions as $key=>$value) {
    $profile_find->AddFindCriterion($key,$value);
}

fmsSetPage($profile_find,'profile',10); 

$profile_result = $profile_find->execute(); 

if(FileMaker::isError($profile_result)) fmsTrapError($profile_result,"error.php"); 

fmsSetLastPage($profile_result,'profile',10); 

$profile_row = current($profile_result->getRecords());

 

$new_record_add = $MainTest->newAddCommand('N@ughty! "WHOO" Database~');
$new_record_fields = array('field with spaces'=>'*',);
foreach($new_record_fields as $key=>$value) {
    $new_record_add->setField($key,$value);
}

$new_record_result = $new_record_add->execute(); 

if(FileMaker::isError($new_record_result)) fmsTrapError($new_record_result,"error.php"); 

$new_record_row = current($new_record_result->getRecords()); 
 // FMStudio v2 - do not remove comment, needed for DreamWeaver support ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Site Visitor Profile</title>
<style type="text/css">
<!--
.MyCSSClass {
	color: #000;
}
-->
</style>
</head>

<body>
<h1>FMStudio v2 Demo - Live Fields</h1>
<h2>Site Visitor Profile</h2>
<form action="profile_save.php" method="post" name="profile" id="profile">
  <table>
    <tr>
      <th>Name</th>
      <td><?php fmsLiveInputField($FMS10, 'Visitor Profile', "name", null, $profile_row->getRecordId(), "text", $profile_row->getFieldUnencoded("name"), null, null, "MyCSSClass", null); ?></td>
    </tr>
    <tr>
      <th>Company</th>
      <td><?php fmsLiveInputField($FMS10, 'Visitor Profile', "company", null, $profile_row->getRecordId(), "textarea", $profile_row->getFieldUnencoded("company"), null, null, "MyCSSClass", null); ?></td>
    </tr>
	
    <tr>
      <th>Location</th>
      <td><?php fmsLiveInputField($FMS10, 'Visitor Profile', "location", "Locations", $profile_row->getRecordId(), "dropdown", $profile_row->getFieldUnencoded("location"), "-- Select Location --", "Other...", "MyCSSClass", null); ?></td>
    </tr>
	
	<tr>
      <th>State/Province</th>
      <td><?php fmsLiveInputField($FMS10, 'Visitor Profile', "state", "States", $profile_row->getRecordId(), "dropdown", $profile_row->getFieldUnencoded("state"), "", "Other...", "MyCSSClass", null); ?></td>
    </tr>
	
    <tr>
      <th>Industry</th>
      <td>
	  <?php fmsLiveInputField($FMS10, 'Visitor Profile', "industryId", "Industries", $profile_row->getRecordId(), "dropdown", $profile_row->getFieldUnencoded("industryId"), "-- Please Select --", null, null, null); ?>
	  </td>
    </tr>
    <tr>
      <th>Position</th>
      <td><?php fmsLiveInputField($FMS10, 'Visitor Profile', "position", "Industry Positions", $profile_row->getRecordId(), "dropdown", $profile_row->getFieldUnencoded("position"), "-- Please Select --", "Other...", null, null); ?>	  
	  </td>
    </tr>
	<tr>
      <th>Position Details<br>(Check all that apply)</th>
      <td><?php fmsLiveInputField($FMS10, 'Visitor Profile', "positionDetail", "Position Detail", $profile_row->getRecordId(), "checkbox", $profile_row->getFieldUnencoded("positionDetail"), "-- Select Location --", "Other...", "MyCSSClass", "<br>"); ?></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td><input type="submit" name="profile_submit" value="Submit" /></td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
</form>
</p>
</body>
</html>
