<?php
/**
 * Plugin Name: WordPress Core Test Team Suite
 * Description: A collection of tools for use by the WordPress Core Test Team.
 * Author:      WordPress Test Team
 * Author URI:  https://make.wordpress.org/test/
 * License:     GPLv2 or later
 * Version:     0.0.1
 *
 * @package WPCoreTestTeamSuite
 */

namespace WPCoreTestTeamSuite;

defined( 'ABSPATH' ) || exit;

define( 'WP_TEST_TEAM_DIR', plugin_dir_path( __FILE__ ) );

add_action(
	'admin_menu',
	static function() {
		add_submenu_page(
			'tools.php',
			'Test Team Suite',
			'Test Team Suite',
			'manage_options',
			'wp-core-test-team-suite',
			'WPCoreTestTeamSuite\init'
		);
	}
);

if ( ! function_exists( 'WPCoreTestTeamSuite\init' ) ) {
	/**
	 * Initialize the settings screen.
	 *
	 * @since 0.0.1
	 */
	function init() {
		if ( ! class_exists( 'WPCoreTestTeamSuite\Admin\Settings' ) ) {
			require_once wp_normalize_path( WP_TEST_TEAM_DIR . 'admin/class-settings.php' );
		}

		$settings = new \WPCoreTestTeamSuite\Admin\Settings();
		$settings->display_current_tab();
	}
}
