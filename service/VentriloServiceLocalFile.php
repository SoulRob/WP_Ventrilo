<?php
include_once "VentriloService.php";

class VentriloServiceLocalFile extends VentriloService {
	
	function get_XML($host, $port, $pass) {
		$myFile = $this->get_file($host, $port, $pass);
		
		if(!file_exists($myFile)) {
			throw new ServiceException("Cache file does not exist, please see installation instructions", 0, $myFile);
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
		return dirname(__FILE__) . "/cron/$host-$port.xml";
	}
}
?>