<?php
/**
 * Uninstall handler: remove all plugin options.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$disable_author_pages_options = [
    'disable_author_pages_activate',
    'disable_author_pages_destination',
    'disable_author_pages_status',
    'disable_author_pages_authorlink',
    'disable_author_pages_adminonly',
    'disable_author_pages_redirect_non_authors',
    'disable_author_pages_restapi',
    'disable_author_pages_sitemap',
    'disable_author_pages_oembed',
    'disable_author_pages_canonical',
];

foreach ( $disable_author_pages_options as $disable_author_pages_option ) {
    delete_option( $disable_author_pages_option );
}
