<?php

// Restrict the access to the plugin's option page only to administrators
invit0r_restrict_admin();


if (!empty($_POST)) {

  check_admin_referer( 'invit0r-nonce' );

  $invit0r_oauth_consumer_key = strip_tags(trim($_POST['invit0r_oauth_consumer_key']));
  $invit0r_oauth_consumer_secret = strip_tags(trim($_POST['invit0r_oauth_consumer_secret']));
  $invit0r_oauth_domain = strip_tags(trim($_POST['invit0r_oauth_domain']));
  $invit0r_oauth_app_id = strip_tags(trim($_POST['invit0r_oauth_app_id']));
  $invit0r_use_wp_cron = invit0r_one_or_zero(strip_tags(trim($_POST['invit0r_use_wp_cron'])));
  $invit0r_wp_cron_interval = invit0r_cron_internvals(strip_tags(trim($_POST['invit0r_wp_cron_interval'])));
  $invit0r_secret_key = invit0r_max_len_32(strip_tags(trim($_POST['invit0r_secret_key'])));
  $invit0r_emails_per_batch = invit0r_int_not_negative(strip_tags(trim($_POST['invit0r_emails_per_batch'])));
  $invit0r_reminders_per_batch = invit0r_int_not_negative(strip_tags(trim($_POST['invit0r_reminders_per_batch'])));
  $invit0r_remind_after = invit0r_int_not_negative(strip_tags(trim($_POST['invit0r_remind_after'])));
  $invit0r_reminders_limit = invit0r_int_not_negative(strip_tags(trim($_POST['invit0r_reminders_limit'])));
  $invit0r_sender_name = strip_tags(trim($_POST['invit0r_sender_name']));
  $invit0r_sender_email = strip_tags(trim($_POST['invit0r_sender_email']));
  $invit0r_subject = strip_tags(trim($_POST['invit0r_subject']));
  $invit0r_body = trim($_POST['invit0r_body']);

} else {

  $invit0r_oauth_consumer_key = get_option('invit0r_oauth_consumer_key');
  $invit0r_oauth_consumer_secret = get_option('invit0r_oauth_consumer_secret');
  $invit0r_oauth_domain = get_option('invit0r_oauth_domain');
  $invit0r_oauth_app_id = get_option('invit0r_oauth_app_id');
  $invit0r_use_wp_cron = get_option('invit0r_use_wp_cron');
  $invit0r_wp_cron_interval = get_option('invit0r_wp_cron_interval');
  $invit0r_secret_key = get_option('invit0r_secret_key');
  $invit0r_emails_per_batch = get_option('invit0r_emails_per_batch');
  $invit0r_reminders_per_batch = get_option('invit0r_reminders_per_batch');
  $invit0r_remind_after = get_option('invit0r_remind_after');
  $invit0r_reminders_limit = get_option('invit0r_reminders_limit');
  $invit0r_sender_name = get_option('invit0r_sender_name');
  $invit0r_sender_email = get_option('invit0r_sender_email');
  $invit0r_subject = get_option('invit0r_subject');
  $invit0r_body = get_option('invit0r_body');

}


if ($invit0r_oauth_consumer_key == '' || $invit0r_oauth_consumer_secret == '' || $invit0r_oauth_domain == '' || $invit0r_oauth_app_id == '') {
  $error = 'Please setup the OAuth details or the script will not work.';
} else if (strlen($invit0r_secret_key) != 32) {
  $error = 'You need to provide a 32 long string for the \'Secret key\' in order for the cron file to work. It is for your own safety.';
} else if (!is_email($invit0r_sender_email)) {
  $error = 'Please enter a valid \'Sender email\'';
} else if ($invit0r_sender_email == '' || $invit0r_subject == '' || $invit0r_body == '') {
  $error = 'The \'Sender email\', \'Subject\' or/and \'Body\' fields are empty. The plugin will not send invitations, reminders and test emails until you fix this problem.';
}


// Send a test email if the "Test" button has been pressed
if (isset($_POST['test'])) {

  // Change the content type of the email from plain text to html
  add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));


  // Verify if the sender email, subject and body are not blank
  if ($invit0r_sender_email != '' && $invit0r_subject != '' && $invit0r_body != '') {

    $unsubscribe_url = plugins_url('unsubscribe.php', __FILE__);

    // Set the wp_mail headers, determined by the sender name
    if ($invit0r_sender_name != '') {
      $headers = 'From: ' . $invit0r_sender_name . ' <' . $invit0r_sender_email . '>' . PHP_EOL;
    } else if ( ($blog_name = get_bloginfo('name')) != '' ) {
      $headers = 'From: ' . $blog_name . ' <' . $invit0r_sender_email . '>' . PHP_EOL;
    } else {
      $headers = 'From: ' . $invit0r_sender_email . ' <' . $invit0r_sender_email . '>' . PHP_EOL;
    }

    // Replace the %unsubscribe_url% tag with a dummy link (dummy because the admin doesn't need to unsubscribe)
    $body_aux = str_replace('%unsubscribe_url%', $unsubscribe_url . '?email=test_email&hash=test_hash', $invit0r_body);

    // Send the email
    wp_mail($invit0r_sender_email, $invit0r_subject, $body_aux, $headers);

    // Success message
    $suc = 'A test email has been sent to the provided \'Sender email\'';
  }

}


// Save the options
if (!isset($error) && isset($_POST['save'])) {

  update_option('invit0r_oauth_consumer_key', $invit0r_oauth_consumer_key);
  update_option('invit0r_oauth_consumer_secret', $invit0r_oauth_consumer_secret);
  update_option('invit0r_oauth_domain', $invit0r_oauth_domain);
  update_option('invit0r_oauth_app_id', $invit0r_oauth_app_id);
  update_option('invit0r_use_wp_cron', $invit0r_use_wp_cron);
  update_option('invit0r_wp_cron_interval', $invit0r_wp_cron_interval);
  update_option('invit0r_secret_key', $invit0r_secret_key);
  update_option('invit0r_emails_per_batch', $invit0r_emails_per_batch);
  update_option('invit0r_reminders_per_batch', $invit0r_reminders_per_batch);
  update_option('invit0r_remind_after', $invit0r_remind_after);
  update_option('invit0r_reminders_limit', $invit0r_reminders_limit);
  update_option('invit0r_sender_name', $invit0r_sender_name);
  update_option('invit0r_sender_email', $invit0r_sender_email);
  update_option('invit0r_subject', $invit0r_subject);
  update_option('invit0r_body', $invit0r_body);

  // Check to see if the user opted for using Wordpress' cron system
  if ($invit0r_use_wp_cron == 1) {
    // Clear the scheduled cron and schedule it again
    wp_clear_scheduled_hook('invit0r_cron_hook');
    wp_schedule_event( time(), $invit0r_use_wp_cron, 'invit0r_cron_hook' );
  } else {
    // Clear the scheduled cron if the user opted to use a proper cron system
    wp_clear_scheduled_hook('invit0r_cron_hook');
  }

  // Success message
  $suc = 'Configuration successfully saved';

}

?>

<div class="wrap">

  <div class="icon32" id="icon-options-general"><br /></div>
  <h2>Invit0r Configuration</h2>

  <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
      <?php wp_nonce_field( 'invit0r-nonce' ); ?>

      <?php

      // If there is an error message set, display it
      if (isset($error)) {
        echo '<div class="error"><p>' , $error , '</p></div>';
      }

      // If it is a success message set, display it
      if (isset($suc)) {
        echo '<div class="updated settings-error"><p>' , $suc , '</p></div>';
      }
      ?>

      <h3>OAuth Settings</h3>

      <p><a href="https://developer.apps.yahoo.com/dashboard/createKey.html">Create Key</a></p>

      <table class="form-table">
        <tr valign="top">
          <th scope="row">OAuth Consumer Key</th>
          <td><input type="text" size="100" name="invit0r_oauth_consumer_key" value="<?php echo stripslashes($invit0r_oauth_consumer_key); ?>" /></td>
        </tr>

        <tr valign="top">
          <th scope="row">OAuth Consumer Secret</th>
          <td><input type="text" size="50" name="invit0r_oauth_consumer_secret" value="<?php echo stripslashes($invit0r_oauth_consumer_secret); ?>" /></td>
        </tr>

        <tr valign="top">
          <th scope="row">OAuth Domain</th>
          <td><input type="text" size="50" name="invit0r_oauth_domain" value="<?php echo stripslashes($invit0r_oauth_domain); ?>" /></td>
        </tr>

        <tr valign="top">
          <th scope="row">OAuth App ID</th>
          <td><input type="text" size="10" name="invit0r_oauth_app_id" value="<?php echo stripslashes($invit0r_oauth_app_id); ?>" /></td>
        </tr>
    </table>

    <h3>Cron Settings</h3>

    <table class="form-table">
      <tr valign="top">
        <th scope="row">Use Wordpress cron</th>
        <td>
          <label><input type="radio" name="invit0r_use_wp_cron" value="1"<?php echo $invit0r_use_wp_cron == 1 ? ' checked="checked"' : '';?> /> Yes</label> &nbsp;
          <label><input type="radio" name="invit0r_use_wp_cron" value="0"<?php echo $invit0r_use_wp_cron == 0 ? ' checked="checked"' : '';?> /> No</label>
        </td>
      </tr>

      <tr class="yes <?php echo $invit0r_use_wp_cron == 1 ? 'show' : 'hidden';?>">
        <th scope="row">Wordpress cron inverval</th>
        <td>
          <select name="invit0r_wp_cron_interval">
            <option value="every_minute"<?php echo $invit0r_wp_cron_interval == 'every_minute' ? ' selected="selected"' : '';?>>Once per minute</option>
            <option value="every_five_minutes"<?php echo $invit0r_wp_cron_interval == 'every_five_minutes' ? ' selected="selected"' : '';?>>Once five minutes</option>
            <option value="every_ten_minutes"<?php echo $invit0r_wp_cron_interval == 'every_ten_minutes' ? ' selected="selected"' : '';?>>Once ten minutes</option>
            <option value="every_fifteen_minutes"<?php echo $invit0r_wp_cron_interval == 'every_fifteen_minutes' ? ' selected="selected"' : '';?>>Once fifteen minutes</option>
            <option value="every_half_an_hour"<?php echo $invit0r_wp_cron_interval == 'every_half_an_hour' ? ' selected="selected"' : '';?>>Once half an hour</option>
          </select>
        </td>
      </tr>

      <tr class="no <?php echo $invit0r_use_wp_cron == 0 ? 'show' : 'hidden';?>">
        <th scope="row">Secret key</th>
        <td>
          <input type="text" size="42" name="invit0r_secret_key" maxlength="32" value="<?php echo $invit0r_secret_key; ?>" />
        </td>
      </tr>

      <tr class="no <?php echo $invit0r_use_wp_cron == 0 ? 'show' : 'hidden';?>">
        <th scope="row">Cron command</th>
        <td>
          <input type="text" size="100" readonly="readonly" value="php <?php echo __DIR__;?>/cron.php secret_key=<?php echo $invit0r_secret_key;?> >/dev/null 2>&1" /> <br />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">1st time emails per batch</th>
        <td>
          <input type="text" size="5" name="invit0r_emails_per_batch" value="<?php echo $invit0r_emails_per_batch; ?>" /> <br />
          The emails which are sent for the 1st time; 0 means it will send no 1st time emails.
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Reminders per batch</th>
        <td>
          <input type="text" size="5" name="invit0r_reminders_per_batch" value="<?php echo $invit0r_reminders_per_batch; ?>" /> <br />
          0 means it will send no reminders.
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Send reminders once every x days</th>
        <td>
          <input type="text" size="5" name="invit0r_remind_after" value="<?php echo $invit0r_remind_after; ?>" /> <br />
          0 means it will send no reminders.
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Reminders limit</th>
        <td>
          <input type="text" size="5" name="invit0r_reminders_limit" value="<?php echo $invit0r_reminders_limit; ?>" /> <br />
          Don't send any reminder beyond this limit; 0 means no limit.
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Sender name</th>
        <td>
          <input type="text" size="100" name="invit0r_sender_name" value="<?php echo stripslashes($invit0r_sender_name); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Sender email</th>
        <td>
          <input type="text" size="100" name="invit0r_sender_email" value="<?php echo $invit0r_sender_email; ?>" /> <br />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Subject</th>
        <td>
          <input type="text" size="100" name="invit0r_subject" value="<?php echo stripslashes($invit0r_subject); ?>" />
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Body</th>
        <td>
          <textarea cols="83" rows="20" name="invit0r_body"><?php echo stripslashes($invit0r_body); ?></textarea> <br />
          You can use XHTML code. Also, you can use the %unsubscribe_url% tag in the href of your unsubscribe link.
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes') ?>" />
      <input type="submit" value="<?php _e('Test') ?>" name="test" title="Send a test email to the provided 'Sender email'" />
    </p>

  </form>
</div>
