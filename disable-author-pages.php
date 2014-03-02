<?php
/*
Plugin Name: Disable Author Pages
Plugin URI: http://staude.net/wordpress/plugins/DisableAuthorPages
Description: Disable the author pages in wordpress and redirect to the homepage.
Author: Frank Staude
Version: 0.5
Text Domain: disable_author_pages
Domain Path: languages
Author URI: http://www.staude.net/
Compatibility: WordPress 3.8.1
*/

/*  Copyright 2014  Frank Staude  (email : frank@staude.net)

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
        //$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 'backend_startpage';" );
    }

    register_uninstall_hook( __FILE__,  'disable_author_pages_uninstall' );

    $disable_author_pages = new disable_author_pages();

}