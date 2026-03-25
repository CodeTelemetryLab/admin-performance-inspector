<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admipein_Profiler {
	public $hook_times = array();
	public $plugin_times = array();
	public $query_data = array();
	public $memory_data = array();
	private $hook_starts = array();

	public function __construct() {
		$hooks = array( 'init', 'admin_init', 'admin_menu', 'current_screen', 'wp_loaded', 'plugins_loaded' );
		foreach ( $hooks as $hook ) {
			add_action( $hook, array( $this, 'start_hook_timer' ), -99999 );
			add_action( $hook, array( $this, 'stop_hook_timer' ), 99999 );
		}
		add_action( 'admin_init', array( $this, 'profile_plugins' ), 0 );
	}

	public function start_hook_timer() {
		$hook = current_action();
		$this->hook_starts[$hook] = microtime( true );
	}

	public function stop_hook_timer() {
		$hook = current_action();
		if ( isset( $this->hook_starts[$hook] ) ) {
			$duration = ( microtime( true ) - $this->hook_starts[$hook] ) * 1000;
			global $wp_filter;
			$callbacks = 0;
			if ( isset( $wp_filter[$hook] ) ) {
				foreach ( $wp_filter[$hook] as $priority ) {
					if ( is_array( $priority ) || $priority instanceof Countable ) {
						$callbacks += count( $priority );
					}
				}
			}
			$this->hook_times[$hook] = array(
				'time' => $duration,
				'callbacks' => $callbacks
			);
		}
	}

	public function profile_plugins() {
		global $admipein_plugin_load_times;
		if ( ! empty( $admipein_plugin_load_times ) ) {
			foreach ( $admipein_plugin_load_times as $plugin => $time ) {
				$this->plugin_times[$plugin] = array(
					'name' => dirname( plugin_basename( $plugin ) ),
					'time' => $time
				);
			}
		} else {
			$active_plugins = get_option( 'active_plugins', array() );
			foreach ( $active_plugins as $plugin ) {
				$this->plugin_times[$plugin] = array(
					'name' => dirname( $plugin ),
					'time' => 0.01
				);
			}
		}
	}

	public function get_memory_stats() {
		return array(
			'current' => memory_get_usage(),
			'peak'    => memory_get_peak_usage(),
			'limit'   => ini_get( 'memory_limit' )
		);
	}

	public function get_query_stats() {
		global $wpdb;
		$stats = array(
			'total' => 0,
			'slow' => 0,
			'time' => 0,
			'queries' => array()
		);
		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES && ! empty( $wpdb->queries ) ) {
			$threshold = get_option( 'admipein_slow_query_threshold', 100 ) / 1000;
			foreach ( $wpdb->queries as $q ) {
				$time = $q[1];
				$stats['total']++;
				$stats['time'] += $time;
				$is_slow = $time > $threshold;
				if ( $is_slow ) {
					$stats['slow']++;
				}
				$stats['queries'][] = array(
					'sql'    => $q[0],
					'time'   => $time * 1000,
					'origin' => $q[2],
					'slow'   => $is_slow
				);
			}
		}
		return $stats;
	}
}
