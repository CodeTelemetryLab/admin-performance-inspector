<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Performance_Inspector_Deactivator {
	public static function deactivate() {
		$mu_file = WP_CONTENT_DIR . '/mu-plugins/api-profiler-mu.php';
		if ( file_exists( $mu_file ) ) {
			wp_delete_file( $mu_file );
		}
	}
}
