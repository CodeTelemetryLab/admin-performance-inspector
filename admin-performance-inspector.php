<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/CodeTelemetryLab
 * @since             1.0.0
 * @package           Admipein_Core
 *
 * Plugin Name:       Admin Performance Inspector
 * Plugin URI:        https://github.com/CodeTelemetryLab/admin-performance-inspector
 * Description:       Profiling tool for WordPress admin page performance. Analyze plugins, hooks, queries, and memory usage.
 * Version:           1.0.0
 * Author:            CodeTelemetryLab
 * Author URI:        https://github.com/CodeTelemetryLab
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-performance-inspector
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Track execution start time for the profiler
if ( ! defined( 'ADMIPEIN_START_TIME' ) ) {
	define( 'ADMIPEIN_START_TIME', microtime( true ) );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ADMIPEIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-admin-performance-inspector-activator.php
 */
function admipein_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-performance-inspector-activator.php';
	Admipein_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-admin-performance-inspector-deactivator.php
 */
function admipein_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-performance-inspector-deactivator.php';
	Admipein_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'admipein_activate' );
register_deactivation_hook( __FILE__, 'admipein_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-admin-performance-inspector.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function admipein_run() {

	$plugin = new Admipein_Core();
	$plugin->run();

}
admipein_run();

