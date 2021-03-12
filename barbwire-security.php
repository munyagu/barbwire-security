<?php

/*
Plugin Name: Barbwire Security
Plugin URI: http://barbwire.co.jp/plugin/barb-pack
Description: This plugin enhances the WordPress security.
Author: barbwire.co.jp
Requires at least: 4.6
Tested up to: 5.7
Requires PHP: 5.6
Version: 2.1.0
Author URI: http://barbwire.co.jp/
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: barbwire-security
 */

require_once dirname( __FILE__ ) . '/inc/functions.php';
require_once dirname( __FILE__ ) . '/inc/Version.php';

use barbsecurity\Version as Version;

class BarbwireSecurity {

	const REST_NAMESPACE = 'barbwire_security/';

	const REST_CURRENT_VERSION = 'v1';

	/**
	 * Default option values
	 *
	 * @var array
	 */
	private static $default_option_value = array(
		'captcha_enable' => false,
		'recaptcha_site_key' => '',
		'recaptcha_secret_key' => '',
		'parameter_enable'         => false,
		'param_name'               => '',
		'param_value'              => '',
		'block_author_archive'     => false,
		'pingback_suppress_enable' => false,
		'disable_rest_api'         => 0,
		'installed_end_point'      => array(),
		'end_points'               => array(),
	);

	/**
	 * Ini file settings
	 *
	 * @var null
	 */
	private static $ini = null;

	/**
	 * Get option setting from Database
	 *
	 * @return mixed options
	 */
	public static function get_option() {
		return wp_parse_args( get_option( Version::$name ), self::$default_option_value );
	}

	/**
	 * Update option setting
	 *
	 * @param mixed $options settings.
	 */
	public static function update_option( $options ) {
		update_option( Version::$name, $options );
	}

	/**
	 * Activation hook
	 */
	public static function barb_security_register_activation_hook() {

	}

	/**
	 * Deactivation hook
	 */
	public static function barb_security_register_deactivation_hook() {

	}

	/**
	 * Uninstall hook
	 */
	public static function barb_security_uninstall() {
		delete_option( Version::$name );
	}

	/**
	 * Read ini file
	 *
	 * @return array ini file settings
	 */
	public static function get_ini() {
		if ( null === self::$ini ) {
			self::$ini = parse_ini_file( dirname( __FILE__ ) . '/barbwire_security.ini', true );
		}

		return self::$ini;
	}


	/**
	 * Add setting link to plugin list page
	 *
	 * @param array  $actions     An array of plugin action links.
	 *
	 * @return mixed
	 */
	public static function barb_security_plugin_action_links( $actions ) {

		$settings_link = '<a href="' . site_url() . '/wp-admin/options-general.php?page=barb_secure_settings">' . __( 'Settings', 'barbwire-security' ) . '</a>';
		array_unshift( $actions, $settings_link );

		return $actions;
	}

	public static function log( $msg ){
		$log_file = self::get_log_file_path();
		error_log( sprintf( "[%s] %s", date( 'Ymd H:i:s' ), $msg ), 3, $log_file );
	}

	public static function get_log_file_path(){
		$date = date('Ymd');
		return __DIR__ . "/log/security.{$date}.log";
	}
}

register_activation_hook( __FILE__, array( 'BarbwireSecurity', 'barb_security_register_activation_hook' ) );
register_deactivation_hook( __FILE__, array( 'BarbwireSecurity', 'barb_security_register_deactivation_hook' ) );
register_uninstall_hook( __FILE__, array( 'BarbwireSecurity', 'barb_security_uninstall' ) );
add_filter('plugin_action_links_'.plugin_basename(__FILE__) , array( 'BarbwireSecurity', 'barb_security_plugin_action_links'), 10, 2);
