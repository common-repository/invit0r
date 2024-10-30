<?php

require_once('../../../wp-load.php');

if (!isset($_GET['suc'])) {

	// Check to see if email and hash are set
	if (isset($_GET['email'], $_GET['hash'])) {

		// Check to see if this is just a test
		if ($_GET['email'] == 'test_email' && $_GET['hash'] == 'test_hash') {
			$suc = 'Nothing happened, but everything\'s ok :)';
		} else if (is_email($_GET['email'])) { // If a valid email was provided, execute code

			// Check to see if the email is in the database
			$row = $wpdb->get_row("SELECT `id`, `unsubscribe` FROM `" . $wpdb->prefix . "invit0r_invitees` WHERE
										 `email_address` = '" . $wpdb->escape($_GET['email']) . "' AND `hash` = '" . $wpdb->escape($_GET['hash']) . "'");

			// If the email is in the database, unsubscribe the user
			if ($row) {

				if ($row->unsubscribe == 1) {
					$error = 'You already have been unsubscribed from our invitation list';
				} else {
					$wpdb->query("UPDATE `" . $wpdb->prefix . "invit0r_invitees` SET `unsubscribe` = '1' WHERE `id` = " . $row->id);
					wp_redirect(plugins_url('unsubscribe.php?suc=1', __FILE__));
				}

			} else {
				$error = 'The provided email address was not found in our database..';
			}

		} else {
			$error = 'Authentification failed! Invalid data provided...';
		}

	} else {
		$error = 'Authentification failed! Invalid data provided...';
	}

} else {
	$suc = 'You have been successfully unsubscribed from our invitation list';
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Unsubscribe</title>
		<style type="text/css">
		.error {
			padding:5px;
			color:#E55908;
			border:1px solid #E55908;
			font-weight:bold;
		}
		.suc {
			padding:5px;
			color:#64A01E;
			border:1px solid #64A01E;
			font-weight:bold;
		}
		</style>
</head>
<body>
	<?php

	// If there is a success message, display it
	if (isset($_GET['suc'])) {
		echo '<p class="suc">' , $suc , '</p>';
	}

	// If there is an error, display it
	if (isset($error)) {
		echo '<p class="error">' , $error , '</p>';
	}

	?>
	<p>Click here to proceed to our <a href="<?php echo get_bloginfo('url');?>">homapge</a>.</p>
</body>
</html>
