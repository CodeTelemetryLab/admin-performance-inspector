<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://github.com/CodeTelemetryLab
 * @since      1.0.0
 *
 * @package    Admipein_Core
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'admipein_slow_query_threshold' );
delete_option( 'admipein_enable_query_profiling' );
delete_option( 'admipein_enable_hook_profiling' );
delete_option( 'admipein_enable_memory_monitoring' );
