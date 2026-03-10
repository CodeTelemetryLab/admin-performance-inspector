<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Performance_Inspector_Admin {

	private $plugin_name;
	private $version;
	private $profiler;

	public function __construct( $plugin_name, $version, $profiler = null ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->profiler = $profiler;
	}

	public function enqueue_styles( $hook ) {
		if ( 'tools_page_admin-performance-inspector' !== $hook ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin-performance-inspector-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts( $hook ) {
		if ( 'tools_page_admin-performance-inspector' !== $hook ) {
			return;
		}
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin-performance-inspector-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function add_plugin_admin_menu() {
		add_management_page(
			__( 'Admin Performance Inspector', 'admin-performance-inspector' ),
			__( 'Perf Inspector', 'admin-performance-inspector' ),
			'manage_options',
			'admin-performance-inspector',
			array( $this, 'display_plugin_setup_page' )
		);
	}

	public function display_plugin_setup_page() {
		$admin_performance_inspector_total_time = ( microtime( true ) - (defined('API_START_TIME') ? API_START_TIME : microtime(true)) ) * 1000;
		$profiler = $this->profiler;
		require_once plugin_dir_path( __FILE__ ) . 'partials/admin-performance-inspector-admin-display.php';
	}

	public function handle_actions() {
		if ( isset( $_POST['api_action'] ) && isset( $_POST['api_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['api_nonce'] ) ), 'api_dashboard_action' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) wp_die();
			
			$action = sanitize_text_field( wp_unslash( $_POST['api_action'] ) );
			$wp_config_path = ABSPATH . 'wp-config.php';
			if ( ! file_exists( $wp_config_path ) ) {
				$wp_config_path = dirname( ABSPATH ) . '/wp-config.php';
			}

			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;

			if ( $action === 'enable_savequeries' ) {
				if ( $wp_filesystem && $wp_filesystem->is_writable( $wp_config_path ) ) {
					$config = $wp_filesystem->get_contents( $wp_config_path );
					if ( strpos( $config, 'SAVEQUERIES' ) === false ) {
						$insert = "define( 'SAVEQUERIES', true ); // Added by Admin Performance Inspector\n";
						$config = preg_replace( '/(\/\* That\'s all, stop editing)/', $insert . "$1", $config );
						$wp_filesystem->put_contents( $wp_config_path, $config, FS_CHMOD_FILE );
					}
				}
			} elseif ( $action === 'disable_savequeries' ) {
				if ( $wp_filesystem && $wp_filesystem->is_writable( $wp_config_path ) ) {
					$config = $wp_filesystem->get_contents( $wp_config_path );
					$config = preg_replace( '/define\(\s*[\'"]SAVEQUERIES[\'"]\s*,\s*true\s*\);\s*\/\/\s*Added by Admin Performance Inspector\n?/i', '', $config );
					$wp_filesystem->put_contents( $wp_config_path, $config, FS_CHMOD_FILE );
				}
			}
			wp_safe_redirect( admin_url( 'tools.php?page=admin-performance-inspector' ) );
			exit;
		}
	}
}
