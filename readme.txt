=== Plugin Name ===
Contributors: G0dLik3
Tags: invite, Yahoo, contacts, email, non-BuddyPress
Requires at least: 2.8.6
Tested up to: 3.2.1
Stable tag: 0.22

This plugin enables your users to invite their friends to your website. Only Yahoo! Mail accounts currently work.

== Description ==

This plugin enables your users to invite their friends to your website. You have comprehensive control over the appearance of the invitation, using your own design. Admins can opt to send out reminders for the invitations. It works only with Yahoo! Mail accounts. Users can select which friends to invite. The recipients can unsubscribe from further invitations. Also, registered users on your website will not receive the invitation.

== Installation ==

1. Upload the invit0r folder to the /wp-content/plugins/ directory.
2. Activate Invit0r through the 'Plugins' menu in WordPress.
3. Get your Api key from https://developer.apps.yahoo.com/dashboard/createKey.html and fill in the fields under 'OAuth Settings'.
4. Fill in the 'Sender name' (optional), 'Sender email'*, 'Subject'* and 'Body'* fields, and modify the rest of them if you want to.
5. Add `<?php invit0r_display();?>` where you want the invite link to show up.
6. Enjoy and may you get a lot of new visitors!

* 'Sender email', 'Subject' and 'Body' are required in order for the plugin to send invites, reminders and test emails.
* If you have a version >= 0.20, you can safely delete `invit0r/select_contacts.php` and `invit0r/js/invit0r_admin.js` as I have replaced them with `invit0r/select-contacts.php` and `invit0r/js/invit0r-admin.js`.

== How it works ==

After you have successfully installed and configured the plugin, and placed `<?php invit0r_display();?>` somewhere on your site, when a visitor invites his Yahoo! contacts, they (the contacts) will be placed in the database and an email with the details written in the 'Sender name', 'Sender email', 'Subject' and 'Body' fields will be sent to each one of them (limiting the number of emails sent at a time to the number written in the '1st time emails per batch' field). Note that the cron job will also try to send reminders (if enabled) at the same time with 1st time emails. You can controll the limit from the configuration page.  That's all folks.

== Frequently Asked Questions ==

= invit0r_display() is not displaying anything! What's wrong? =

You probably didn't fill in the fields under the 'OAuth Settings' section with the corret values.
Or you called the display function passing null as parameter.

= I hate that 'Yahoo!' icon. Is there a way to change that? =

Sure! You can specify an image that you want (needs to be the whole <img> tag) or any other html tag that can go in an anchor tag.
You can also specify plain text, or if you don't want to display anything in the invite link just call the display function with null.

Examples:

`<?php invit0r_display('<img src="my-image.jpg" alt="" width="100" height="50" />');?>
<?php invit0r_display('<span class="my-custom-span">Some text</span>');?>
<?php invit0r_display(null);?>`

= Do I need BuddyPress? =

NO, you do not need BuddyPress.

= My question is not in this FAQ. Help! =

Just send me the question and I'll see what I can do.

== Screenshots ==

1. Invit0r submenu
2. Invit0r configuration page
3. Invit0r configuration continued
4. Invit0r export page
5. Invit0r import page
6. Invit0r stats page
7. The invite link + logout
8. The contacts select page

== Changelog ==

= 0.22 =

* Fixed a few bugs

= 0.21 =

* Fixed a few bugs

= 0.2 =

* Moved Invit0r in its own submenu
* Added 'Reminders per batch'
* Added 'Reminders limit'
* Added a simple csv export page
* Added a simple stats page
* Added a new field in the database table, 'is_imported', which indicates whether the contacts were invited or imported (imported contacts prior to this update will be counted as invited)
* Added and replaced a few screenshots
* Changed 'Email per batch' to '1st time emails per batch'
* Changed the importer so it doesn't import emails which already are in the 'wp_users' table
* Renamed js/invit0r_admin.js to js/invit0r-admin.js
* Renamed select_contacts.php to select-contacts.php

= 0.12 =

* Added contacts import

= 0.11 =

* Fixed a bug causing the admin area to deny access to any non-admin users
* Fixed a bug causing the invit0r_link to vanish after closing the popup and quickly refreshing the page
