<?php

// Restrict the access to the plugin's option page only to administrators
invit0r_restrict_admin();


// handle the contacts import
if (isset($_POST['submit_import'])) {

    check_admin_referer( 'invit0r-nonce' );


    if ( ($handle = fopen($_FILES['imported_contacts']['tmp_name'], 'r')) !== false ) {

        // Get the email addresses of current users to avoid sending them useless emails
        $results = $wpdb->get_results("SELECT `user_email` FROM `" . $wpdb->prefix . "wp_users`");

        $emails = array();

        foreach ($results as $result) {
            $emails[] = $result->user_email;
        }


        $query = "INSERT IGNORE INTO `" . $wpdb->prefix . "invit0r_invitees` (`hash`, `email_address`, `given_name`, `middle_name`, `family_name`, `nickname`, `time_added`, `time_resent`, `is_imported`) VALUES ";

        $values = array();
        $limit = 0;
        $count = 0;

        while ( ($data = fgetcsv($handle, 1000, ',')) !== false ) {
            // Check if the imported email is valid and it is not already in the users table
            if ( is_email($data[0]) && !in_array($data[0], $emails)  ) {

                if (count($data) < 5) {
                    for ($i = count($data); $i < 5; $i++) {
                        $data[] = '';
                    }
                }

                $values_str = "('" . invit0r_generate_random_hash() .  "'" ;
                foreach ($data as $index => $value) {
                    $values_str .= ",'" . $wpdb->escape(trim($value)) ."'" ;
                }

                $values_str .= "," . time() . "," . time() . ",'1')";

                $values[] = $values_str;
                $limit++;
            }

            if ($limit == 1000) {
                // Insert the contacts in the database
                $wpdb->query($query . implode(',', $values));
                $count += mysql_affected_rows();
                $values = array();
                $limit = 0;
            }

        }

        if (!empty($values)) {
            // Insert the contacts in the database if there are any remaining in the queue
            $wpdb->query($query . implode(',', $values));
            $count += mysql_affected_rows();
        }

        // close the file
        fclose($handle);
        // delete the file
        unlink($_FILES['imported_contacts']['tmp_name']);

        $suc = ($count > 0 ? $count : 'No') . ' contacts have been imported';
    } else {
        $error = 'There was an error opening the file.';
    }
}

?>
<div class="wrap">

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

    <h2>Invit0r Import Contacts</h2>

    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data">
        <?php wp_nonce_field( 'invit0r-nonce' ); ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">File</th>
                <td>
                  <input type="file" size="60" name="imported_contacts" /> <br />
                  Format: email_address[[[[,given_name],middle_name],family_name],nickname]
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit_import" class="button-primary" value="<?php _e('Upload') ?>" />
        </p>
    </form>
</div>
