=== Admin Performance Inspector ===
Contributors: llakshitmathur
Tags: performance, admin, profiling, memory, query
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Profiling tool for WordPress admin page performance. Analyze plugins, hooks, queries, and memory usage.

== Description ==

Admin Performance Inspector analyzes WordPress admin performance by profiling plugin load time, database queries, memory usage, and hook execution to help identify performance bottlenecks. It is designed specifically for developers and site administrators to gain granular visibility into what is slowing down the wp-admin area.

= Features =
* Profiling of wp-config.php SAVEQUERIES metrics.
* Monitoring of peak memory usage and limits.
* Hook execution profiling (init, admin_init, etc.).
* Automated approximation of exact plugin load times via an MU-plugin drop in.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/admin-performance-inspector` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to Tools -> Perf Inspector to view profiling metrics.
4. For query profiling, define SAVEQUERIES as true in wp-config.php. The plugin dashboard will provide an easy button to enable this automatically.

== Frequently Asked Questions ==

= Does this slow down the site? =
Profiling naturally introduces slight overhead. It is recommended for use in staging environments or for troubleshooting active admin slowness.

= How does it track plugin load times? =
It drops a tiny Must-Use (MU) plugin into the mu-plugins folder when activated to accurately time plugin_loaded actions for every active plugin.

== Screenshots ==

1. The Admin Performance Inspector dashboard outlining key metrics.

== Changelog ==

= 1.0.0 =
* Initial release.