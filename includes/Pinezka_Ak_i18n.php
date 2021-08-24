<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 *
 * @package    Pinezka_Ak
 * @subpackage Pinezka_Ak/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Pinezka_Ak
 * @subpackage Pinezka_Ak/includes
 * @author     Artur Komoter <artur@komoter.pl>
 */
class Pinezka_Ak_i18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'pinezka-ak',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}