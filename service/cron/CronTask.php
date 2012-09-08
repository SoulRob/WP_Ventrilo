<?php
	include_once __DIR__.'/../VentriloServiceCRON.php';

	// Grab the Parameters from command line
	$params = getopt ( "", array("host:","port::","pass::") );
	
	if(!$params) {
		echo "Error in Syntax: php CronTask.php --host HOST [--port PORT] [--pass PASS]\n";
		var_dump($params);
		exit -1;
		
	} else if(!isset($params['host'])) {
		echo "-host is required\n";
		var_dump($params);
		exit -1;
	}
	
	$host = $params['host'];
	$port = isset($params['port']) ? $params['port'] : 3784;
	$pass = isset($params['pass']) ? $params['pass'] : null;
	
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