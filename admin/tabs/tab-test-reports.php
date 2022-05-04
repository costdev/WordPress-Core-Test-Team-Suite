<?php
/**
 * The "Test Reports" tab.
 *
 * @since 0.0.1
 *
 * @package WPCoreTestTeamSuite\Admin\Tabs
 */

namespace WPCoreTestTeamSuite\Admin\Tabs;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPCoreTestTeamSuite\Modules\Report' ) ) {
	require_once wp_normalize_path( WP_TEST_TEAM_DIR . 'modules/class-report.php' );
}
?>

<div class="tab">
	<h2>Trac Test Report</h2>
	<ol>
		<li>Paste this into a comment on Trac.</li>
		<li>Add the steps you took and your results.</li>
		<li>Post!</li>
	</ol>
	<div class="card">
		<?php
			$report = new \WPCoreTestTeamSuite\Modules\Report();
			echo wp_kses_post( $report->get_test_report() );
		?>
	</div>
</div>
