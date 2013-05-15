<?php

define('FMSTUDIOV2_ACTION_EDIT','edit');

class FMStudioV2_Engine {
	
	function FMStudioV2_Engine() {
		if(!session_id()) session_start();
	}
	
	static function factory() {
		global $FMStudioV2_Engine;
		if(!@($FMStudioV2_Engine)) $FMStudioV2_Engine = new FMStudioV2_Engine();
		return $FMStudioV2_Engine;
	}
	
	
	function get_action_reference($connectionName, $layoutName, $actionName,$recid) {
		if(!session_id()) session_start();
		if(!isset($_SESSION['__FMS__V2__'])) $_SESSION['__FMS__V2__'] = array();
		if(!isset($_SESSION['__FMS__V2__']['actions'])) $_SESSION['__FMS__V2__']['actions'] = array();
		
		$id = $connectionName.'-'.$layoutName.'-'.$actionName.'-'.$recid;
		
		$found = null;
		foreach($_SESSION['__FMS__V2__']['actions'] as $ref=>$action) {
			if($action->id == $id) {
				$found = $action;
				break;
			}
		}
		
		if($found) return $found;
		$rand = rand(1,getrandmax());
		while(isset($_SESSION['__FMS__V2__']['actions'][$rand])) { $rand = rand(1,getrandmax()); }
		
		$action = new FMStudioV2_Engine_Action($id,$rand,$recid,$connectionName,$layoutName,$actionName);
		$_SESSION['__FMS__V2__']['actions'][$rand] = $action;
		return $action;
	}
	
	function lookup_action($id) {
		if(!session_id()) session_start();
		if(!isset($_SESSION['__FMS__V2__'])) $_SESSION['__FMS__V2__'] = array();
		if(!isset($_SESSION['__FMS__V2__']['actions'])) $_SESSION['__FMS__V2__']['actions'] = array();
		
		if(isset($_SESSION['__FMS__V2__']['actions'][$id])) return $_SESSION['__FMS__V2__']['actions'][$id];
		
		die('Unknown action ID!');
	}
	
	function get_connection_object($action) {
		$connName = $action->connectionName;
		global ${'hostname_'.$connName};
		global ${'username_'.$connName};
		global ${'password_'.$connName};
		global ${'Lusername_'.$connName};
		global ${'Lpassword_'.$connName};
		global ${'TableLogin_'.$connName};
		require_once('../Connections/'.$connName.'.php');
		$connObj = ${$action->connectionName};
		return $connObj;
	}
	
	function engine_process_request() {
		$id = fmsREQUEST('__FMS_ACTION_ID');
		$data = fmsREQUEST('__FMS_ACTION_DATA');
		
		
		if($data) {
			$var_array = array();
			parse_str($data,$var_array);
			$data = $var_array;
		}
		if(!$data) $data = array();
		
		//var_dump($data); die();
		
		$view = fmsREQUEST('__FMS_ACTION_VIEW');
		if(!$view) $view = 'data';
		
		$action = $this->lookup_action($id);
		
		if(!$action->fields_allowed($data)) {
			die('Fields not allowed');
		}
		
		$ret = array();
		
		switch($action->actionName) {
			case FMSTUDIOV2_ACTION_EDIT:
				$conn = $this->get_connection_object($action);
				$edit = $conn->newEditCommand($action->layoutName,$action->recid);
				foreach($data as $key=>$value) {
					if(is_array($value)) $value = fmsCheckboxCombine($value);
					$edit->setField($key,str_replace("\n","\r",$value));
				}
				$result = $edit->execute();
				if(FileMaker::isError($result)) {
					$code = $result->code;
					$msg = $result->getErrorString();
					die("ERROR: $msg($code)");
				}
				$record = $result->getRecords();
				$record = $record[0];
				if($view == 'layout') {
					$ret['value_lists'] = array();
					foreach($action->allowedValueLists as $valueListName) {
						$items = fmsValueListItems2($conn,$action->layoutName,$valueListName,$action->recid,null,null);
						$ret['value_lists'][$valueListName] = $items;
					}
				}
				$ret['record'] = array();
				foreach($record->getFields() as $fieldName) {
					$ret['record'][$fieldName] = $record->getField($fieldName);
				}
				break;
			default:
				die('Unknown action: '.$action->actionName);
		}
		require_once('JSON.php');
		$json = new Services_JSON();
		$ret = $json->encode($ret);
		echo $ret;
		die();
	}
}

class FMStudioV2_Engine_Action {
	var $id;
	var $ref;
	var $recid;
	var $connectionName;
	var $layoutName;
	var $actionName;
	
	var $allowedRecIds;
	var $allowedFields;
	var $allowedValueLists;
	
	function FMStudioV2_Engine_Action($id, $ref, $recid, $connectionName, $layoutName, $actionName) {
		$this->id = $id;
		$this->ref = $ref;
		$this->recid = $recid;
		
		$this->connectionName = $connectionName;
		$this->layoutName = $layoutName;
		$this->actionName = $actionName;
		
		$this->allowedRecIds = array();
		$this->allowedFields = array();
		$this->allowedValueLists = array();
	}
	
	function allow_recid($value) {
		$this->allowedRecIds[$value] = $value;
	}
	
	function allow_field($value) {
		$this->allowedFields[$value] = $value;
	}
	
	function fields_allowed($fields) {
		$keys = array_keys($fields);
		foreach($fields as $key=>$field) {
			if(!isset($this->allowedFields[$key])) return false;
		}
		return true;
	}
	
	function allow_value_list($value) {
		$this->allowedValueLists[$value] = $value;
	}
}

?>