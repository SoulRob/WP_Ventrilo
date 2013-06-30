<?php
	include_once dirname(__FILE__).'/../VentriloServiceLocalFile.php';
	require_once "HTTP/Request.php";
	
	// Grab the Parameters from command line
	$params = getopt ( "h:o::a::" );
	
	if(!$params) {
		echo "Error in Syntax: php CronTaskVentSpy.php --h HOST [--o PORT] [--a PASS]\n";
		var_dump($params);
		exit -1;
		
	} else if(!isset($params['h'])) {
		echo "-h is required for the HOST\n";
		var_dump($params);
		exit -1;
	}
	
	$host = $params['h'];
	$port = isset($params['o']) ? $params['o'] : 3784;
	$pass = isset($params['a']) ? $params['a'] : null;
	
	$impl = new VentriloServiceLocalFile();
	$file = $impl->get_file($host, $port, $pass);
	
	echo "Retrieving XML from server $host:$port\n";
	
	try {
		
		$req =& new HTTP_Request("http://services.eetara.com/ventrilo/"
				. $host.":".$port);
		
		if (!PEAR::isError($req->sendRequest())) {
			$xml = $req->getResponseBody();
		}
		
	} catch (ServiceException $ex ){ 
		echo "Encountered an error; rendering error XML instead\n";
		$xml = $impl->get_XML_Error($host, $port, $pass, $ex);
	}
	
	echo "Writing XML to file: $file\n";
	$impl->write_cache($file, $xml);
	
	echo "Done.\n"
?>
