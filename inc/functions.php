<?php
require_once dirname( __FILE__ ) . '/Version.php';
require_once WP_PLUGIN_DIR . '/barbwire-security/admin/settings.php';
require_once dirname( __FILE__ ) . '/DisableXMLRPCPingBack.php';

require_once dirname( __FILE__ ) . '/LoginParameter.php';
require_once dirname( __FILE__ ) . '/AuthorArchive.php';
require_once dirname( __FILE__ ) . '/DisableRESTAPI.php';
require_once dirname( __FILE__ ) . '/ReCaptcha.php';

use barbsecurity\Version as Version;
use barbsecurity\LoginParameter as LoginParameter;
use barbsecurity\AuthorArchive as AuthorArchive;
use barbsecurity\DisableRESTAPI as DisableRESTAPI;
use barbsecurity\DisableXMLRPCPingBack as DisableXMLRPCPingBack;
use barbsecurity\ReCaptcha as ReCaptcha;

define( 'BARB_SECURITY_AUTHORITYSECURE', 'manage_options' );    //User level required in order to change the settings.
define( 'BARB_SECURITY_SAVE_TRANSIENT', Version::$name . "_SAVE" );
define( 'BARB_SECURITY_OPTION_TRANSIENT', Version::$name . "_OPTION" );

$version               = Version::get_version();
$barb_security_options = BarbwireSecurity::get_option();
function barb_security_plugins_loaded() {
	$result = load_plugin_textdomain( Version::$name );
}

add_action( 'plugins_loaded', 'barb_security_plugins_loaded' );



/*
 * ADMIN LOGIN PAGE URL PARAMETER
 */
// If enable ADMIN LOGIN PAGE URL PARAMETER, initialize activate it.
if ( isset( $barb_security_options['parameter_enable'] ) && $barb_security_options['parameter_enable'] == true ) {
	LoginParameter::activate();
}

/*
 * BLOCK SHOW AUTHOR ARCHIVE
 */
if ( isset( $barb_security_options['block_author_archive'] ) && $barb_security_options['block_author_archive'] == true ) {
	AuthorArchive::activete();
}

/*************************************
 * DISABLE XMLRCP PINGBACK
 *************************************/
if ( isset( $barb_security_options['pingback_suppress_enable'] ) && $barb_security_options['pingback_suppress_enable'] == true ) {
	DisableXMLRPCPingBack::activate();
}

/*************************************
 * DISABLE REST API
 *************************************/
// specified end point.
if( function_exists( 'rest_get_url_prefix') ) {
	$barb_security_rest_prefix = rest_get_url_prefix();

	$barb_security_rest_installed_end_point = false;
// Check installed end point.
	if ( isset( $barb_security_options['installed_end_point'] ) && is_array( $barb_security_options['installed_end_point'] ) ) {
		foreach ( $barb_security_options['installed_end_point'] as $installed_end_point ) {
			$barb_security_installed_end_point_uri = '/' . $barb_security_rest_prefix . '/' . $installed_end_point;
			if ( strpos( $_SERVER['REQUEST_URI'], $barb_security_installed_end_point_uri ) === 0 ) {
				$barb_security_rest_installed_end_point = true;
				break;
			}
		}
	}

// Check specified end point.
	$barb_security_rest_specified_end_point = false;
	if ( isset( $barb_security_options['end_points'] ) && is_array( $barb_security_options['end_points'] ) ) {
		foreach ( $barb_security_options['end_points'] as $end_points ) {
			$barb_security_end_point_uri = '/' . $barb_security_rest_prefix . '/' . $end_points;

			if ( strpos( $_SERVER['REQUEST_URI'], $barb_security_end_point_uri ) === 0 ) {
				$barb_security_rest_specified_end_point = true;
				break;
			}
		}
	}


	if ( isset( $barb_security_options['disable_rest_api'] )
	     && $barb_security_options['disable_rest_api'] == 2
	     && ! ( $barb_security_rest_installed_end_point || $barb_security_rest_specified_end_point ) ) {
		DisableRESTAPI::activate();
	}

	if ( isset( $barb_security_options['disable_rest_api'] )
	     && $barb_security_options['disable_rest_api'] == 1
	     && ! ( $barb_security_rest_installed_end_point || $barb_security_rest_specified_end_point ) ) {
		DisableRESTAPI::activate_auth();
	}
}

/*************************************
 * Activate Google reCaptcha V3
 *************************************/
function barb_security_recaptcha_init(){
	ReCaptcha::init();
}
add_action('init', 'barb_security_recaptcha_init');

/*************** OTHER ***************/

/**
 * Exit error 403
 */
function exit_403() {
	echo '<html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1>' . __( 'Failed to login.', 'barbwire-security' ) . '</body></html>';
	status_header( 403 );
	exit();
}

/**
 * Exit error 404
 */
function exit_404() {

	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	$template404 = get_query_template( '404' );
	if ( $template404 ) {
		include( $template404 );
	} else {
		echo '<html><head><title>404</title></head><body><h1>Not Found</h1>' . "The requested URL {$_SERVER['REQUEST_URI']} was not found on this server" . '</body></html>';
	}

	exit();
}
