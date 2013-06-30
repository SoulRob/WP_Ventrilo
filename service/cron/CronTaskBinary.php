<?php
	include_once dirname(__FILE__).'/../VentriloServiceLocalFile.php';

	// Grab the Parameters from command line
	$params = getopt ( "h:o::a::" );
	
	if(!$params) {
		echo "Error in Syntax: php CronTaskBinary.php --h HOST [--o PORT] [--a PASS]\n";
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
	
	$impl = new VentriloServiceCRON();
	$file = $impl->get_file($host, $port, $pass);
	
	echo "Retrieving XML from server $host:$port\n";
	try {
		$xml = $impl->get_XML($host, $port, $pass);
	} catch (ServiceException $ex ){ 
		echo "Encountered an error; rendering error XML instead\n";
		$xml = $impl->get_XML_Error($host, $port, $pass, $ex);
	}
	
	echo "Writing XML to file: $file\n";
	$impl->write_cache($file, $xml);
	
	echo "Done.\n"
?>
