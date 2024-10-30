<?php

if ( isset($_GET['secret_key']) && strlen($_GET['secret_key']) == 32 ) {
	$secret_key = $_GET['secret_key'];
} else if (isset($argv[1])) {
	list(,$secret_key) = explode('=', $argv[1]);
}

if (isset($secret_key) && !empty($secret_key)) {

	require_once('../../../wp-load.php');

	if ($secret_key != get_option('invit0r_secret_key') || get_option('invit0r_use_wp_cron') == 1) {
		die();
	}

	invit0r_cron();
}
