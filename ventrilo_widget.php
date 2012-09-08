<?php
/**
 * Plugin Name: Ventrilo Widget
 * Plugin URI: http://www.robweeks.net
 * Description: 
 * Version: 1.0
 * Author: Rob Weeks
 * Author URI: http://www.robweeks.net
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

add_action('widgets_init', 'vsd_load_widgets' ); 
add_action('wp_head', 'vsd_get_javascript' );
//add_action('init', 'vsd_include_javascript');
add_action('wp_head', 'vsd_onload_event');
add_action('wp_ajax_vsd_get_status', 'vsd_get_status');
add_action('wp_ajax_nopriv_vsd_get_status', 'vsd_get_status');


/**
 * Register our widget.
 *
 */
function vsd_load_widgets() {
	register_widget( 'VentriloStatus_Widget' );
}

/**
 * Includes the Javascript source file we need to process the response
 *
 */
function vsd_include_javascript() {
	wp_deregister_script('vsd_javascript');
	wp_register_script('vsd_javascript', plugins_url('ventrilo_status.js.php', __FILE__ ));
	wp_enqueue_script('vsd_javascript');
}    

/**
 * The XML Web Service to return the XML for the status of the server
 *
 */
function vsd_get_status() {
	$results_id = $_POST['results_div_id'];

	$error = "";
	try {
		$response = _vsd_getXML();
		header('Content-type: text/xml');
		die($response);
	} catch (Exception $ex) {
		$error = $ex->getMessage();
		die("alert('$error');");
	}
}

/**
 * Gets the XML for the ventrilo status
 *
 */
function _vsd_getXML() {
	include "service/VentriloServiceDirect.php";
	
	$before_widget = sprintf('<div class="widget %s">', $widget_obj->widget_options['classname']);

	$host = isset($_GET["host"]) ? $_GET["host"] : _getSetting('host'); 
	$port = isset($_GET["port"]) ? $_GET["port"] : _getSetting('port'); 
	$pass = isset($settings['password']) ? _getSetting('password') : ""; 
	
	$impl = new VentriloServiceDirect();
	return $impl->get_XML($host, $port, $pass);
}

/**
 * 
 *
 */
function vsd_get_javascript() {
	?>
	<script type="text/javascript">
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
						vsd_renderXML(document.getElementById('ventrilo_status_area'), xmlhttp.responseXML);
					}
				}

				// Send the request off
				var params = "action=vsd_get_status";
				xmlhttp.open('POST','<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php',true);
				xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');
				xmlhttp.setRequestHeader("Content-length", params.length);

				xmlhttp.send(params);
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

				xhttp.open('GET', '<?php echo plugins_url('ventrilo_status.xsl', __FILE__ );?>', false);
				xhttp.send('');
				return xhttp.responseXML;
			} catch (ex) {	
				alert(ex);
			}
		}
		
		function vsd_renderXML(divElement, xmlDocument) {
			try {		
				xsl = vsd_getXSLT();

				// code for IE
				if (window.ActiveXObject) {
					ex = xmlDocument.transformNode(xsl);
					divElement.innerHTML=ex;
				} else if (document.implementation && document.implementation.createDocument) {
					// code for Mozilla, Firefox, Opera, etc.
					xsltProcessor = new XSLTProcessor();
					xsltProcessor.importStylesheet(xsl);
					resultDocument = xsltProcessor.transformToFragment(xmlDocument, document);
					divElement.innerHTML = '';
					divElement.appendChild(resultDocument);
				}
			} catch (ex) {	
				alert(ex);
			}
		}	
	</script>
	<?php
 }
 
/**
 * Fire's off the population of the Widget when the page has finishedloading
 *
 */
function vsd_onload_event() {
	?>
		<script type="text/javascript">
			vsd_reload();
		</script>
	<?php		
} 

$vsd_default_settings = array( 
	'title' => __('Ventrilo Status', 'ventrilo_status'), 			
	'host' => '127.0.0.1', 
	'port' => '3784', 
	'password' => '', 
//	'progpath' => '/usr/sbin/ventsrv/ventrilo_status', 
	'use_ventspy' => false, 
);

function _setSetting($name, $value) {
	delete_option("vsd_option_".$name);
	return add_option("vsd_option_".$name, $value);
}

function _getSetting($name) {
	return get_option("vsd_option_".$name, $vsd_default_settings[$name]);
}


/**
 * The Actual Widget itself
 *
 */
class VentriloStatus_Widget extends WP_Widget {
	function VentriloStatus_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'ventrilo_status', 'description' => __('Displays the status of a Ventrilo server.', 'ventrilo_status') );

		/* Widget control settings. */
		$control_ops = array( 
			//'width' => 300, 
			//'height' => 350, 
			'id_base' => 'ventrilo_status_widget',
		);

		/* Create the widget. */
		$this->WP_Widget( 'ventrilo_status_widget', __('Ventrilo Status Widget', 'ventrilo_status'), $widget_ops, $control_ops );
	}

	function form($instance) {
		?>
	
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'ventrilo_status'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" 
				name="<?php echo $this->get_field_name( 'title' ); ?>" 
				value="<?php echo _getSetting('title'); ?>" />
		</p>

		<!-- Hostname -->
		<p>
			<label for="<?php echo $this->get_field_id( 'host' ); ?>"><?php _e('Host:', 'ventrilo_status'); ?></label>
			<input id="<?php echo $this->get_field_id( 'host' ); ?>" 
				name="<?php echo $this->get_field_name( 'host' ); ?>" 
				value="<?php echo _getSetting('host'); ?>" />
		</p>

		<!-- Port -->
		<p>
			<label for="<?php echo $this->get_field_id( 'port' ); ?>"><?php _e('Port:', 'ventrilo_status'); ?></label>
			<input id="<?php echo $this->get_field_id( 'port' ); ?>" 
				name="<?php echo $this->get_field_name( 'port' ); ?>" 
				value="<?php echo _getSetting('port'); ?>" />
		</p>

		<!-- Password -->
		<p>
			<label for="<?php echo $this->get_field_id( 'password' ); ?>"><?php _e('Password:', 'ventrilo_status'); ?></label>
			<input id="<?php echo $this->get_field_id( 'password' ); ?>" 
				name="<?php echo $this->get_field_name( 'password' ); ?>" 
				value="<?php echo _getSetting('password'); ?>" />
		</p>

		<!-- Program Path 
		<p>
			<label for="<?php echo $this->get_field_id( 'progpath' ); ?>"><?php _e('Progam path:', 'ventrilo_status'); ?></label>
			<input id="<?php echo $this->get_field_id( 'progpath' ); ?>" 
				name="<?php echo $this->get_field_name( 'progpath' ); ?>" 
				value="<?php echo _getSetting('progpath'); ?>" />
		</p>-->

		<!-- Use Ventspy? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" "<?php echo _getSetting('use_ventspy')=="on"?"checked":"" ?>
				id="<?php echo $this->get_field_id( 'use_ventspy' ); ?>" 
				name="<?php echo $this->get_field_name( 'use_ventspy' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'use_ventspy' ); ?>"><?php _e('Use VentSpy?', 'ventrilo_status'); ?></label>
		</p>
<?php
	}

	function update($new_instance, $old_instance) {
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['host'] = strip_tags($new_instance['host']);
		$instance['port'] = strip_tags($new_instance['port']);
		$instance['password'] = strip_tags($new_instance['password']);
//		$instance['progpath'] = strip_tags($new_instance['progpath']);

		/* No need to strip tags for sex and show_sex. */
		$instance['use_ventspy'] = $new_instance['use_ventspy'];
		
		_setSetting("title", $instance['title'] );
		_setSetting("host", $instance['host'] );
		_setSetting("port", $instance['port'] );
		_setSetting("password", $instance['password'] );
//		_setSetting("progpath", $instance['progpath'] );
		_setSetting("use_ventspy", $instance['use_ventspy'] );
		
		return $instance; 
	}

	function widget($args, $instance) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
	
		// The place the status will go
		echo "<div id='ventrilo_status_area'></div>";
		//echo "<a href='javascript:vsd_reload();'><font>Click Me</font></a>";

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
}

