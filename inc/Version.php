<?php
namespace barbsecurity;

require_once WP_PLUGIN_DIR . '/barbwire-security/barbwire-security.php';

class Version {

	public static $name = 'barbwire-security';
	private $version = '';
	private static $instance = null;

	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new Version();
		}

		return self::$instance;
	}

	function __construct() {
		$plugin_meta   = get_file_data( WP_PLUGIN_DIR . '/barbwire-security/barbwire-security.php', array(
			'name'    => 'Plugin Name',
			'version' => 'Version',
		) );
		$this->version = $plugin_meta['version'];
	}

	public static function getVersion() {
		$instance = self::get_instance();

		return $instance->version;
	}
}
