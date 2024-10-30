<?php
// Restrict the access to the plugin's option page only to administrators
invit0r_restrict_admin();
?>

<div class="wrap">
  <h2>Invit0r Stats</h2>
  <p>This includes both invited and imported contacts.</p>
  <p>
    Invited: <strong><?php echo $wpdb->get_var("SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "invit0r_invitees` WHERE `is_imported` = '0'");?></strong> |
    Imported: <strong><?php echo $wpdb->get_var("SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "invit0r_invitees` WHERE `is_imported` = '1'");?></strong> |
    Unsubscribed: <strong><?php echo $wpdb->get_var("SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "invit0r_invitees` WHERE `unsubscribe` = '1'");?></strong> |
    Total: <strong><?php echo $wpdb->get_var("SELECT COUNT(`id`) FROM `" . $wpdb->prefix . "invit0r_invitees`");?></strong>
  </p>

  <div id="invit0r_chart_wrap">
    <div id="invit0r_chart_switch">
      <a id="invit0r_chart_switch_1" class="invit0r_chart_switch_active" href="#">Days</a>
      <a id="invit0r_chart_switch_7" href="#">Weeks</a>
      <a id="invit0r_chart_switch_31" href="#">Months</a>
    </div>
    <div class="invit0r_chart">
      <div id="invit0r_chart_1"></div>
    </div>
    <div class="invit0r_chart" style="display:none;">
      <div id="invit0r_chart_7"></div>
    </div>
    <div class="invit0r_chart" style="display:none;">
      <div id="invit0r_chart_31"></div>
    </div>
  </div>
</div>
