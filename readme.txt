=== Issues Exporter for GitLab ===
Contributors: shitalparmar443
Donate link: https://paypal.me/shitalparmar443
Tags: gitlab, csv export, issues, ajax, fluent boards
Short Description : Export GitLab issues to a CSV file with AJAX progress tracking and Fluent Boards compatibility.
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Export GitLab issues to a CSV file with AJAX progress tracking and Fluent Boards compatibility.

== Description ==

Export GitLab issues to a CSV file with AJAX progress tracking and Fluent Boards compatibility.

= Installation =

From the admin panel, Go to your WordPress Admin -> Plugins -> Add New. Search for Issues Exporter for GitLab. Install and Activate.

From directories, Upload `Issues Exporter for GitLab` to the `/wp-content/plugins/` directory and activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How many issues can it export? =
Up to 5000 issues in paginated batches of 50.

= Is my access token saved securely? =
Yes. It is stored using WordPress options API and never exposed in frontend HTML.

= Can I delete old CSV files? =
Yes, all generated files are listed in the admin page, with delete buttons.

== Screenshots ==

1. Issues Exporter for GitLab Settings screen
2. Export progress and status display
3. List of generated files

== Changelog ==

= 1.0 =
* Initial release. Issues Exporter for GitLab with live progress and cleanup.

== Upgrade Notice ==

= 1.0 =
First release.
