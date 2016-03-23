<?php

/*  
 Copyright 2014  Frank Staude  (email : frank@staude.net)

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
        add_action( 'template_redirect',    array( 'disable_author_pages', 'disable_author_page' ) );
        add_action( 'admin_init',           array( 'disable_author_pages', 'register_settings' ) );
        add_action( 'admin_menu',           array( 'disable_author_pages', 'options_menu' ) );        
        add_action( 'plugins_loaded',       array( 'disable_author_pages', 'load_translations' ) );
        add_filter( 'author_link',          array( 'disable_author_pages', 'disable_author_link') );
    }

    /**
     * Redirect the user
     * 
     * This function is registerd to the template_redirect hook and  checks
     * to redirect the user to the selected page (or to the homepage)
     * 
     */
    static public function disable_author_page() {
        global $post;
        $authorrequest = FALSE;
        if ( is_404() && ( get_query_var( 'author' ) || get_query_var( 'author_name' ) ) ) {
              if ( get_option( 'disable_author_pages_redirect_non_authors' ) == 1 ) {
                  $authorrequest = true;
              }
        }

        if ( is_404() && ! ( get_query_var( 'author' ) || get_query_var( 'author_name' ) ) ) {
              return;
        }
 
        if ( ( is_author() || $authorrequest ) && get_option( 'disable_author_pages_activate' ) == 1 ) {
            $adminonly = get_option( 'disable_author_pages_adminonly', '0' );
            $author_can = false;

            if ( ! is_404() && $adminonly ) {
                if( is_object( $post ) ) {
                    $author_can = author_can( get_the_ID(), 'administrator' );
                }
            }

            if ( $adminonly && $author_can===true || !$adminonly && !is_404() || is_404() && ( get_option( 'disable_author_pages_redirect_non_authors' ) == 1 ) ) {
                $status = get_option( 'disable_author_pages_status', '301' );
                $url = get_option( 'disable_author_pages_destination', '' );
                if ( $url == '' ) {
                    $url = home_url();
                }
                wp_redirect( $url, $status );
                exit;
            }
        }
    }

    /**
     * Register all settings 
     * 
     * Register all the settings, the plugin uses.
     */
    static public function register_settings() {
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_activate' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_destination' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_status' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_authorlink' );
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_adminonly' ); 
        register_setting( 'disable_author_pages_settings', 'disable_author_pages_redirect_non_authors' ); 
    }
   
    /**
     * Overwrite the author url with an empty string
     * 
     * @param string $content url to author page
     * @return string
     */
    static public function disable_author_link( $content ) {
        if ( get_option( 'disable_author_pages_authorlink', '0' ) == 1 ) {
            return "";
        } else {
            return $content;
        }
    }
   
    /**
     * load the plugin textdomain
     * 
     * load the plugin textdomain with translations for the backend settingspage
     */
    static public function load_translations() {
        load_plugin_textdomain( 'disable_author_pages', false, apply_filters ( 'disable_author_pages_translationpath', dirname( plugin_basename( __FILE__ )) . '/languages/' ) ); 
    }    

    /**
     * Generate the options menu page
     * 
     * Generate the options page under the options menu
     */
    static public function options_menu() {
        add_options_page( 'Disable Author Pages',  __('Author Pages','disable_author_pages', 'hinweis'), 'manage_options',
        __FILE__, array( 'disable_author_pages', 'create_options_disable_author_menu' ) );
    }

    /**
     * Generate the options page for the plugin
     * 
     * @global type $settings
     */
    static public function create_options_disable_author_menu() {
        global $settings;
        $selectedpage = get_option( 'disable_author_pages_destination' );
    ?>
    <div class="wrap"  id="disableauthorpages">
    <h2><?php _e( 'Disable Author settings', 'disable_author_pages' ); ?></h2>
    <p><?php _e( 'Settings to disable the author pages.', 'disable_author_pages' ); ?></p>
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
        <td>
            <select name="disable_author_pages_status"> 
                <option value="301" <?php if ( get_option( 'disable_author_pages_status' ) == '301' ) { echo ' selected '; } ?> ><?php _e( '301 (Moved Permanently)', 'disable_author_pages' );?></option>
                <option value="307" <?php if ( get_option( 'disable_author_pages_status' ) == '307' ) { echo ' selected '; } ?> ><?php _e( '307 (Temporary Redirect)', 'disable_author_pages' );?></option>
            </select> <?php _e( 'HTTP Status', 'disable_author_pages' );?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <?php  echo wp_dropdown_pages("name=disable_author_pages_destination&selected={$selectedpage}&echo=0&show_option_none=" . __( 'Homepage', 'disable_author_pages' ) ); ?>
            <?php _e( 'Destinationpage', 'disable_author_pages' ); ?>
        </td>
    </tr>    
    <tr>
        <td></td>
        <td>
            <input type="checkbox" name="disable_author_pages_authorlink" value="1" <?php if ( get_option( 'disable_author_pages_authorlink' ) ) echo " checked "; ?> />
            <?php _e( 'Disable Authorlink', 'disable_author_pages' ); ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input type="checkbox" name="disable_author_pages_redirect_non_authors" value="1" <?php if ( get_option( 'disable_author_pages_redirect_non_authors' ) ) echo " checked "; ?> />
            <?php _e( 'Redirect non exists author pages', 'disable_author_pages' ); ?>
        </td>          
    </tr>   
    <tr>
        <td></td>
        <td>
            <input type="checkbox" name="disable_author_pages_adminonly" value="1" <?php if ( get_option( 'disable_author_pages_adminonly' ) ) echo " checked "; ?> />
            <?php _e( 'Disable for admin author pages only', 'disable_author_pages' ); ?>
        </td>          
    </tr>       
    </table>
    <br/>
    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'disable_author_pages' )?>" />
    </form>
    </div>
    <?php   
    }
}

