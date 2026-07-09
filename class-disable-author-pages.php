<?php

/*
 Copyright 2014-2026  Frank Staude  (email : frank@staude.net)

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

defined( 'ABSPATH' ) || exit;

class Disable_Author_Pages {

    public const OPTION_ACTIVATE             = 'disable_author_pages_activate';
    public const OPTION_DESTINATION          = 'disable_author_pages_destination';
    public const OPTION_STATUS               = 'disable_author_pages_status';
    public const OPTION_AUTHORLINK           = 'disable_author_pages_authorlink';
    public const OPTION_ADMINONLY            = 'disable_author_pages_adminonly';
    public const OPTION_REDIRECT_NON_AUTHORS = 'disable_author_pages_redirect_non_authors';
    public const OPTION_RESTAPI              = 'disable_author_pages_restapi';
    public const OPTION_SITEMAP              = 'disable_author_pages_sitemap';
    public const OPTION_OEMBED               = 'disable_author_pages_oembed';
    public const OPTION_CANONICAL            = 'disable_author_pages_canonical';

    private const SETTINGS_GROUP = 'disable_author_pages_settings';
    private const MENU_SLUG      = 'disable-author-pages';

    /**
     * Register all actions and filters.
     */
    public function __construct() {
        add_action( 'template_redirect', [ $this, 'disable_author_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_menu', [ $this, 'options_menu' ] );
        add_filter( 'author_link', [ $this, 'disable_author_link' ] );
        add_filter( 'plugin_action_links_disable-author-pages/disable-author-pages.php', [ $this, 'plugin_settings_link' ] );
        add_filter( 'rest_endpoints', [ $this, 'disable_rest_user_endpoints' ] );
        add_filter( 'wp_sitemaps_add_provider', [ $this, 'disable_users_sitemap' ], 10, 2 );
        add_filter( 'oembed_response_data', [ $this, 'disable_oembed_author' ] );
        add_filter( 'redirect_canonical', [ $this, 'disable_author_canonical_redirect' ] );
    }

    /**
     * Redirect requests for author pages to the configured destination.
     *
     * Runs on template_redirect. Existing author archives are redirected when
     * the plugin is active (optionally only for administrator authors);
     * requests for non-existing authors (404) are redirected when the
     * corresponding option is enabled.
     */
    public function disable_author_page(): void {
        if ( ! $this->option_enabled( self::OPTION_ACTIVATE ) ) {
            return;
        }

        $is_author_request = get_query_var( 'author' ) || get_query_var( 'author_name' );

        if ( is_404() ) {
            if ( ! $is_author_request || ! $this->option_enabled( self::OPTION_REDIRECT_NON_AUTHORS ) ) {
                return;
            }
        } elseif ( is_author() ) {
            if ( $this->option_enabled( self::OPTION_ADMINONLY ) && ! $this->author_is_administrator() ) {
                return;
            }
        } else {
            return;
        }

        $this->redirect();
    }

    /**
     * Whether the author of the currently queried post is an administrator.
     */
    private function author_is_administrator(): bool {
        global $post;

        if ( ! is_object( $post ) ) {
            return false;
        }

        return author_can( get_the_ID(), 'administrator' );
    }

    /**
     * Send the redirect to the configured destination page (or the homepage).
     */
    private function redirect(): void {
        $status      = (int) get_option( self::OPTION_STATUS, '301' );
        $destination = (int) get_option( self::OPTION_DESTINATION );

        $url = $destination > 0 ? get_permalink( $destination ) : home_url();
        if ( ! $url ) {
            $url = home_url();
        }

        wp_safe_redirect( $url, $status );
        exit;
    }

    /**
     * Register all settings the plugin uses.
     */
    public function register_settings(): void {
        $checkboxes = [
            self::OPTION_ACTIVATE,
            self::OPTION_AUTHORLINK,
            self::OPTION_ADMINONLY,
            self::OPTION_REDIRECT_NON_AUTHORS,
            self::OPTION_RESTAPI,
            self::OPTION_SITEMAP,
            self::OPTION_OEMBED,
            self::OPTION_CANONICAL,
        ];

        foreach ( $checkboxes as $option ) {
            register_setting(
                self::SETTINGS_GROUP,
                $option,
                [
                    'type'              => 'string',
                    'sanitize_callback' => [ $this, 'sanitize_checkbox' ],
                    'default'           => '',
                ]
            );
        }

        register_setting(
            self::SETTINGS_GROUP,
            self::OPTION_STATUS,
            [
                'type'              => 'string',
                'sanitize_callback' => [ $this, 'sanitize_status' ],
                'default'           => '301',
            ]
        );

        register_setting(
            self::SETTINGS_GROUP,
            self::OPTION_DESTINATION,
            [
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 0,
            ]
        );
    }

    /**
     * Normalize a checkbox value to '1' or ''.
     */
    public function sanitize_checkbox( $value ): string {
        return '1' === (string) $value ? '1' : '';
    }

    /**
     * Allow only the supported HTTP redirect status codes.
     */
    public function sanitize_status( $value ): string {
        return in_array( (string) $value, [ '301', '307' ], true ) ? (string) $value : '301';
    }

    /**
     * Overwrite the author url with an empty string.
     *
     * @param string $content Url to the author page.
     */
    public function disable_author_link( $content ): string {
        if ( $this->option_enabled( self::OPTION_AUTHORLINK ) ) {
            return '';
        }

        return (string) $content;
    }

    /**
     * Remove the public REST API user endpoints for visitors.
     *
     * The endpoints stay available for logged-in users, so the author
     * selection in the block editor keeps working.
     *
     * @param array $endpoints Registered REST API endpoints.
     */
    public function disable_rest_user_endpoints( array $endpoints ): array {
        if ( ! $this->option_enabled( self::OPTION_RESTAPI ) || is_user_logged_in() ) {
            return $endpoints;
        }

        unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

        return $endpoints;
    }

    /**
     * Remove the users provider from the core XML sitemaps (wp-sitemap-users.xml).
     *
     * @param WP_Sitemaps_Provider|false $provider The sitemap provider.
     * @param string                     $name     Name of the provider.
     * @return WP_Sitemaps_Provider|false
     */
    public function disable_users_sitemap( $provider, $name ) {
        if ( 'users' === $name && $this->option_enabled( self::OPTION_SITEMAP ) ) {
            return false;
        }

        return $provider;
    }

    /**
     * Remove author name and author archive url from oEmbed responses.
     *
     * @param array $data The oEmbed response data.
     */
    public function disable_oembed_author( array $data ): array {
        if ( $this->option_enabled( self::OPTION_OEMBED ) ) {
            unset( $data['author_url'], $data['author_name'] );
        }

        return $data;
    }

    /**
     * Prevent author enumeration via the canonical redirect.
     *
     * Without this, a request for ?author=N gets redirected to
     * /author/<username>/ and leaks the username in the Location header
     * before the plugin's own redirect runs.
     *
     * @param string|false $redirect_url The canonical redirect url.
     * @return string|false
     */
    public function disable_author_canonical_redirect( $redirect_url ) {
        if ( $this->option_enabled( self::OPTION_CANONICAL )
            && ( get_query_var( 'author' ) || get_query_var( 'author_name' ) ) ) {
            return false;
        }

        return $redirect_url;
    }

    /**
     * Add the options page under the settings menu.
     */
    public function options_menu(): void {
        add_options_page(
            'Disable Author Pages',
            __( 'Author Pages', 'disable-author-pages' ),
            'manage_options',
            self::MENU_SLUG,
            [ $this, 'create_options_disable_author_menu' ]
        );
    }

    /**
     * Render the options page for the plugin.
     */
    public function create_options_disable_author_menu(): void {
    ?>
    <div class="wrap" id="disableauthorpages">
    <h1><?php esc_html_e( 'Disable Author settings', 'disable-author-pages' ); ?></h1>
    <p><?php esc_html_e( 'Settings to disable the author pages.', 'disable-author-pages' ); ?></p>
    <form method="POST" action="options.php">
    <?php settings_fields( self::SETTINGS_GROUP ); ?>
    <table class="form-table" role="presentation">
    <tr>
        <th scope="row"><?php esc_html_e( 'Disable Author Pages', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_ACTIVATE ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_ACTIVATE ) ); ?> />
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'HTTP Status', 'disable-author-pages' ); ?></th>
        <td>
            <select name="<?php echo esc_attr( self::OPTION_STATUS ); ?>">
                <option value="301" <?php selected( get_option( self::OPTION_STATUS, '301' ), '301' ); ?>><?php esc_html_e( '301 (Moved Permanently)', 'disable-author-pages' ); ?></option>
                <option value="307" <?php selected( get_option( self::OPTION_STATUS, '301' ), '307' ); ?>><?php esc_html_e( '307 (Temporary Redirect)', 'disable-author-pages' ); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Destinationpage', 'disable-author-pages' ); ?></th>
        <td>
            <?php
            wp_dropdown_pages(
                [
                    'name'              => self::OPTION_DESTINATION,
                    'selected'          => (int) get_option( self::OPTION_DESTINATION ),
                    'show_option_none'  => __( '&mdash; Homepage &mdash;', 'disable-author-pages' ),
                    'option_none_value' => '0',
                ]
            );
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Disable Authorlink', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_AUTHORLINK ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_AUTHORLINK ) ); ?> />
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Redirect non exists author pages', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_REDIRECT_NON_AUTHORS ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_REDIRECT_NON_AUTHORS ) ); ?> />
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Disable for admin author pages only', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_ADMINONLY ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_ADMINONLY ) ); ?> />
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Hide users in the REST API', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_RESTAPI ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_RESTAPI ) ); ?> />
            <p class="description"><?php esc_html_e( 'Removes the /wp/v2/users endpoints for visitors who are not logged in.', 'disable-author-pages' ); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Remove authors from the XML sitemap', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_SITEMAP ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_SITEMAP ) ); ?> />
            <p class="description"><?php esc_html_e( 'Removes the users sitemap (wp-sitemap-users.xml) from the WordPress core sitemaps.', 'disable-author-pages' ); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Remove author from oEmbed data', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_OEMBED ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_OEMBED ) ); ?> />
            <p class="description"><?php esc_html_e( 'Removes the author name and the author archive link from oEmbed responses.', 'disable-author-pages' ); ?></p>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php esc_html_e( 'Prevent author enumeration', 'disable-author-pages' ); ?></th>
        <td>
            <input type="checkbox" name="<?php echo esc_attr( self::OPTION_CANONICAL ); ?>" value="1" <?php checked( $this->option_enabled( self::OPTION_CANONICAL ) ); ?> />
            <p class="description"><?php esc_html_e( 'Stops requests like ?author=1 from redirecting to /author/username/ and leaking the username.', 'disable-author-pages' ); ?></p>
        </td>
    </tr>
    </table>
    <?php submit_button(); ?>
    </form>
    </div>
    <?php
    }

    /**
     * Add a settings link on the plugin list page.
     *
     * @param array $links Existing action links.
     * @since 0.10
     */
    public function plugin_settings_link( $links ): array {
        $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=' . self::MENU_SLUG ) ) . '">' . __( 'Settings', 'disable-author-pages' ) . '</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }

    /**
     * Whether a checkbox option is enabled.
     */
    private function option_enabled( string $option ): bool {
        return '1' === (string) get_option( $option );
    }

}
