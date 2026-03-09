<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codetelemetry.com
 * @since             1.0.0
 * @package           Admin_Performance_Inspector
 *
 * @wordpress-plugin
 * Plugin Name:       Admin Performance Inspector
 * Plugin URI:        https://admin-performance-inspect.co
 * Description:       Admin Performance Inspector analyzes WordPress admin performance by profiling plugin load time, database queries, memory usage, and hook execution to help identify performance bottlenecks.
 * Version:           1.0.0
 * Author:            CodeTelemetry Labs
 * Author URI:        https://codetelemetry.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-performance-inspector
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ADMIN_PERFORMANCE_INSPECTOR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-admin-performance-inspector-activator.php
 */
function activate_admin_performance_inspector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-performance-inspector-activator.php';
	Admin_Performance_Inspector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-admin-performance-inspector-deactivator.php
 */
function deactivate_admin_performance_inspector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-admin-performance-inspector-deactivator.php';
	Admin_Performance_Inspector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_admin_performance_inspector' );
register_deactivation_hook( __FILE__, 'deactivate_admin_performance_inspector' );

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
function run_admin_performance_inspector() {

	$plugin = new Admin_Performance_Inspector();
	$plugin->run();

}
run_admin_performance_inspector();
