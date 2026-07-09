# Disable Author Pages

WordPress plugin to disable the author pages and redirect visitors to another page.

* Plugin page: https://staude.net/wordpress/plugins/disable-author-pages/
* WordPress.org: https://wordpress.org/plugins/disable-author-pages/
* Issues & feature requests: https://github.com/staude/disable-author-pages/issues
* Translations: https://translate.wordpress.org/projects/wp-plugins/disable-author-pages

## Features

* Redirect author archives (`/author/username/`) to the homepage or any page, with HTTP status 301 or 307
* Optionally redirect only author pages of administrators
* Optionally redirect requests for non-existing authors (instead of the default 404)
* Optionally replace the author link with an empty string

### Author data protections (since 1.0.0)

WordPress exposes author data in more places than just the author archives.
Each of the following protections can be enabled separately (all disabled by default):

* **REST API** – hide the `/wp-json/wp/v2/users` endpoints for visitors who are not logged in, so usernames are no longer listed publicly. Logged-in users keep access, so the author selection in the block editor keeps working.
* **XML sitemap** – remove the users sitemap (`wp-sitemap-users.xml`) from the WordPress core sitemaps.
* **oEmbed** – remove the author name and the author archive link from oEmbed responses.
* **Author enumeration** – stop requests like `?author=1` from redirecting to `/author/username/` and leaking the username.

## Requirements

* WordPress 6.5 or later (tested up to 7.0)
* PHP 8.2 or later

## Installation

1. Install the plugin from within the Dashboard or upload the directory `disable-author-pages` and all its contents to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure it under *Settings → Author Pages*

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GPLv2 or later – see [LICENSE details in readme.txt](readme.txt).
