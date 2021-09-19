<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Pinezka_Ak
 * @subpackage Pinezka_Ak/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pinezka_Ak
 * @subpackage Pinezka_Ak/admin
 * @author     Artur Komoter <artur@komoter.pl>
 */
class Pinezka_Ak_Admin
{
    const MENU_SLUG_MARKERS = 'markers';
    const MENU_SLUG_TEAMS = 'teams';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Create admin menu pages.
     */
    public function get_menu()
    {
        // Markers
        add_menu_page(
            'Pinezki',
            'Pinezki',
            'read',
            self::MENU_SLUG_MARKERS,
            [&$this, 'get_markers_page_content'],
            'dashicons-post-status',
            26
        );
        add_submenu_page(
            self::MENU_SLUG_MARKERS,
            'Pinezka',
            'Dodaj nową',
            'read',
            'marker-edit',
            [&$this, 'get_marker_edit_page_content']
        );

        // Teams
        add_menu_page(
            'Zespoły',
            'Zespoły',
            'read',
            self::MENU_SLUG_TEAMS,
            [&$this, 'get_teams_page_content'],
            'dashicons-groups',
            27
        );
        add_submenu_page(
            self::MENU_SLUG_TEAMS,
            'Zespół',
            'Dodaj nowy',
            'read',
            'team-edit',
            [&$this, 'get_team_edit_page_content']
        );
    }

    /**
     * Get Markers menu page.
     */
    public function get_markers_page_content()
    {
        include_once dirname(__FILE__) . '/markers.php';
    }

    /**
     * Get New Marker submenu page.
     */
    public function get_marker_edit_page_content()
    {
        include_once dirname(__FILE__) . '/marker-edit.php';
    }

    /**
     * Get Teams menu page.
     */
    public function get_teams_page_content()
    {
        include_once dirname(__FILE__) . '/teams.php';
    }

    /**
     * Get New Team submenu page.
     */
    public function get_team_edit_page_content()
    {
        include_once dirname(__FILE__) . '/team-edit.php';
    }

    /**
     * The field on the editing screens.
     *
     * @param $user WP_User user object
     */
    public function usermeta_form_field_institution(WP_User $user)
    {
        ?>
        <h3>Pinezka AK</h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="institution">
                        Uczelnia
                    </label>
                </th>
                <td>
                    <input class="regular-text ltr"
                           id="institution"
                           name="institution"
                           value="<?= esc_attr(get_user_meta($user->ID, 'institution', true)) ?>">
                    <p class="description">
                        Wprowadź nazwę swojej uczelni lub szkoły.
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * The save action.
     *
     * @param $user_id int the ID of the current user.
     *
     * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
     */
    public function usermeta_form_field_institution_update(int $user_id): bool
    {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        // create/update user meta for the $user_id
        return update_user_meta(
            $user_id,
            'institution',
            $_POST['institution']
        );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pinezka_Ak_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pinezka_Ak_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style('leaflet_css', plugin_dir_url(__FILE__) . 'css/leaflet.css');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pinezka_Ak_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pinezka_Ak_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script('leaflet_js', plugin_dir_url(__FILE__) . 'js/leaflet.js', ['jquery']);
    }

}