=== Issues Exporter for GitLab ===
Contributors: shitalparmar443
Donate link: https://paypal.me/shitalparmar443
Tags: gitlab, csv export, issues, ajax, fluent boards
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Export GitLab issues to a CSV file with AJAX progress tracking and Fluent Boards compatibility.

== Description ==

Export GitLab issues to a CSV file with AJAX progress tracking and Fluent Boards compatibility.

Key features:
* Export issues directly from GitLab into CSV format.
* AJAX-based export with live progress tracking.
* Fluent Boards compatibility.
* Manage and delete generated CSV files from the admin panel.

== Installation ==

= From WordPress Admin =
1. Go to **Plugins → Add New**.
2. Search for **Issues Exporter for GitLab**.
3. Install and Activate.

= Manual Installation =
1. Upload the plugin folder `issues-exporter-for-gitlab` to `/wp-content/plugins/`.
2. Activate the plugin from the **Plugins** menu in WordPress.

== How to Create a Project Access Token in GitLab ==

= Prerequisites =
* You must have sufficient permissions (Owner or Maintainer role).
* On **GitLab.com**, Project Access Tokens require **Premium or Ultimate subscription**, or a trial.
* On **self-managed GitLab instances**, tokens are generally available without restrictions.

References:
* https://docs.gitlab.com/user/project/settings/project_access_tokens/
* https://forum.gitlab.com/

= Steps =
1. Log in to GitLab and open your project.
2. Navigate to **Settings → Access Tokens**.
3. Click **Add new token**.
4. Fill in:
   * Token name
   * (Optional) Token description
   * Expiration date (default 30 days; non-expiring tokens removed since GitLab 16.0)
   * Role (Guest, Reporter, Developer, Maintainer, Owner)
   * Scopes (e.g., `api`, `read_api`)
5. Click **Create project access token**.
6. Copy and save the token immediately — it will only be shown once.

== External Services ==

This plugin connects to the GitLab API (https://gitlab.com) to retrieve project issues and export them as CSV files.

Data sent:
* Project ID (required to fetch issues)
* Access token (required for authentication)

Data usage:
* Sent only during export via AJAX requests.
* No WordPress personal user data is sent to GitLab.

Service Provider:
* GitLab Inc.
* Terms of Service: https://about.gitlab.com/terms/
* Privacy Policy: https://about.gitlab.com/privacy/

== Frequently Asked Questions ==

= How many issues can it export? =
Up to 5000 issues, exported in batches of 50.

= Is my access token saved securely? =
Yes, stored using the WordPress Options API and never exposed in frontend HTML.

= Can I delete old CSV files? =
Yes, all generated files are listed in the admin page with delete options.

== Screenshots ==

1. Settings screen.
2. Export progress with status updates.
3. Generated files list.

== Changelog ==

= 1.0 =
* Initial release: Export GitLab issues with live progress and file cleanup.

== Upgrade Notice ==

= 1.0 =
First release of Issues Exporter for GitLab.
