<?php
/**
 * A class to manage the settings screen.
 *
 * @since 1.0.0
 *
 * @package WPCoreTestTeamSuite\Admin
 */

namespace WPCoreTestTeamSuite\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Display the settings screen.
 *
 * @since 1.0.0
 */
class Settings {
	/**
	 * The default tabs.
	 *
	 * @since 1.0.0
	 *
	 * @var array $tabs
	 */
	private $tabs = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$default_tabs = array(
			'test-reports' => __( 'Test Reports' ),
		);

		/**
		 * Filter the settings tabs.
		 *
		 * Make sure that you create `admin/tabs/tab-{slug}.php` for each new tab.
		 *
		 * @since 1.0.0
		 *
		 * @param array $default_tabs The default tabs in `'slug' => 'Label'` format.
		 */
		$tabs = apply_filters( 'wp_test_team_settings_tabs', $default_tabs );

		// Enforce nice things.
		if ( ! is_array( $tabs ) ) {
			$this->tabs = $default_tabs;
			return;
		}

		// Enforce nice things times `count( $tabs )`.
		foreach ( $tabs as $k => $tab ) {
			if ( 0 < validate_file( $tab ) ) {
				unset( $tabs[ $k ] );
			}
		}

		// Enforce nice things times 10**9.
		$this->tabs = is_array( $tabs ) ? $tabs : $default_tabs;
	}

	/**
	 * Get the current tab.
	 *
	 * @since 1.0.0
	 *
	 * @return string The current tab.
	 */
	public function get_current_tab() {
		$current_tab = array_key_first( $this->tabs );

		if ( ! empty( $_GET['tab'] ) ) {
			$tab = sanitize_key( wp_unslash( $_GET['tab'] ) );

			if ( in_array( $tab, array_keys( $this->tabs ), true ) ) {
				$current_tab = $tab;
			}
		}

		return $current_tab;
	}

	/**
	 * Wrap a tab's content.
	 *
	 * @since 1.0.0
	 */
	public function display_current_tab() {
		$default_wrapper_start = '<div class="wrap"><h1>WordPress Core Test Team Suite</h1>';
		$default_wrapper_end   = '</div>';

		/**
		 * Filter the start of the wrapper.
		 *
		 * @since 1.0.0
		 *
		 * @param string $default_wrapper_start The default start of the wrapper.
		 *                                      Default: `<div class="wrap">`.
		 */
		$custom_wrapper_start = apply_filters( 'wp_test_team_tab_wrapper_start', $default_wrapper_start );

		/**
		 * Filter the end of the wrapper.
		 *
		 * @since 1.0.0
		 *
		 * @param string $default_wrapper_end The default end of the wrapper.
		 *                                    Default: `</div>`.
		 */
		$custom_wrapper_end = apply_filters( 'wp_test_team_tab_wrapper_end', $default_wrapper_end );

		// Enforce nice things.
		$wrapper_start = is_string( $custom_wrapper_start ) ? $custom_wrapper_start : $default_wrapper_start;
		$wrapper_end   = is_string( $custom_wrapper_end ) ? $custom_wrapper_end : $default_wrapper_end;

		echo wp_kses_post( $wrapper_start . $this->get_tabbed_navigation() );

		$current_tab = $this->get_current_tab();
		include_once wp_normalize_path( WP_TEST_TEAM_DIR . "admin/tabs/tab-$current_tab.php" );

		echo wp_kses_post( $wrapper_end );
	}

	/**
	 * Get tabbed navigation.
	 *
	 * @since 1.0.0
	 *
	 * @return string The tabbed navigation.
	 */
	public function get_tabbed_navigation() {
		$default_wrapper_start = '<nav class="nav-tab-wrapper">';
		$default_wrapper_end   = '</nav>';

		/**
		 * Filter the start of the tabbed navigation wrapper.
		 *
		 * @since 1.0.0
		 *
		 * @param string $default_wrapper_start The default start of the tabbed navigation wrapper.
		 *                                      Default: `<nav class="nav-tab-wrapper">`.
		 */
		$custom_wrapper_start = apply_filters( 'wp_test_team_tab_nav_wrapper_start', $default_wrapper_start );

		/**
		 * Filter the end of the tabbed navigation wrapper.
		 *
		 * @since 1.0.0
		 *
		 * @param string $default_wrapper_end The default end of the tabbed navigation wrapper.
		 *                                    Default: `</nav>`.
		 */
		$custom_wrapper_end = apply_filters( 'wp_test_team_tab_nav_wrapper_end', $default_wrapper_end );

		// Enforce nice things.
		$wrapper_start = is_string( $custom_wrapper_start ) ? $custom_wrapper_start : $default_wrapper_start;
		$wrapper_end   = is_string( $custom_wrapper_end ) ? $custom_wrapper_end : $default_wrapper_end;

		$content = '';

		foreach ( $this->tabs as $slug => $tab ) {
			$url = add_query_arg(
				array(
					'page' => 'wp-core-test-team-suite',
					'tab'  => $slug,
				)
			);

			$is_current      = $this->get_current_tab() === $slug;
			$default_classes = array( 'nav-tab' );

			/**
			 * Filter the classes for navigation tabs.
			 *
			 * @since 1.0.0
			 *
			 * @param array  $default_classes The default navigation tab classes.
			 *                                Default: `array( 'nav-tab' )`
			 * @param string $slug            The tab's slug.
			 * @param string $tab             The tab's label.
			 * @param bool   $is_current      Whether the tab is the current tab.
			 */
			$classes = apply_filters( 'wp_test_team_tab_classes', $default_classes, $slug, $tab, $is_current );

			// Enforce nice things.
			$classes = is_array( $classes ) ? $classes : $default_classes;

			if ( $is_current ) {
				$default_current_class = 'nav-tab-active';

				/**
				 * Filter the class used to indicate the current class.
				 *
				 * @since 1.0.0
				 *
				 * @param string $default_current_class The default current class.
				 *                                      Default: 'nav-tab-active'.
				 */
				$current_class = apply_filters( 'wp_test_team_current_tab_class', $default_current_class );

				// Enforce nice things.
				$classes[] = is_string( $current_class ) ? $current_class : $default_current_class;
			}

			$content .= '<a href="' . $url . '" class="' . implode( ' ', $classes ) . '">' . $tab . '</a>';
		}

		return $wrapper_start . $content . $wrapper_end;
	}

}
