<?php
namespace barbsecurity;

require_once WP_PLUGIN_DIR . '/barbwire-security/barbwire-security.php';

/**
 * Class Version
 *
 * @package barbsecurity
 */
class Version {

	/**
	 * @var string
	 */
	public static $name = 'barbwire-security';

	/**
	 * @var string
	 */
	private $version = '';

	/**
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @return Version|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Version();
		}

		return self::$instance;
	}

	/**
	 * Version constructor.
	 */
	function __construct() {
		$plugin_meta   = get_file_data( WP_PLUGIN_DIR . '/barbwire-security/barbwire-security.php', array(
			'name'    => 'Plugin Name',
			'version' => 'Version',
		) );
		$this->version = $plugin_meta['version'];
	}

	/**
	 * @return string
	 */
	public static function get_version() {
		$instance = self::get_instance();

		return $instance->version;
	}
}
