<?php
/**
 * A class to generate a test report.
 *
 * @since 1.0.0
 *
 * @package WPCoreTestTeamSuite\Modules
 */

namespace WPCoreTestTeamSuite\Modules;

defined( 'ABSPATH' ) || exit;

/**
 * Generate a test report.
 *
 * @since 1.0.0
 */
class Report {
	/**
	 * The operating system.
	 *
	 * @var string $os
	 */
	private $os = '';

	/**
	 * The server.
	 *
	 * @var string $server
	 */
	private $server = '';

	/**
	 * The browser.
	 *
	 * @var string $browser
	 */
	private $browser = '';

	/**
	 * The current theme.
	 *
	 * @var string $theme
	 */
	private $theme = '';

	/**
	 * The active plugins.
	 *
	 * @var string $plugins
	 */
	private $plugins = '';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->get_os();
		$this->get_server();
		$this->get_browser();
		$this->get_theme();
		$this->get_plugins();
	}

	/**
	 * Generate and return the test report.
	 *
	 * @global $wp_version The WordPress version.
	 *
	 * @since 1.0.0
	 *
	 * @return string The test report.
	 */
	public function get_test_report() {
		global $wp_version;
		$report = <<<EOD
		== Test Report

		=== Environment
		* Server: $this->server
		* WordPress: $wp_version
		* Browser: $this->browser
		* OS: $this->os
		* Theme: $this->theme
		* Plugins: $this->plugins

		=== Steps
		1.&nbsp;

		=== Results
		1.&nbsp;
		EOD;

		return nl2br( $report );
	}

	/**
	 * Get the operating system name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The operating system name.
	 */
	private function get_os() {
		$this->os = __( 'Could not determine.' );

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return $this->os;
		}

		$agent   = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		$os_list = array(
			'/windows nt 10/i'      => 'Windows 10',
			'/windows nt 6.3/i'     => 'Windows 8.1',
			'/windows nt 6.2/i'     => 'Windows 8',
			'/windows nt 6.1/i'     => 'Windows 7',
			'/windows nt 6.0/i'     => 'Windows Vista',
			'/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'     => 'Windows XP',
			'/windows xp/i'         => 'Windows XP',
			'/windows nt 5.0/i'     => 'Windows 2000',
			'/windows me/i'         => 'Windows ME',
			'/win98/i'              => 'Windows 98',
			'/win95/i'              => 'Windows 95',
			'/win16/i'              => 'Windows 3.11',
			'/macintosh|mac os x/i' => 'Mac OS X',
			'/mac_powerpc/i'        => 'Mac OS 9',
			'/linux/i'              => 'Linux',
			'/ubuntu/i'             => 'Ubuntu',
			'/iphone/i'             => 'iPhone',
			'/ipod/i'               => 'iPod',
			'/ipad/i'               => 'iPad',
			'/android/i'            => 'Android',
			'/blackberry/i'         => 'BlackBerry',
			'/webos/i'              => 'Mobile',
		);

		foreach ( $os_list as $regex => $value ) {
			if ( preg_match( $regex, $agent ) ) {
				$this->os = $value;
			}
		}

		return $this->os;
	}

	/**
	 * Get details about the server.
	 *
	 * @since 1.0.0
	 *
	 * @return string Details about the server.
	 */
	private function get_server() {
		global $is_apache, $is_IIS, $is_iis7, $is_nginx;

		$this->server = __( 'Could not determine.' );
		$servers      = array(
			'Apache' => $is_apache,
			'NGINX'  => $is_nginx,
			'IIS'    => $is_IIS,
			'IIS7'   => $is_iis7,
		);
		$filtered     = array_filter( $servers );

		if ( empty( $filtered ) ) {
			return $this->server;
		}

		$server = array_keys( $filtered );

		$this->server = end( $server ) . ' (' . PHP_OS . ')';

		return $this->server;
	}

	/**
	 * Get details about the browser.
	 *
	 * @since 1.0.0
	 *
	 * @return string Details about the browser.
	 */
	private function get_browser() {
		global $is_lynx, $is_gecko, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_IE, $is_edge;

		$this->browser = __( 'Could not determine.' );
		$browsers      = array(
			'Lynx'              => $is_lynx,
			'Gecko'             => $is_gecko,
			'Opera'             => $is_opera,
			'Netscape 4'        => $is_NS4,
			'Safari'            => $is_safari,
			'Internet Explorer' => $is_IE,
			'Edge'              => $is_edge,
			'Chrome'            => $is_chrome,
		);
		$filtered      = array_filter( $browsers );

		if ( empty( $filtered ) ) {
			return $this->browser;
		}

		$browser       = array_keys( $filtered );
		$this->browser = end( $browser );

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return $this->browser;
		}

		$agent = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		preg_match( '/' . $this->browser . '\/([0-9\.\-]+)/', $agent, $version );

		$this->browser .= $version ? ' ' . $version[1] : '';
		$this->browser .= wp_is_mobile() ? ' (' . __( 'Mobile' ) . ')' : '';

		return $this->browser;
	}

	/**
	 * Get the current theme's name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The current theme's name.
	 */
	private function get_theme() {
		$this->theme = __( 'Could not determine.' );

		if ( ! wp_get_theme()->exists() ) {
			return $this->theme;
		}

		$this->theme = wp_get_theme()->name;

		return $this->theme;
	}

	/**
	 * Get the active plugins, excluding this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string The active plugins.
	 */
	private function get_plugins() {
		$this->plugins = __( 'None activated' );
		$plugin_files  = get_option( 'active_plugins' );

		if ( ! $plugin_files || 1 >= count( $plugin_files ) ) {
			return $this->plugins;
		}

		foreach ( $plugin_files as $k => &$plugin ) {
			$path    = WP_PLUGIN_DIR . '/' . $plugin;
			$data    = get_plugin_data( $path );
			$name    = $data['Name'];
			$version = $data['Version'];

			// Exclude this plugin.
			if ( 'WordPress Core Test Team Suite' === $name ) {
				unset( $plugin_files[ $k ] );
			}

			$plugin = "&nbsp;&nbsp;* $name $version";
		}
		unset( $plugin );

		$this->plugins = "\n" . implode( "\n", $plugin_files );

		return $this->plugins;
	}
}
