<?php
/*
Plugin Name: Invit0r
Plugin URI: http://marianbucur.com/wordpress-projects/invit0r.html
Description: This plugin enables your users to invite their friends to your website. Only Yahoo! Mail accounts currently work.
Author: Marian Bucur
Version: 0.22
Author URI: http://marianbucur.com/
*/

/*
* Copyright 2011  Invit0r  (email : thebigman@marianbucur.com)
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License, version 2, as
* published by the Free Software Foundation.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

global $invit0r_version;
global $invit0r_db_version;

$invit0r_version = '0.22';
$invit0r_db_version = '0.2';


/**
* Generates a random hash.
*
*	@param int $len What length should the hash string have? Default: 32.
*	@return string
*/
function invit0r_generate_random_hash($len = 32)
{

	if (!function_exists('mt_rand')) {
		function mt_rand($min, $max)
		{
			return rand($min, $max);
		}
	}

	$str = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$str_len = strlen($str);

	$secret_key = '';

	for ($i = 0; $i < $len; $i++) {
		$secret_key .= $str[ mt_rand( 0, $str_len - 1 ) ];
	}

	return $secret_key;
}


/**
* The function responsible with restricting the use of the plugin only to admins
*/
function invit0r_restrict_admin()
{
	if ( !current_user_can('manage_options') ) {
		wp_die( __('You are not allowed to access this part of the site.') );
	}
}


/**
* Verifies if the input is numeric and returns 0 otherwise.
* If the input is numeric, it rounds it down,
* returning 0 if the number is smaller than 0 and
* the actuall number if it greater than 0.
*
* @param int $input input
* @return int
*/
function invit0r_int_not_negative($input)
{
	if (is_numeric($input)) {
		$input = floor($input);

		if ($input < 0) {
			return 0;
		}

		return $input;
	}

	return 0;
}


/**
* Trims the input and shortens it to 32 characters.
*
* @param string $input input
* @return string
*/
function invit0r_max_len_32($input)
{
	return substr(trim($input), 0, 32);
}


/**
* Returns 0 or 1.
*
* @param int $input input
* @return int
*/
function invit0r_one_or_zero($input)
{
	return $input == 1 ? 1 : 0;
}


/**
* Checks if the input is a valid value and sets it, by default,
* to "every_five_minutes" if it is not.
* It also reschedules or removes the cron based on the user's options.
*
* @param string $input input
* @return string
*/
function invit0r_cron_internvals($input)
{
	// Valid values
	$valid = array('every_minute', 'every_five_minutes', 'every_ten_minutes', 'every_fifteen_minutes', 'every_half_an_hour');

	// Check to see if the input is valid and gets the value from the database if it is not
	if (!in_array($input, $valid)) {
		$input = get_option('invit0r_wp_cron_interval');
	}

	// If there is no value in the database, it just sets the value to the default "every_five_minutes"
	if (!in_array($input, $valid)) {
		$input = 'every_five_minutes';
	}

	return $input;
}


/**
* Checks to see if the provided element is in an array or not.
*
*	@param mixed $elem Needle
* @param mixed $array Haystack
*	@return bool
*/
function invit0r_in_multiarray($elem, $array)
{
	// if the $array is an array or is an object
	if( is_array( $array ) || is_object( $array ) ) {
			// if $elem is in $array object
			if( is_object( $array ) ) {
				$temp_array = get_object_vars( $array );
				if( in_array( $elem, $temp_array ) )
					return true;
			}

			// if $elem is in $array return true
			if( is_array( $array ) && in_array( $elem, $array ) )
				return true;


			// if $elem isn't in $array, then check foreach element
			foreach ( $array as $array_element ) {
				// if $array_element is an array or is an object call the invit0r_in_multiarray function to this element
				// if invit0r_in_multiarray returns TRUE, than return is in array, else check next element
				if( ( is_array( $array_element ) || is_object( $array_element ) ) && invit0r_in_multiarray( $elem, $array_element ) ) {
					return true;
					exit;
				}
			}
	}

	// if isn't in array return FALSE
	return false;
}


/**
* The function called when installing the plugin.
*/
function invit0r_install()
{
	global $wpdb, $invit0r_db_version, $invit0r_version;

	$invitees_table_name = $wpdb->prefix . 'invit0r_invitees';

	// Create or update the database table
	if($wpdb->get_var('SHOW TABLES LIKE ' . $invitees_table_name) != $invitees_table_name) {
		$sql = 'CREATE TABLE `' . $invitees_table_name . '` (
					`id` bigint(20) unsigned NOT NULL auto_increment,
					`hash` varchar(32) NOT NULL,
					`email_address` varchar(255) NOT NULL,
					`given_name` varchar(255) NOT NULL,
					`middle_name` varchar(255) NOT NULL,
					`family_name` varchar(255) NOT NULL,
					`nickname` varchar(255) NOT NULL,
					`time_added` int(11) unsigned NOT NULL,
					`time_resent` int(11) unsigned NOT NULL,
					`invites_sent` int(11) unsigned NOT NULL default \'0\',
					`unsubscribe` enum(\'0\',\'1\') NOT NULL default \'0\',
					`is_imported` ENUM(\'0\',\'1\') NOT NULL DEFAULT \'0\',
					PRIMARY KEY  (`id`),
					UNIQUE KEY `email_address` (`email_address`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	// Add the critical options
	add_option('invit0r_version', $invit0r_version);
	add_option('invit0r_db_version', $invit0r_db_version);
	add_option('invit0r_emails_per_batch', 10);
	add_option('invit0r_reminders_per_batch', 0);
	add_option('invit0r_remind_after', 0);
	add_option('invit0r_reminders_limit', 0);
	add_option('invit0r_use_wp_cron', 1);
	add_option('invit0r_wp_cron_interval', 'every_minute');
	add_option('invit0r_secret_key', invit0r_generate_random_hash());


	// Schedule the Wordpress cron job if it isn't already scheduled and if the user chose to
	if ( !wp_next_scheduled('invit0r_cron_hook') && get_option('invit0r_use_wp_cron') == 1 ) {
		wp_schedule_event( time(), 'every_five_minutes', 'invit0r_cron_hook' );
	}
}

// Register the activation function
register_activation_hook( __FILE__, 'invit0r_install' );


/**
* The function called when deactivating the plugin.
*/
function invit0r_deactivate()
{
	wp_clear_scheduled_hook('invit0r_cron_hook');
}

// Register the deactivation function
register_deactivation_hook( __FILE__, 'invit0r_deactivate' );


if (is_admin()) {
	/**
	* Add some more links to the plugin row meta.
	*
	*	@param array $links Already defined links
	*	@param string $file File path
	* @return array
	*/
	function invit0r_more_plugin_links($links, $file)
	{
		$base = plugin_basename(__FILE__);
		if ($file == $base) {
			$links[] = '<a href="admin.php?page=invit0r/main.php">' . __('Configuration') . '</a>';
		}
		return $links;
	}

	//Additional links on the plugin page
	add_filter('plugin_row_meta', 'invit0r_more_plugin_links', 10, 2);


	/**
	* The function responsible with adding the menus.
	*/
	function invit0r_admin_menu()
	{
		if (function_exists('add_menu_page')) {
			add_menu_page('Invit0r', 'Invit0r', 'manage_options', 'invit0r/main.php');
		}

		if (function_exists('add_submenu_page')) {
			add_submenu_page('invit0r/main.php', 'Invit0r Configuration', 'Configuration', 'manage_options', 'invit0r/main.php');
			add_submenu_page('invit0r/main.php', 'Invit0r Export contacts', 'Export contacts', 'manage_options', 'invit0r/export-contacts.php');
			add_submenu_page('invit0r/main.php', 'Invit0r Import contacts', 'Import contacts', 'manage_options', 'invit0r/import-contacts.php');
			add_submenu_page('invit0r/main.php', 'Invit0r Stats', 'Stats', 'manage_options', 'invit0r/stats.php');
		}

	}

	// Add admin menu
	add_action( 'admin_menu', 'invit0r_admin_menu' );


	/**
	* Custom intervals used for Wordpress' cron system.
	*
	*	@param array $schedules Already defined intervals
	*	@return array
	*/
	function invit0r_filter_cron_schedules($schedules)
	{

		$schedules['every_minute'] = array(
			'interval' => 60,
			'display' => __( 'Once per minute' )
		);

		$schedules['every_five_minutes'] = array(
			'interval' => 300,
			'display' => __( 'Once five minutes' )
		);

		$schedules['every_ten_minutes'] = array(
			'interval' => 600,
			'display' => __( 'Once ten minutes' )
		);

		$schedules['every_fifteen_minutes'] = array(
			'interval' => 900,
			'display' => __( 'Once fifteen minutes' )
		);

		$schedules['every_half_an_hour'] = array(
			'interval' => 1800,
			'display' => __( 'Once half an hour' )
		);

		return $schedules;
	}

	// Add custom intervals for Wordpress' cron system
	add_filter( 'cron_schedules', 'invit0r_filter_cron_schedules' );
}


/**
* The function used by both Wordpress' cron system and Invit0r's cron.php file
* for sending invites and reminders.
*/
function invit0r_cron()
{
	global $wpdb;

	// Change the content type of the email from plain text to html
	add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

	$sender_email = get_option('invit0r_sender_email');
	$subject = stripslashes(get_option('invit0r_subject'));
	$body =  stripslashes(get_option('invit0r_body'));

	// Verify if the sender email, subject and body are not blank
	if (trim($sender_email) != '' && trim($subject) != '' && trim($body) != '') {

		$emails_per_batch = get_option('invit0r_emails_per_batch');
		$reminders_per_batch = get_option('invit0r_reminders_per_batch');
		$reminders_limit = get_option('invit0r_reminders_limit');
		$remind_after = get_option('invit0r_remind_after') * 24 * 3600;
		// $remind_after = 1;

		$unsubscribe_url = plugins_url('unsubscribe.php', __FILE__);

		// Get the email addresses where the invites will be sent
		if ($emails_per_batch > 0) {
			$results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "invit0r_invitees` WHERE `invites_sent` = 0 LIMIT " . $emails_per_batch);
		} else {
			$results = null;
		}

		// If reminders are active, get the email addresses where the reminders will be sent
		if ($reminders_per_batch && $remind_after > 0) {
			$results_reminders = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "invit0r_invitees` WHERE `invites_sent` > 0 " . ( $reminders_limit != 0 ? "AND `invites_sent` <= " . $reminders_limit : '' ) . " AND `unsubscribe` = '0' AND `time_resent` + " . $remind_after . " < " . time() . "  LIMIT " . $reminders_per_batch);
		} else {
			$results_reminders = null;
		}

		// Check to see if there are any emails to be sent and execute the rest of the code if there are
		if ($results || $results_reminders) {

			$sender_name = stripslashes(get_option('invit0r_sender_name'));

			// Set the wp_mail headers, determined by the sender name
			if ($sender_name != '') {
				$headers = 'From: ' . $sender_name . ' <' . $sender_email . '>' . PHP_EOL;
			} else if ( ($blog_name = get_bloginfo('name')) != '' ) {
				$headers = 'From: ' . $blog_name . ' <' . $sender_email . '>' . PHP_EOL;
			} else {
				$headers = 'From: ' . $sender_email . ' <' . $sender_email . '>' . PHP_EOL;
			}

			// Send 1st time invite
			if (is_array($results)) {

				$ids = array();

				foreach ($results as $result) {
					$ids[] = '`id` = ' . $result->id;
					$body_aux = str_replace('%unsubscribe_url%', $unsubscribe_url . '?email=' . $result->email_address . '&hash=' . $result->hash, $body);
					wp_mail($result->email_address, $subject, $body_aux, $headers);
				}
			}


			// Send reminders
			if (is_array($results_reminders)) {

				if (!isset($ids)) {
					$ids = array();
				}

				foreach ($results_reminders as $result) {
					$ids[] = '`id` = ' . $result->id;
					$body_aux = str_replace('%unsubscribe_url%', $unsubscribe_url . '?email=' . $result->email_address . '&hash=' . $result->hash, $body);
					wp_mail($result->email_address, $subject, $body_aux, $headers);
				}

			}

			// If any emails were sent, update the number of invites sent to that address and the time when it was (re)sent
			if (!empty($ids)) {
				$wpdb->query("UPDATE `" . $wpdb->prefix . "invit0r_invitees` SET `invites_sent` = `invites_sent` + 1, `time_resent` = " . time() . " WHERE " . implode(' OR ', $ids));
			}

		}

	}

}

// Schedule the Wordpress cron job
add_action( 'invit0r_cron_hook', 'invit0r_cron' );


/**
* Admin css
*/
function invit0r_admin_css()
{
	echo '<style type="text/css">.show{display:table-row}.hidden{display:none}#invit0r_chart_wrap{text-align:center}#invit0r_chart_switch{width:200px;margin:0 auto}#invit0r_chart_switch a{float:left;padding:7px;margin-right:5px;border:1px solid #ddd;border-bottom:none;border-top-left-radius:3px;border-top-right-radius:3px}.invit0r_chart{clear:left}.invit0r_chart_switch_active{font-weight:bold}</style>';
}

// Print the admin css
add_action('admin_print_styles-invit0r/main.php', 'invit0r_admin_css');
add_action('admin_print_styles-invit0r/stats.php', 'invit0r_admin_css');


/**
* Admin js
*/
function invit0r_admin_js()
{
	wp_enqueue_script('invit0r_admin', plugins_url('js/invit0r-admin.js', __FILE__), array('jquery', 'swfobject'), null);
}

// Print the admin js
add_action('admin_print_scripts-invit0r/main.php', 'invit0r_admin_js');
add_action('admin_print_scripts-invit0r/stats.php', 'invit0r_admin_js');


/**
* User js
*/
function invit0r_enqueue_scripts()
{
	wp_enqueue_script('invit0r', plugins_url('js/invit0r.js', __FILE__), array('jquery'), null);

	echo "<script type='text/javascript'>\nvar select_cotacts_url = '" , plugins_url('select-contacts.php', __FILE__) , "'\nvar admin_ajax_url = '" . get_bloginfo('url') . "/wp-admin/admin-ajax.php'\n</script>\n";
}

add_action('wp_enqueue_scripts', 'invit0r_enqueue_scripts');


// Require the Yahoo library
require_once('lib/Invit0r_Yahoo.inc');


// Load the OAuth authentication data
$invit0r_oauth_consumer_key = get_option('invit0r_oauth_consumer_key');
$invit0r_oauth_consumer_secret = get_option('invit0r_oauth_consumer_secret');
$invit0r_oauth_app_id = get_option('invit0r_oauth_app_id');


// Get the session status
$invit0r_hasSession = Invit0r_YahooSession::hasSession($invit0r_oauth_consumer_key, $invit0r_oauth_consumer_secret, $invit0r_oauth_app_id);


// Check the session status
if ($invit0r_hasSession) {
	// pass the credentials to initiate a session
	$invit0r_session = Invit0r_YahooSession::requireSession($invit0r_oauth_consumer_key, $invit0r_oauth_consumer_secret, $invit0r_oauth_app_id);
	$invit0r_user = $invit0r_session->getSessionedUser();
}


// Set the yahoo logo
$invit0r_yahoo_logo = '<img src="' . plugins_url('images/yahoo_logo.png', __FILE__) . '" width="168" height="44" alt="yahoo logo" />';


// if a session exists and the logout flag is detected clear the session tokens and redirect to the main page
if(isset($_GET['invit0r_logout'])) {
	invit0r_logout();
	wp_redirect(get_bloginfo('url'));
}


/**
* Clear the session tokens.
*/
function invit0r_logout()
{
	global $invit0r_hasSession;

	Invit0r_YahooSession::clearSession();
	$invit0r_hasSession = false;
}


/**
* The function used to display the invite link on the user area.
*
*	@param string|null $display_element You can specify a html tag, text or null (nothing will be displayed inside the link)
*/
function invit0r_display($display_element = '')
{
	global $invit0r_hasSession, $invit0r_yahoo_logo;

	if (!is_null($display_element)) {
		// If display_element is blank, display the default image
		if ($display_element == '') {
			$display_element = $invit0r_yahoo_logo;
		}
	} else {
		// If display_element is null, don't display anything inside the link
		$display_element = '';
	}

	// Store display_element for displaying the invite link using ajax
	$_SESSION['invit0r_display'] = $display_element;

	echo '<div id="invit0r">';

	if ($invit0r_hasSession == false) {
		// If there is no session established yet, display the auth link
		$auth_url = invit0r_auth_url();
		if (trim($auth_url) != '') {
			echo '<a id="invit0r_link" href="' . $auth_url . '">' , $display_element , '</a>';
		}
	} else {
		// If there is a session established, display the invite contacts and logout links
		echo 'Invite more contacts <br /><br /> <a id="invit0r_link" href="' , plugins_url('select-contacts.php', __FILE__) , '">' , $display_element , '</a> <br /><br /> or <a href="' . Invit0r_YahooUtil::current_url() . '?invit0r_logout" id="invit0r_logout">logout</a>';
	}

	echo '</div>';
}


/**
* Get user's contacts from Yahoo between the limits supplied as arguments.
*
*	@param int $from from
*	@param int $to to
*	@return List of contacts for the current user
* @see Invit0r_YahooUser::getContacts()
*/
function invit0r_get_contacts($from, $to)
{
	global $invit0r_user;

	return $invit0r_user -> getContacts($from, $to);
}


/**
* Returns the auth url based on the session status.
*
*	@return str
* @see Invit0r_YahooSession::createAuthorizationUrl()
*/
function invit0r_auth_url()
{
	global $invit0r_oauth_consumer_key, $invit0r_oauth_consumer_secret;

	return Invit0r_YahooSession::createAuthorizationUrl($invit0r_oauth_consumer_key, $invit0r_oauth_consumer_secret, plugins_url('select-contacts.php', __FILE__));
}


/**
* Check to see if the user established a session or not
*
*	@return int
* @access public
*/
function invit0r_check_session()
{
	global $invit0r_hasSession;

	if($invit0r_hasSession == false) {
		return 0;
	}
	return 1;
}


/**
* Display the invite link, using ajax
*/
function invit0r_display_ajax()
{
	invit0r_display($_SESSION['invit0r_display']);
	die();
}

add_action( 'wp_ajax_nopriv_invit0r_display', 'invit0r_display_ajax' );
add_action( 'wp_ajax_invit0r_display', 'invit0r_display_ajax' );


/**
* Logout using ajax and refresh the invite link.
*/
function invit0r_logout_ajax()
{
	global $invit0r;

	invit0r_logout();
	invit0r_display_ajax();
}

add_action( 'wp_ajax_nopriv_invit0r_logout', 'invit0r_logout_ajax' );
add_action( 'wp_ajax_invit0r_logout', 'invit0r_logout_ajax' );
