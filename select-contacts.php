<?php
require_once('../../../wp-load.php');

// If there is not session established, redirect to Yahoo "Agree" or "Login" page
if (invit0r_check_session() == false) {
	wp_redirect( invit0r_auth_url() );
	exit;
}

$sep = '[%sep%]';

// Check to see if the "Submit" button was pressed
if (!empty($_POST)) {

	// Check to see if any contacts were selected
	if (!empty($_POST['contact_this'])) {

		// Get the email addresses of the users already in the Wordpress database
		$results = $wpdb->get_results("SELECT `user_email` FROM `" . $wpdb->users  . "`");

		// Start building the insert query
		$query = "INSERT IGNORE INTO `" . $wpdb->prefix . "invit0r_invitees` (`hash`, `email_address`, `given_name`, `middle_name`, `family_name`, `nickname`, `time_added`, `time_resent`) VALUES ";

		$mysql_values = array();

		// Go through every selected contact
		foreach ($_POST['contact_this'] as $values_str) {

			$values_array = explode($sep, $values_str);

			if (count($values_array) == 5) {

				if (is_numeric($values_array[4])) {
					$email = $_POST['contact_email' . $values_array[4]];
				} else {
					$email = $values_array[4];
				}

				// If the email is a valid one and is not already present in the Wordpress database, add it to the insert query
				if ( is_email($email) && !invit0r_in_multiarray($email, $results) ) {
					$mysql_values[] = "('" . $wpdb->escape(invit0r_generate_random_hash(32)) . "',
												'" . $wpdb->escape($email) . "',
												'" . $wpdb->escape($values_array[0]) . "',
												'" . $wpdb->escape($values_array[1]) . "',
												'" . $wpdb->escape($values_array[2]) . "',
												'" . $wpdb->escape($values_array[3]) . "',
												" . time() . ",
												" . time() . ")";
				}
			}
		}

		// Insert the contacts in the database
		$wpdb->query($query . implode(', ', $mysql_values));

		wp_redirect( plugins_url('select-contacts.php?suc=1', __FILE__) );
		exit;

	} else {
		$error = 'You haven`t selected any contacts';
	}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Select contacts</title>
		<style type="text/css">
		body {
			font-family: arial;
			font-size:12px;
			background-color:#fff;
		}
		.row {
			height:18px;
			padding:8px 0;
			clear:both;
			cursor:pointer;
		}
		.row div {
			float:left;
			overflow:hidden;
			height:18px;
		}
		.number {
			width:30px;
			padding-left:5px;
		}
		.name {
			width:120px;
		}
		.nickname {
			width:100px;
		}
		.email {
			width:220px;
		}
		.table-header {
			font-weight:bold;
			background:transparent url('images/headerGd.png') repeat-x left top;
			height:35px;
			line-height:35px;
			padding:0;
		}
		.table-header div {
			overflow:visible;
		}
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
		.alt {
			background-color:#f6f8d9;
		}
		select {
			width:200px;
		}
		</style>
		<script type="text/javascript">
		window.onload = function() {

			var elements = document.getElementsByTagName('input'); // This is used to fix a little bug in the script
				for (var i in elements)
					elements[i].onclick = function(e){
						if (!e) var e = window.event;
						e.cancelBubble = true;
						if (e.stopPropagation) e.stopPropagation();
					}

			document.getElementById('select_all').onclick = function(e) { // The select all feature

				if (!e) var e = window.event;
				e.cancelBubble = true;
				if (e.stopPropagation) e.stopPropagation();

				var elements = document.getElementsByTagName('input');
				for (var i in elements)
					elements[i].checked = this.checked;
			}

			var divs = document.getElementsByTagName('div');

			for (var i in divs) {

				if (typeof(divs[i].className) != "undefined" && divs[i].className.search(/row/) != -1) {

					divs[i].onclick = function(){ // Used for checking and unchecking the input filed
						this.getElementsByTagName('input')[0].click();
					}

					divs[i].onmouseover = function(){ // Used for highlighting the row
						this.style.backgroundColor = '#f5e0ad';
					}

					divs[i].onmouseout = function(){ // Used for unhighlighting the row
						if (this.className.search(/alt/) != -1) {
							this.style.backgroundColor = '#f6f8d9';
						} else {
							this.style.backgroundColor = '#ffffff';
						}
					}
				}

			}
		}
		</script>
</head>
<body>
	<div id="wrap">
	<?php

	if (isset($_GET['suc'])) {
		echo '<p class="suc">Your friends have been invited. Thank you!</p>';
	} else {

		if (isset($error)) {
			echo '<p class="error">' , $error , '</p>';
		}

		?>

		<form action="<?php echo plugins_url('select-contacts.php', __FILE__);?>" method="POST">
			<?php
			// Get the contacts from Yahoo
			$contacts = invit0r_get_contacts(0, 1000)->contacts;

			echo '<div class="row table-header">
							<div class="number">#</div>
							<div class="name">Name</div>
							<div class="nickname">Nickname</div>
							<div class="email">
								<span style="float:right;">
									Select all / none
								</span>
								Email
							</div>
							<div>
								<input type="checkbox" id="select_all" />
							</div>
						</div>';

			if (!empty($contacts) && is_object($contacts)) {

				foreach ($contacts->contact as $index => $contact) {

					$emails = array();
					$yahooid = '';
					$givenName = '';
					$middleName = '';
					$familyName = '';
					$nickname = '';

					// Go through every contact
					foreach ($contact->fields as $field) {

						if ($field->type == 'yahooid') {
							$yahooid = $field->value;
							if (strpos($yahooid, '@') === false) {
								$yahooid .= '@yahoo.com';
							}

							if (!in_array($yahooid, $emails)) {
								$emails[] = $yahooid;
							}
						}

						if ($field->type == 'name') {
							$givenName = esc_attr($field->value->givenName);
							$middleName = esc_attr($field->value->middleName);
							$familyName = esc_attr($field->value->familyName);
						}

						if($field->type =='nickname') {
							$nickname = esc_attr($field->value);
						}

						if ($field->type == 'email' && !in_array($field->value, $emails)) {
							$emails[] = $field->value;
						}

						if ($field->type == 'otherid') {
							if (strpos($field->value, '@') !== false && !in_array($field->value, $emails)) {
								$emails[] = $field->value;
							}
						}
					}

					// If the contact has an email, display the row
					if (!empty($emails)) {

						$name = $givenName . ' ' . $middleName . ' ' . $familyName;

						echo '<div class="row' , $index % 2 == 1 ? ' alt' : '' , '">
										<div class="number">' , $index + 1 , '</div>
										<div class="name" title="' , $name , '">' , $name , '&nbsp;</div>
										<div class="nickname" title="' , $nickname , '">' , $nickname , '&nbsp;</div>
										<div class="email">';


						$values = "$givenName$sep$middleName$sep$familyName$sep$nickname$sep";

						if (count($emails) > 1) {
							// Use a select if the contact has multiple email addresses
							echo '<select name="contact_email' , $index , '">';

							foreach ($emails as $email) {
								echo '<option value="' , $email , '">' , $email , '</option>';
							}

							echo '</select>';
							$values .= $index;
						} else {
							echo '<div title="' , $emails[0] , '">' , $emails[0] , '</div>';
							$values .= $emails[0];
						}

						echo '</div>
										<div>
											<input type="checkbox" name="contact_this[]" id="id' , $index , '" value="' , $values , '" />
										</div>
									</div>';
					}
				}

			} else {
				echo '<p class="error">There was an error fetching your contacts, please try again later.</p>';
			}

			?>

			<br /><br />

			<button type="submit" name="submit">Submit</button>
		</form>

		<?php
	}
	?>
	</div>
</body>
</html>
