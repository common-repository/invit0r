<?php
// Restrict the access to the plugin's option page only to administrators
invit0r_restrict_admin();
?>

<div class="wrap">
  <h2>Invit0r Export Contacts</h2>
  <br /> <a class="button" href="<?php echo plugins_url('export-contacts-data-file.php', __FILE__); ?>">Export</a>
</div>
