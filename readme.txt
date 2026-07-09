=== Disable Author Pages ===
Contributors: f.staude
Donate link: https://staude.net/donate/
Tags: author, author page, redirect, security, user enumeration
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Disable the author pages

== Description ==

Disable the author pages ( /author=? ) in WordPress and redirect the user to another page.

Beyond the classic author archives, WordPress exposes author data in several other places.
This plugin can optionally close those as well:

* **REST API**: hide the `/wp-json/wp/v2/users` endpoints for visitors who are not logged in, so usernames are no longer listed publicly.
* **XML sitemap**: remove the users sitemap (`wp-sitemap-users.xml`) from the WordPress core sitemaps.
* **oEmbed**: remove the author name and the author archive link from oEmbed responses.
* **Author enumeration**: stop requests like `?author=1` from redirecting to `/author/username/` and leaking the username.

All options are disabled by default, so updating the plugin does not change the behavior of an existing installation.

== Installation ==

1. Install the plugin from within the Dashboard or upload the directory `disable-author-pages` and all its contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure it under settings/disable author pages

== Frequently Asked Questions ==

= I have a new translation  =

Translations are handled via  https://translate.wordpress.org/projects/wp-plugins/disable-author-pages

= I found a bug  =

Please report it at https://github.com/staude/disable-author-pages/issues

= I have a feature request =

Please report it at https://github.com/staude/disable-author-pages/issues

= Why are usernames still visible in the REST API? =

Enable the option "Hide users in the REST API" on the settings page. The endpoints stay
available for logged-in users, so the author selection in the block editor keeps working.

== Changelog ==
= 1.0.0 =
- Modernized for current WordPress (tested up to 7.0) and PHP (requires 8.2, tested against PHP 8.4)
- New option: hide the REST API user endpoints (/wp/v2/users) for visitors. Thanks to paulo-graca (issue #10 / PR #11)
- New option: remove the users sitemap from the WordPress core sitemaps (wp-sitemap-users.xml)
- New option: remove author name and author archive link from oEmbed responses
- New option: prevent author enumeration via ?author=N (canonical redirect)
- The destination page dropdown now offers "Homepage" as an explicit choice (issue #6)
- Fixed the plugin tags in the readme (issue #8)
- Settings are now sanitized, output is escaped, redirects use wp_safe_redirect()
- Uninstall cleanup moved to uninstall.php using delete_option() instead of a raw SQL query
- Removed the obsolete load_plugin_textdomain() call (translations load automatically since WordPress 4.6)

= 0.11 =
- version tagging sync github / wordpress.org svn. Thanks to swissspidy
- fix: notice plugin_settings_link not static. Thanks to swissspidy

= 0.10 =
- DB clean up on uninstall
- added settings list on pluginlist

= 0.9 =
- fix: redirect to pages. Thanks to Georg
- changed text domain for translations via translate.wordpress.org
- added github updater metadata

= 0.8 =
- Updated German Translation, Urls and Description. Thanks to krafit
- Minor spelling correction. Thanks to tristanpenman

= 0.7 =
- Make it work if WordPress installed in a subdirectory
  https://github.com/staude/disable-author-pages/pull/1
  Thanks to smeric
- Moved sourcecode to GitHub: https://github.com/staude/disable-author-pages
- Moved Issuetracker to GitHub: https://github.com/staude/disable-author-pages/issues


= 0.6 =
Fix: When I choose redirect to "Profile" page and save, the dialogue reverts back to Homepage. ( Thanks to Tradedog )

= 0.5 =
Fix: Notice: Trying to get property of non-object in wp-includes/post-template.php on line 29 (from user BackuPs - Thanks)

= 0.4 =
- Added feature disable only admin author page (from user BackuPs - Thanks)
- Added feature redirect non exists autor page (instead of default 404) ( Request from Matt Jensen - Thanks)


= 0.3 =
Disable some php notices if WP Debug active

= 0.2 =
- Added settings to Options > Author Pages
- 0000027: Overwrite the author_link (optional).
- 0000029: status of redirect
- 0000028: Select destination for redirect


= 0.1 =
First version.

== Upgrade Notice ==

= 1.0.0 =
Requires PHP 8.2 and WordPress 6.5 or later. New optional protections for the REST API, XML sitemap, oEmbed and author enumeration - all disabled by default.
