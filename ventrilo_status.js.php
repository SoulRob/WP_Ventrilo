<?php
	$path = substr($_SERVER["SCRIPT_NAME"], 0, strlen(basename(__FILE__))*-1);
?>
function vsd_reload() {
	try{
		// The place we're going to render to
		var divTag = document.getElementById('ventrilo_status_area');

		var xmlhttp;
		if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else { // code for IE6, IE5
			xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');	
		}

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				// Everything was fine, let's render the result
				vsd_renderXML(xmlhttp.responseXML);
			}
		}

		// Send the request off
		xmlhttp.open('POST','<?php echo $path.'ventrilo_status.php';?>',true);
		xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		xmlhttp.send('');
	} catch (ex) {	
		alert(ex);
	}
}

function vsd_getXSLT() {
	try {
		if (window.XMLHttpRequest) {
			xhttp=new XMLHttpRequest();
		} else {
			xhttp=new ActiveXObject('Microsoft.XMLHTTP');
		}

		xhttp.open('GET', '<?php echo $path.'ventrilo_status.xsl';?>', false);
		xhttp.send('');
		return xhttp.responseXML;
	} catch (ex) {	
		alert(ex);
	}
}

function vsd_renderXML(xmlDocument) {
	try {				
		// The place we're going to render to
		var divTag = document.getElementById('ventrilo_status_area');

		xsl = vsd_getXSLT();

		// code for IE
		if (window.ActiveXObject) {
			ex = xmlDocument.transformNode(xsl);
			divTag.innerHTML=ex;
		} else if (document.implementation && document.implementation.createDocument) {
			// code for Mozilla, Firefox, Opera, etc.
			xsltProcessor = new XSLTProcessor();
			xsltProcessor.importStylesheet(xsl);
			resultDocument = xsltProcessor.transformToFragment(xmlDocument, document);
			divTag.innerHTML = '';
			divTag.appendChild(resultDocument);
		}
	} catch (ex) {	
		alert(ex);
	}
}