	<?php
		if($_POST['vsd_hidden'] == 'Y') {
			//Form data sent
			$progpath = $_POST['vsd_progpath'];
			update_option('vsd_progpath', $progpath);

			$host = $_POST['vsd_host'];
			update_option('vsd_host', $host);

			$port = $_POST['vsd_port'];
			update_option('vsd_port', $port);

			$pass = $_POST['vsd_pass'];
			update_option('vsd_pass', $pass);

			?>
			<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
			<?php
		} else {
			//Normal page display
			$progpath = get_option('vsd_progpath');
			$host = get_option('vsd_host');
			$port = get_option('vsd_port');
			$pass = get_option('vsd_pass');

		}
	?>
			<div class="wrap">
			<?php    echo "<h2>" . __( 'Ventrilo Status Display Options', 'vsd_trdom' ) . "</h2>"; ?>

			<form name="vsd_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="vsd_hidden" value="Y">
				<?php    echo "<h4>" . __( 'OSCommerce Database Settings', 'vsd_trdom' ) . "</h4>"; ?>
				<p><?php _e("Status Program Path: " ); ?><input type="text" name="vsd_progpath" value="<?php echo $progpath; ?>" size="20"><?php _e(" ex: /path/to/my/ventrilo_status" ); ?></p>
				<p><?php _e("Server Host: " ); ?><input type="text" name="vsd_host" value="<?php echo $host; ?>" size="20"><?php _e(" ex: 127.0.0.1" ); ?></p>
				<p><?php _e("Server Port: " ); ?><input type="text" name="vsd_port" value="<?php echo $port; ?>" size="20"><?php _e(" ex: 3784" ); ?></p>
				<p><?php _e("Server Password: " ); ?><input type="text" name="vsd_path" value="<?php echo $path; ?>" size="20"><?php _e(" ex: secretpassword" ); ?></p>

				<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Update Options', 'vsd_trdom' ) ?>" />
				</p>
			</form>
		</div>
