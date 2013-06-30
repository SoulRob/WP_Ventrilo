<?php
	include 'exception/ServiceException.php';
	
	abstract class VentriloService {
		abstract function get_XML($host, $port, $pass);
		
		function get_XML_Error($host, $port, $pass, $serviceException) {
			$xml = "<ventrilo ";
			$xml .= "address=\"$host:$port\" ";
			$xml .= "errorno=\"".$serviceException->getCode()."\" ";
			$xml .= "message=\"".$serviceException->getMessage()."\" ";
			$xml .= "/>\n";
			return $xml;
		}
		
		function get_status($host, $port, $pass){
			try {
				return $this->get_XML($host, $port, $pass);
			} catch (ServiceException $ex) {
				return $impl->get_XML_Error($host, $port, $pass, $ex);
			}
		}
		
		function cdata($string) {
			return (strpos($string, "&")!==false || strpos($string, "<")!==false ) ? "<![CDATA[".$string."]]>" : $string;
		}		
	}

?>