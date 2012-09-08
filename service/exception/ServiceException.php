<?php

class ServiceException extends Exception {
	private $debug = null;
	
    public function __construct($message = null, $code = 0, $debug=null) {
        parent::__construct($message, $code);
        $this->setDebugginInfo($debug);
    }
	
	function setDebugginInfo($info) {
		$this->debug = $info;
	}
	
	function getDebuggingInfo(){
		return $this->debug;
	}
	
	function isDebugged() {
		return isset($this->debug);
	}
}