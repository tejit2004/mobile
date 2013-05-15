<?php

class FMStudioV2_Injection {
	
	static function factory() {
		global $FMStudioV2_Injection;
		if(!@($FMStudioV2_Injection)) $FMStudioV2_Injection = new FMStudioV2_Injection();
		return $FMStudioV2_Injection;
	}

	var $_js_files_to_inject;
	// Calling this function injects the JS file reference at the end of the page load
	function _inject_js_file($file) {
		$this->_add_method_to_finalize($this,"inject_js_file_finalize");
		if(!is_array($this->_js_files_to_inject)) $this->_js_files_to_inject = array();
		if(!isset($this->_js_files_to_inject[$file])) $this->_js_files_to_inject[$file] = $file;
	}
	
	function inject_js_file_finalize($content) {		
		$to_inject = "";
		foreach($this->_js_files_to_inject as $file) {
			$to_inject.= '<script type="text/javascript" src="'.htmlspecialchars($file).'"></script>';;
		}
		
		$body_pos = stripos($content,"</body>");
		if($body_pos !== false) {
			$content = substr($content,0,$body_pos).$to_inject.substr($content,$body_pos);
		}else{
			$content.=$to_inject;
		}
		return $content;
	}
	
	var $_js_lines_to_inject;
	function _inject_js_line($line,$unique = false) {
		$this->_add_method_to_finalize($this,"inject_js_line_finalize");
		if(!is_array($this->_js_lines_to_inject)) $this->_js_lines_to_inject = array();
		if(!$unique) {
			$this->_js_lines_to_inject[] = $line;
		}else{
			$this->_js_lines_to_inject[$line] = $line;
		}
	}
	
	function inject_js_line_finalize($content) {
		$to_inject = "<script type=\"text/javascript\">";
		foreach($this->_js_lines_to_inject as $line) {
			$to_inject.= "\n".$line;
		}
		$to_inject.= "\n</script>";
		
		$body_pos = stripos($content,"</body>");
		if($body_pos !== false) {
			$content = substr($content,0,$body_pos).$to_inject.substr($content,$body_pos);
		}else{
			$content.=$to_inject;
		}
		return $content;
	}
	
	
	
	var $_finalization_methods;
	function _will_need_to_finalize() {
		static $first = true;
		if($first) {
			$first = false;
			ob_start(array($this,"_finalizing"));
		}
	}
	
	function _add_method_to_finalize($object,$method) {
		$this->_will_need_to_finalize();
		if(!is_array($this->_finalization_methods)) $this->_finalization_methods = array();
		$exists = false;
		foreach($this->_finalization_methods as $callback) {
			if($callback[0] === $object && $callback[1] === $method) {
				$exists = true;
				break;
			}
		}
		if(!$exists) {
			$this->_finalization_methods[] = array($object,$method);
		}
	}
	
	function _finalizing($content) {
		if(is_array($this->_finalization_methods)) {
			foreach($this->_finalization_methods as $callback) {
				$ret = call_user_func($callback,$content);
				if($ret) $content = $ret;
			}
		}
		return $content;
	}

}

?>