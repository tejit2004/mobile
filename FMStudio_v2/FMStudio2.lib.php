<?php

define('FMStudioV2_VERSION','1.0');

class FMStudioV2 {
	var $_injection;
	var $_engine;
	
	function FMStudioV2() {
		$this->init_globals();
	}
	
	function init_globals() {
		global $fmsMIME_TYPES;
		global $self_url,$self_url_clean;
		
		$self_url = $this->self_url();
		$self_url_clean = $this->self_url(true);


		require_once('php/mime.php');
		$fmsMIME_TYPES = FMStudio2_MIME_Types();
		
		require_once('php/FMStudio2_Injection.lib.php');
		$this->_injection = FMStudioV2_Injection::factory();
		
		require_once('php/FMStudio2_Engine.lib.php');
		$this->_engine = FMStudioV2_Engine::factory();
	}
	
	function version() {
		return FMStudioV2_VERSION;
	}
	
	function uniqueid() {
		static $c = 0;
		$c++;
		return "__FMS".$c;
	}
	
	function root_path_using_caller($backtrace,$called_file) {
		$call = $backtrace[0];
		$file = $call['file'];
		
		$rootDir = dirname(dirname(__FILE__));
		$diff = str_replace($rootDir,".",dirname($file));
		if($diff == '.') return "";
		
		$count = substr_count($diff,'/');
		if(!$count) $count = substr_count($diff,'\\');
		return str_repeat("../",$count);
	}
	
	function self_url($clean = false) {
		$self_url = $_SERVER['PHP_SELF'];
		if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') $self_url.='?'.$_SERVER['QUERY_STRING'];
		if($clean) {
			$self_url_clean = preg_replace('#\?.*$#','',$self_url);
			return $self_url_clean;
		}
		return $self_url;
	}
	
	function connection_name_from_object($obj) {
		$class_name = get_class($obj);
		foreach($GLOBALS as $name=>$potential_obj) {
			if(is_object($obj) && is_a($potential_obj,$class_name)) {
				return $name;
			}
		}
		return NULL;
	}
	
	function CSV_Export($recordsetName,$fields,$dataRange,$settings) {
		global ${$recordsetName.'_result'};
		$recordset = ${$recordsetName.'_result'};
		
		$records = $recordset->getRecords();
		if($dataRange == 'full') {
			if($recordset->getFoundSetCount() != $recordset->getFetchCount()) {
				global ${$recordsetName.'_find'};
				$find = ${$recordsetName.'_find'};
				$find->setRange(0,5000);
				$recordset = $find->execute();
				$records = $recordset->getRecords();
			}
		}
		
		$settings = fmsDecodeAdvDialogValues($settings);
				
		$ret = '';
		if(strtolower($settings[1]) == 'yes') {
			foreach($fields as $field) {
				$fieldName = $field[1];
				$fieldName = $this->CSV_Export_Encode_Value($fieldName);
				$ret.=$fieldName.',';
			}
			$ret[strlen($ret)-1] = "\n";
		}
			
		foreach($records as $record) {
			foreach($fields as $field) {
				$fieldData = $record->getFieldUnencoded($field[0]);
				$fieldData = $this->CSV_Export_Encode_Value($fieldData);
				$ret.=$fieldData.',';
			}
			$ret[strlen($ret)-1] = "\n";
		}
		$ret = substr($ret,0,-1);
		
		header('Content-type: text/csv; charset=utf-8');
		
		
		$fileName = $settings[0];
		if(strtolower($settings[2]) == 'inline') {
			header('Content-Disposition: inline; filename="'.$fileName.'"');
		}else{
			header('Content-Disposition: attachment; filename="'.$fileName.'"');
		}
		
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		
		echo $ret;
		die();
	}
	
	function CSV_Export_Encode_Value($value) {
		return '"'.str_replace(array('"'),array('""'),$value).'"';
	}
	
	/*
		The functions below are delegated to other sub classes
	*/
	function inject_js_file($file) { return $this->_injection->_inject_js_file($file); }
	function inject_js_line($line,$unique = false) { return $this->_injection->_inject_js_line($line,$unique); }

	function get_action_reference($connectionName, $layoutName, $actionName, $recid) { return $this->_engine->get_action_reference($connectionName, $layoutName, $actionName, $recid); }
	function engine_process_request() { return $this->_engine->engine_process_request(); }
	
}


$FMStudioV2 = new FMStudioV2();

?>