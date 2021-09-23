<?php
/**
 * Pinezka AK
 *
 * @package           PinezkaAk
 * @author            Artur Komoter
 * @copyright         2021 Artur Komoter
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Pinezka AK
 * Version:           1.1.0
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
/*
Pinezka AK is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Pinezka AK is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Pinezka AK. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

const PINEZKA_AK_VERSION = '1.1.0';
const PINEZKA_AK_MARKERS_TABLE = 'pinezka_ak_markers';
const PINEZKA_AK_TEAM_TABLE = 'pinezka_ak_team';
const PINEZKA_AK_TEAM_MEMBER_TABLE = 'pinezka_ak_team_member';

/**
 * The code that runs during plugin activation.
 */
function activate_pinezka_ak()
{
    require_once plugin_dir_path(__FILE__) . 'includes/Pinezka_Ak_Activator.php';
    Pinezka_Ak_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_pinezka_ak()
{
    require_once plugin_dir_path(__FILE__) . 'includes/Pinezka_Ak_Deactivator.php';
    Pinezka_Ak_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_pinezka_ak');
register_deactivation_hook(__FILE__, 'deactivate_pinezka_ak');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/Pinezka_Ak.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pinezka_ak()
{
    $plugin = new Pinezka_Ak();
    $plugin->run();
}

run_pinezka_ak();