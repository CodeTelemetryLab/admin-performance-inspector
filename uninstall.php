<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://codetelemetry.com
 * @since      1.0.0
 *
 * @package    Admin_Performance_Inspector
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'api_slow_query_threshold' );
delete_option( 'api_enable_query_profiling' );
delete_option( 'api_enable_hook_profiling' );
delete_option( 'api_enable_memory_monitoring' );
