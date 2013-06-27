<?php

/*  Copyright 2012  Frank Staude  (email : frank@staude.net)

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


class disable_author_pages {

    /**
     * Constructor
     * 
     * Register all actions and filters
     */
    function __construct() {
        add_action( 'template_redirect',    array( 'disable_author_pages', 'disableauthorpage' ) );
        add_action( 'admin_init',           array( 'disable_author_pages', 'registerSettings' ) );
        add_action( 'admin_menu',           array( 'disable_author_pages', 'optionsMenu' ) );
        
        //add_action( 'plugins_loaded',   array( 'mime_types_extended', 'load_translations' ) );
        //add_filter( 'upload_mimes' ,    array( 'mime_types_extended', 'addMimeTypes' ) );
    }

    function disableautorpage() {
        if ( is_author() && TRUE === get_option( 'disable_author_pages_activate' ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

   function registerSettings() {
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_activate' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_destination' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_status' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_overwriteauthorlink' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_authorlink' );
   }
   
    /**
     * load the plugin textdomain
     * 
     * load the plugin textdomain with translations
     */
    function load_translations() {
        load_plugin_textdomain( 'disable_author_pages', false, apply_filters ( 'disable_author_pages_translationpath', dirname( plugin_basename( __FILE__ )) . '/languages/' ) ); 
    }    
    

    function optionsMenu() {
        add_options_page( 'Disable Autor Pages',  __('Author Pages','disable_author_pages', 'hinweis'), 'manage_options',
        __FILE__, array( 'disable_author_pages', 'createOptionsDisableAuthorMenu' ) );
    }
    

    function createOptionsDisableAuthorMenu() {
        global $settings, $mimetypes;
    ?>
    <div class="wrap"  id="settingsNeedfulTweaks">
    <h2><?php _e( 'MIME Types settings', 'disable_author_pages' ); ?></h2>
    <p><?php _e( 'Activate mimetypes you want to use.', 'disable_author_pages' ); ?></p>
    <form method="POST" action="options.php">
    <?php 
    settings_fields( 'disable_author_pages_settings' ); 
    echo '<table class="form-table">';
    ?>
    <tr>
        <td style="width: 13px;"><input type="checkbox" name="disable_author_pages_activate" value="1" <?php if ( get_option( 'disable_author_pages_activate' ) ) echo " checked "; ?> /></td>
        <td><?php _e( 'Disable Author Pages', 'disable_author_pages' ); ?></td>
    </tr>
    <tr>
        <td></td>
        <td><?php  echo wp_dropdown_pages("echo=0&show_option_none=Keine Seite"); ?></td>
    </tr>    
    </table>
    <br/>
    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'disable_author_pages' )?>" />
    </form>
    </div>
    <?php   
    }
    

}
?>
