<?php
	include 'exception/ServiceException.php';
	
	abstract class VentriloService {
		abstract function get_XML($host, $port, $pass);
		
		function get_XML_Error($host, $port, $pass, $serviceException) {
			$xml = "<ventrilo>\n";
			$xml .= "<server>\n";
			$xml .= "<host>$host</host>\n";
			$xml .= "<port>$port</port>\n";
			$xml .= "<error no='".$serviceException->getCode()."'>";
			$xml .= "<message>".$this->cdata($serviceException->getMessage())."</message>";
			if($serviceException->isDebugged()) $xml .= "<debug>".$this->cdata($serviceException->getDebuggingInfo())."</debug>";
			$xml .= "</error>\n";
			$xml .= "</server>\n";
			$xml .= "</ventrilo>\n";
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