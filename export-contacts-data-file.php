<?php
ob_start();
require_once('../../../wp-load.php');
ob_end_clean();

if ( !current_user_can('manage_options') ) {
  wp_die( __('You are not allowed to access this part of the site.') );
}


header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=invit0r-contacts.csv');
header('Pragma: no-cache');

$query = "SELECT `id`, `email_address`, `given_name`, `middle_name`, `family_name`, `nickname`, DATE(FROM_UNIXTIME(`time_added`)) AS `date_added`, DATE(FROM_UNIXTIME(`time_resent`)) AS `date_resent`, `invites_sent`, `unsubscribe`, `is_imported` FROM `" . $wpdb->prefix . "invit0r_invitees` ORDER BY `time_added` DESC";

$results = $wpdb->get_results($query, ARRAY_A);


echo "id,email_address,given_name,middle_name,family_name,nickname,date_added,date_resent,invites_sent,unsubscribe,is_imported\r\n";


foreach ($results as $result) {
  foreach ($result as $index => $value) {
    $result[$index] = preg_replace('/[,\r\n\t]+/', ' ', $value);
  }

  echo $result['id'] , ',',
       $result['email_address'] , ',' ,
       $result['given_name'] , ',' ,
       $result['middle_name'] , ',' ,
       $result['family_name'] , ',' ,
       $result['nickname'] , ',' ,
       $result['date_added'] , ',' ,
       $result['date_resent'] , ',' ,
       $result['invites_sent'] , ',' ,
       $result['unsubscribe'] , ',' ,
       $result['is_imported'] , "\r\n";
}
