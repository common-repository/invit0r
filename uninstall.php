<?php

// *sighs* :(

if ( function_exists('register_uninstall_hook') ) {
  register_uninstall_hook(__FILE__, 'invit0r_uninstall_hook');
}

/**
* Remove the table and all the options from the database and delete the scheduled cron.
*/
function invit0r_uninstall_hook()
{
	global $wpdb;

	$invitees_table_name = $wpdb->prefix . 'invit0r_invitees';

	$wpdb->query("DROP TABLE IF EXISTS " . $invitees_table_name);

	delete_option('invit0r_db_version');
	delete_option('invit0r_oauth_consumer_key');
	delete_option('invit0r_oauth_consumer_secret');
	delete_option('invit0r_oauth_domain');
	delete_option('invit0r_oauth_app_id');
	delete_option('invit0r_use_wp_cron');
	delete_option('invit0r_wp_cron_interval');
	delete_option('invit0r_secret_key');
	delete_option('invit0r_emails_per_batch');
	delete_option('invit0r_reminders_per_batch');
	delete_option('invit0r_remind_after');
	delete_option('invit0r_reminders_limit');
	delete_option('invit0r_sender_name');
	delete_option('invit0r_sender_email');
	delete_option('invit0r_subject');
	delete_option('invit0r_body');
  wp_clear_scheduled_hook('invit0r_cron_hook');
}
