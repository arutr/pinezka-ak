<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    Pinezka_Ak
 * @subpackage Pinezka_Ak/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pinezka_Ak
 * @subpackage Pinezka_Ak/includes
 * @author     Artur Komoter <artur@komoter.pl>
 */
class Pinezka_Ak_Activator
{
    public static function activate()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        dbDelta("
CREATE TABLE IF NOT EXISTS `pinezka_ak_team` (
    ID BIGINT(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    leader_id BIGINT(20) UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL
) $charset_collate;\n");
        dbDelta("
CREATE TABLE IF NOT EXISTS `pinezka_ak_team_member` (
    team_id BIGINT(20) UNSIGNED NOT NULL,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    PRIMARY KEY (team_id, user_id)
) $charset_collate;\n");
        dbDelta("
CREATE TABLE IF NOT EXISTS `pinezka_ak_markers` (
    ID BIGINT(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT(20) UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(1000),
    coordinates VARCHAR(40),
    type VARCHAR(30),
    city VARCHAR(255),
    region VARCHAR(255),
    image VARCHAR(30)
) $charset_collate;\n");
    }
}