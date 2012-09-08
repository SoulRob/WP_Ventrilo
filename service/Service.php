<?php
	include_once 'VentriloServiceDirect.php';
	include_once 'VentriloServiceCRON.php';	
		
	
	// Require parameters from GET
	$host = isset($_GET["host"]) ? $_GET["host"] : "192.168.0.1";
	$port = isset($_GET["port"]) ? $_GET["port"] : "3784";
	$pass = isset($_GET["pass"]) ? $_GET["pass"] : "";
	$prettyprint = strcasecmp( (!isset($_GET["prettyprint"]) ? "false" : $_GET["prettyprint"]), "true" )==0;  
	
	$impl = new VentriloServiceCRON();
	if(!$impl instanceof VentriloService){
		throw new ServiceException("Configuration error: ".get_class($impl)." does not implement VentriloService");
	}
		
	try {		
		$xml = $impl->get_status($host, $port, $pass);
			
		$parser = xml_parser_create("UTF-8");
		if (0==xml_parse($parser, $xml, TRUE)){
			$code = xml_get_error_code($parser);
			$ex = new ServiceException("Failed to Parse XML: ".xml_error_string($code), $code, $xml);
			
			throw $ex;
		}	
		
		echo "<?xml-stylesheet type='text/xsl' href='ventrilo_status.xsl'?>\n";
		if ( $prettyprint ){
			$dom = new DOMDocument;
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($xml);
			$dom->formatOutput = TRUE;
			$xml = $dom->saveXml();
		} else {
			echo "<?xml version='1.0'?>\n";
		}
		
		echo $xml;
	} catch (ServiceException $ex) {
		echo $impl->get_XML_Error($host, $port, $pass, $ex);
	}
?>
