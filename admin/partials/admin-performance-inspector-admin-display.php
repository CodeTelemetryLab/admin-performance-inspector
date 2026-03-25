<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$admipein_mem_stats = $profiler->get_memory_stats();
$admipein_query_stats = $profiler->get_query_stats();
$admipein_hook_times = $profiler->hook_times;
$admipein_plugin_times = $profiler->plugin_times;

$admipein_mem_limit = wp_convert_hr_to_bytes( $admipein_mem_stats['limit'] );
$admipein_mem_usage_pct = $admipein_mem_limit > 0 ? ( $admipein_mem_stats['current'] / $admipein_mem_limit ) * 100 : 0;

if ( ! function_exists( 'admipein_format_bytes' ) ) {
	function admipein_format_bytes( $bytes ) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		$i = 0;
		while ( $bytes >= 1024 && $i < count( $units ) - 1 ) {
			$bytes /= 1024;
			$i++;
		}
		return round( $bytes, 2 ) . ' ' . $units[$i];
	}
}

$admipein_total_color = $admipein_total_time > 1000 ? 'api-red' : ( $admipein_total_time > 500 ? 'api-yellow' : 'api-green' );
?>

<div class="wrap admin-performance-inspector-wrap">
	
	<?php if ( ! defined( 'SAVEQUERIES' ) || ! SAVEQUERIES ) : ?>
		<div class="notice notice-warning is-dismissible" style="display:flex; align-items:center; justify-content: space-between; padding: 10px 15px; margin-bottom: 20px;">
			<p style="margin:0;"><strong><?php esc_html_e( 'Query Profiling is disabled:', 'admin-performance-inspector' ); ?></strong> <?php esc_html_e( 'SAVEQUERIES is not enabled in wp-config.php.', 'admin-performance-inspector' ); ?></p>
			<form method="post" action="" style="margin:0;">
				<?php wp_nonce_field( 'admipein_dashboard_action', 'admipein_nonce', false ); ?>
				<input type="hidden" name="admipein_action" value="enable_savequeries">
				<button type="submit" class="button button-primary button-small"><?php esc_html_e( 'Enable Automatically', 'admin-performance-inspector' ); ?></button>
			</form>
		</div>
	<?php endif; ?>

	<div class="api-header" style="align-items: center; display: flex;">
		<div class="api-logo" style="margin-right: 15px; display: flex; align-items: center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <path d="M3 9h18"></path>
                <path d="M9 21V9"></path>
                <path d="m14 14 3 3"></path>
                <path d="m17 14-3 3"></path>
            </svg>
		</div>
		<h1 style="margin: 0; padding: 0; line-height: 1;"><?php esc_html_e( 'Admin Performance Inspector', 'admin-performance-inspector' ); ?></h1>
	</div>
	
	<div class="api-dashboard">
		<div class="api-section api-overview">
			<h2><?php esc_html_e( 'Performance Overview', 'admin-performance-inspector' ); ?></h2>
			<div class="api-metrics">
				<div class="api-metric">
					<strong><?php esc_html_e( 'Total Page Load Time', 'admin-performance-inspector' ); ?></strong>
					<span class="<?php echo esc_attr( $admipein_total_color ); ?>">
						<?php echo esc_html( round( $admipein_total_time, 2 ) ); ?> ms
					</span>
				</div>
				<div class="api-metric">
					<strong><?php esc_html_e( 'Database Queries', 'admin-performance-inspector' ); ?></strong>
					<span class="api-blue"><?php echo esc_html( $admipein_query_stats['total'] ); ?></span>
				</div>
				<div class="api-metric">
					<strong><?php esc_html_e( 'Memory Usage', 'admin-performance-inspector' ); ?></strong>
					<span class="api-blue">
						<?php echo esc_html( admipein_format_bytes( $admipein_mem_stats['current'] ) ); ?> / <?php echo esc_html( $admipein_mem_stats['limit'] ); ?> 
					</span>
					<div style="font-size: 11px; color: #777; margin-top: -5px; margin-bottom: 5px;">(<?php echo esc_html( round( $admipein_mem_usage_pct, 1 ) ); ?>%)</div>
					<div class="api-progress-bar"><div class="api-progress-inner" style="width: <?php echo esc_attr( $admipein_mem_usage_pct ); ?>%"></div></div>
				</div>
				<div class="api-metric">
					<strong><?php esc_html_e( 'Peak Memory', 'admin-performance-inspector' ); ?></strong>
					<span class="api-blue"><?php echo esc_html( admipein_format_bytes( $admipein_mem_stats['peak'] ) ); ?></span>
				</div>
			</div>
		</div>

		<div class="api-section">
			<h2><?php esc_html_e( 'Hook Execution Profiling', 'admin-performance-inspector' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Hook Name', 'admin-performance-inspector' ); ?></th>
						<th style="width: 150px;"><?php esc_html_e( 'Execution Time (ms)', 'admin-performance-inspector' ); ?></th>
						<th style="width: 150px;"><?php esc_html_e( 'Registered Callbacks', 'admin-performance-inspector' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $admipein_hook_times as $admipein_hook => $admipein_data ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $admipein_hook ); ?></strong></td>
							<td><?php echo esc_html( round( $admipein_data['time'], 2 ) ); ?></td>
							<td><?php echo esc_html( $admipein_data['callbacks'] ); ?></td>
						</tr>
					<?php endforeach; ?>
					<?php if ( empty( $admipein_hook_times ) ) : ?>
						<tr><td colspan="3"><?php esc_html_e( 'No hook data captured.', 'admin-performance-inspector' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="api-section">
			<h2>
                <?php esc_html_e( 'Plugin Load Performance', 'admin-performance-inspector' ); ?>
                <?php if ( empty( $GLOBALS['api_plugin_load_times'] ) ) : ?>
                    <span class="api-badge api-badge-slow" style="margin-left: 10px; font-size: 11px;"><?php esc_html_e( 'MU Profiler Missing - Please reactivate plugin', 'admin-performance-inspector'); ?></span>
                <?php endif; ?>
            </h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Plugin Directory', 'admin-performance-inspector' ); ?></th>
						<th style="width: 150px;"><?php esc_html_e( 'Load Time (ms)', 'admin-performance-inspector' ); ?></th>
						<th style="width: 100px;"><?php esc_html_e( 'Status', 'admin-performance-inspector' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $admipein_plugin_times as $admipein_slug => $admipein_data ) : 
						$admipein_is_slow = $admipein_data['time'] > 50;
					?>
						<tr>
							<td><strong><?php echo esc_html( $admipein_data['name'] ); ?></strong></td>
							<td><?php echo esc_html( round( $admipein_data['time'], 2 ) ); ?></td>
							<td>
								<?php if ( $admipein_is_slow ) : ?>
									<span class="api-badge api-badge-slow"><?php esc_html_e( 'Slow', 'admin-performance-inspector' ); ?></span>
								<?php else : ?>
									<span class="api-badge api-badge-normal"><?php esc_html_e( 'Normal', 'admin-performance-inspector' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					<?php if ( empty( $admipein_plugin_times ) ) : ?>
						<tr><td colspan="3"><?php esc_html_e( 'No plugin data captured.', 'admin-performance-inspector' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="api-section">
			<div style="display:flex; justify-content:space-between; align-items:center;">
				<h2 style="border:none; margin:0; padding:0;"><?php esc_html_e( 'Database Queries', 'admin-performance-inspector' ); ?></h2>
				
				<?php if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) : ?>
					<form method="post" action="">
						<?php wp_nonce_field( 'admipein_dashboard_action', 'admipein_nonce', false ); ?>
						<input type="hidden" name="admipein_action" value="disable_savequeries">
						<button type="submit" class="button"><?php esc_html_e( 'Disable Query Profiling', 'admin-performance-inspector' ); ?></button>
					</form>
				<?php endif; ?>
			</div>
			
			<div style="margin-top:20px; border-top: 1px solid #f0f0f1; padding-top:20px;">
			<?php if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) : ?>
				
				<?php
				$admipein_total_queries = $admipein_query_stats['total'];
				$admipein_slow_queries = $admipein_query_stats['slow'];
				$admipein_per_page = 20;
				$admipein_total_pages = ceil( $admipein_total_queries / $admipein_per_page );
				?>

				<!-- WP Core-Style List Filters -->
				<ul class="subsubsub api-query-filters">
					<li class="all"><a href="#" class="current" data-filter="all"><?php esc_html_e( 'All', 'admin-performance-inspector' ); ?> <span class="count">(<span class="api-filter-count-all"><?php echo esc_html( $admipein_total_queries ); ?></span>)</span></a> |</li>
					<li class="slow"><a href="#" data-filter="slow"><?php esc_html_e( 'Slow', 'admin-performance-inspector' ); ?> <span class="count">(<span class="api-filter-count-slow"><?php echo esc_html( $admipein_slow_queries ); ?></span>)</span></a></li>
				</ul>

				<!-- WP Core-Style Pagination -->
				<div class="tablenav top api-query-tablenav">
					<div class="alignleft actions">
						<span style="display: inline-block; padding-top: 5px;">
							<?php esc_html_e( 'Total Query Time:', 'admin-performance-inspector' ); ?> <strong><?php echo esc_html( round( $admipein_query_stats['time'] * 1000, 2 ) ); ?> ms</strong>
						</span>
					</div>
					<div class="tablenav-pages <?php echo $admipein_total_pages <= 1 ? 'one-page' : ''; ?>">
						<span class="displaying-num"><span class="api-displaying-num-val"><?php echo esc_html( $admipein_total_queries ); ?></span> <?php esc_html_e( 'items', 'admin-performance-inspector' ); ?></span>
						<span class="pagination-links">
							<a class="first-page button disabled" href="#" title="Go to the first page"><span>&laquo;</span></a>
							<a class="prev-page button disabled" href="#" title="Go to the previous page"><span>&lsaquo;</span></a>
							<span class="paging-input">
								<label for="current-page-selector" class="screen-reader-text">Current Page</label>
								<input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging">
								<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo esc_html( $admipein_total_pages ); ?></span></span>
							</span>
							<a class="next-page button <?php echo $admipein_total_pages <= 1 ? 'disabled' : ''; ?>" href="#" title="Go to the next page"><span>&rsaquo;</span></a>
							<a class="last-page button <?php echo $admipein_total_pages <= 1 ? 'disabled' : ''; ?>" href="#" title="Go to the last page"><span>&raquo;</span></a>
						</span>
					</div>
				</div>

				<table class="wp-list-table widefat fixed striped api-queries-table">
					<thead>
						<tr>
							<th style="width: 50px;">#</th>
							<th><?php esc_html_e( 'Query SQL', 'admin-performance-inspector' ); ?></th>
							<th style="width: 100px;"><?php esc_html_e( 'Time (ms)', 'admin-performance-inspector' ); ?></th>
							<th><?php esc_html_e( 'Origin', 'admin-performance-inspector' ); ?></th>
							<th style="width: 80px;"><?php esc_html_e( 'Status', 'admin-performance-inspector' ); ?></th>
						</tr>
					</thead>
					<tbody id="api-query-tbody" data-per-page="<?php echo esc_attr( $admipein_per_page ); ?>">
						<?php if ( ! empty( $admipein_query_stats['queries'] ) ) : ?>
							<?php foreach ( $admipein_query_stats['queries'] as $admipein_index => $admipein_q ) : ?>
								<tr class="api-query-row <?php echo $admipein_q['slow'] ? 'api-row-slow is-slow' : 'is-normal'; ?>" data-index="<?php echo esc_attr( $admipein_index ); ?>">
									<td><?php echo esc_html( $admipein_index + 1 ); ?></td>
									<td><textarea readonly style="width:100%; height: 50px; font-family: monospace; font-size: 12px; background: transparent; border: 1px solid #ddd; padding: 5px;"><?php echo esc_html( $admipein_q['sql'] ); ?></textarea></td>
									<td><?php echo esc_html( round( $admipein_q['time'], 2 ) ); ?></td>
									<td><small style="word-break: break-all;"><?php echo esc_html( $admipein_q['origin'] ); ?></small></td>
									<td>
										<?php if ( $admipein_q['slow'] ) : ?>
											<span class="api-badge api-badge-slow"><?php esc_html_e( 'Slow', 'admin-performance-inspector' ); ?></span>
										<?php else : ?>
											<span class="api-badge api-badge-normal"><?php esc_html_e( 'Normal', 'admin-performance-inspector' ); ?></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr><td colspan="5"><?php esc_html_e( 'No queries found.', 'admin-performance-inspector' ); ?></td></tr>
						<?php endif; ?>
					</tbody>
				</table>

				<div class="tablenav bottom api-query-tablenav">
					<div class="tablenav-pages <?php echo $admipein_total_pages <= 1 ? 'one-page' : ''; ?>">
						<span class="displaying-num"><span class="api-displaying-num-val"><?php echo esc_html( $admipein_total_queries ); ?></span> <?php esc_html_e( 'items', 'admin-performance-inspector' ); ?></span>
						<span class="pagination-links">
							<a class="first-page button disabled" href="#" title="Go to the first page"><span>&laquo;</span></a>
							<a class="prev-page button disabled" href="#" title="Go to the previous page"><span>&lsaquo;</span></a>
							<span class="paging-input">
								<span class="tablenav-paging-text"><span class="current-page-display">1</span> of <span class="total-pages"><?php echo esc_html( $admipein_total_pages ); ?></span></span>
							</span>
							<a class="next-page button <?php echo $admipein_total_pages <= 1 ? 'disabled' : ''; ?>" href="#" title="Go to the next page"><span>&rsaquo;</span></a>
							<a class="last-page button <?php echo $admipein_total_pages <= 1 ? 'disabled' : ''; ?>" href="#" title="Go to the last page"><span>&raquo;</span></a>
						</span>
					</div>
				</div>

			<?php endif; ?>
			</div>
		</div>

	</div>
</div>
