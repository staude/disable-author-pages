# Changelog

## 1.0.0

* Modernized for current WordPress (tested up to 7.0) and PHP (requires 8.2, tested against PHP 8.4)
* New option: hide the REST API user endpoints (`/wp/v2/users`) for visitors. Thanks to paulo-graca ([#10](https://github.com/staude/disable-author-pages/issues/10) / [PR #11](https://github.com/staude/disable-author-pages/pull/11))
* New option: remove the users sitemap from the WordPress core sitemaps (`wp-sitemap-users.xml`)
* New option: remove author name and author archive link from oEmbed responses
* New option: prevent author enumeration via `?author=N` (canonical redirect)
* The destination page dropdown now offers "Homepage" as an explicit choice ([#6](https://github.com/staude/disable-author-pages/issues/6))
* Fixed the plugin tags in the readme ([#8](https://github.com/staude/disable-author-pages/issues/8))
* Settings are now sanitized, output is escaped, redirects use `wp_safe_redirect()`
* Uninstall cleanup moved to `uninstall.php` using `delete_option()` instead of a raw SQL query
* Removed the obsolete `load_plugin_textdomain()` call (translations load automatically since WordPress 4.6)

## 0.11

* version tagging sync github / wordpress.org svn. Thanks to swissspidy
* fix: notice plugin_settings_link not static. Thanks to swissspidy

## 0.10

* DB clean up on uninstall
* added settings list on pluginlist

## 0.9

* fix: redirect to pages. Thanks to Georg
* changed text domain for translations via translate.wordpress.org
* added github updater metadata

## 0.8

* Updated German Translation, Urls and Description. Thanks to krafit
* Minor spelling correction. Thanks to tristanpenman

## 0.7

* Make it work if WordPress installed in a subdirectory ([PR #1](https://github.com/staude/disable-author-pages/pull/1)). Thanks to smeric
* Moved sourcecode to GitHub: https://github.com/staude/disable-author-pages
* Moved Issuetracker to GitHub: https://github.com/staude/disable-author-pages/issues

## 0.6

* Fix: When I choose redirect to "Profile" page and save, the dialogue reverts back to Homepage. (Thanks to Tradedog)

## 0.5

* Fix: Notice: Trying to get property of non-object in wp-includes/post-template.php on line 29 (from user BackuPs - Thanks)

## 0.4

* Added feature disable only admin author page (from user BackuPs - Thanks)
* Added feature redirect non exists author page (instead of default 404) (Request from Matt Jensen - Thanks)

## 0.3

* Disable some php notices if WP Debug active

## 0.2

* Added settings to Options > Author Pages
* 0000027: Overwrite the author_link (optional)
* 0000029: status of redirect
* 0000028: Select destination for redirect

## 0.1

* First version.
