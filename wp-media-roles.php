<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              5nines.com
 * @since             1.0.0
 * @package           Wp_Media_Roles
 *
 * @wordpress-plugin
 * Plugin Name:       WP Media Roles
 * Plugin URI:        5nines.com/wordpress/plugins/wp-media-roles
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Matthew Kirk
 * Author URI:        5nines.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-media-roles
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-media-roles-activator.php
 */
function activate_wp_media_roles() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-media-roles-activator.php';
	Wp_Media_Roles_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-media-roles-deactivator.php
 */
function deactivate_wp_media_roles() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-media-roles-deactivator.php';
	Wp_Media_Roles_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_media_roles' );
register_deactivation_hook( __FILE__, 'deactivate_wp_media_roles' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-media-roles.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_media_roles() {

	$plugin = new Wp_Media_Roles();
	$plugin->run();

}
run_wp_media_roles();
