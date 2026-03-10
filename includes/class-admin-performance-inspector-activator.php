<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Performance_Inspector_Activator {

	public static function activate() {

		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			wp_die( esc_html__( 'Admin Performance Inspector requires PHP 7.4 or higher.', 'admin-performance-inspector' ) );
		}

		global $wp_version;
		if ( version_compare( $wp_version, '6.0', '<' ) ) {
			wp_die( esc_html__( 'Admin Performance Inspector requires WordPress 6.0 or higher.', 'admin-performance-inspector' ) );
		}

		$mu_dir = WP_CONTENT_DIR . '/mu-plugins';
		if ( ! is_dir( $mu_dir ) ) {
			wp_mkdir_p( $mu_dir );
		}
		
		$mu_plugin_code = "<?php\n" .
		"/**\n" .
		" * Plugin Name: API Profiler Drop-in\n" .
		" * Description: Required by Admin Performance Inspector for accurate plugin load times.\n" .
		" */\n" .
		"if ( ! defined( 'API_PLUGIN_LOAD_TIMES' ) ) {\n" .
		"    define( 'API_PLUGIN_LOAD_TIMES', true );\n" .
		"    global \$api_plugin_load_times;\n" .
		"    \$api_plugin_load_times = array();\n" .
		"    add_action( 'muplugins_loaded', function() {\n" .
		"        global \$api_plugin_last_time;\n" .
		"        \$api_plugin_last_time = microtime( true );\n" .
		"    }, 99999 );\n" .
		"    add_action( 'plugin_loaded', function( \$plugin ) {\n" .
		"        global \$api_plugin_load_times, \$api_plugin_last_time;\n" .
		"        \$now = microtime( true );\n" .
		"        if ( isset( \$api_plugin_last_time ) ) {\n" .
		"            \$api_plugin_load_times[\$plugin] = ( \$now - \$api_plugin_last_time ) * 1000;\n" .
		"        }\n" .
		"        \$api_plugin_last_time = \$now;\n" .
		"    }, 0 );\n" .
		"}\n";
		
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;
		
		if ( $wp_filesystem ) {
			$wp_filesystem->put_contents( $mu_dir . '/api-profiler-mu.php', $mu_plugin_code, FS_CHMOD_FILE );
		} else {
			@file_put_contents( $mu_dir . '/api-profiler-mu.php', $mu_plugin_code );
		}

		add_option( 'api_slow_query_threshold', 100 );
		add_option( 'api_enable_query_profiling', 1 );
		add_option( 'api_enable_hook_profiling', 1 );
		add_option( 'api_enable_memory_monitoring', 1 );

	}

}
