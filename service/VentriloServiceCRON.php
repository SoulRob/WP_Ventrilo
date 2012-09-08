<?php
include_once "VentriloServiceDirect.php";

class VentriloServiceCRON extends VentriloServiceDirect {
	
	/*
	 * This Service will load the XML from a file that's generated from a CRON
	 */
	function get_status($host, $port, $pass) {
		$myFile = $this->get_file($host, $port, $pass);
		
		if(!file_exists($myFile)) {
			return $this->get_XML_Error($host, $port, $pass, new ServiceException("Cache file does not exist", 0, $myFile));
		}
		
		$fh = fopen($myFile, 'r');
		$theData = fread($fh, filesize($myFile));
		fclose($fh);
		return $theData;
	}
	
	/*
	 * Writes the given XML to disk
	 */
	function write_cache($file, $xml) {
		$fh = fopen($file, 'w');
		fwrite($fh, $xml);
		fclose($fh);
	}
	
	/*
	 * Gets the name of the cache file using passed parameters
	 */
	function get_file($host, $port, $pass) {
		return __DIR__ . "/cron/$host-$port.cache";
	}
}
?>