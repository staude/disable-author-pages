<?php
/*
 * Plugin Name: Disable Author Pages
 * Plugin URI: https://staude.net/wordpress/plugins/disable-author-pages/
 * Description: Disable the author pages in WordPress and redirect to the homepage.
 * Author: Frank Staude
 * Version: 0.11
 * Text Domain: disable-author-pages
 * Domain Path: languages
 * Author URI: https://staude.net/
 * Compatibility: WordPress 4.7.2
 * GitHub Plugin URI: https://github.com/staude/disable-author-pages/
 * GitHub Branch: master
 */

/*  Copyright 2014-2017  Frank Staude  (email : frank@staude.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if (!class_exists( 'disable_author_pages' ) ) {

    include_once dirname( __FILE__ ) .'/class-disable-author-pages.php';

    /**
     * Delete options on plugin install
     */
    function disable_author_pages_uninstall() {
        global $wpdb;
        $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name like 'disable_author_pages_%';" );
    }

    register_uninstall_hook( __FILE__,  'disable_author_pages_uninstall' );

    $disable_author_pages = new disable_author_pages();

}