<?php
require_once dirname( __FILE__ ) . '/Version.php';
require_once WP_PLUGIN_DIR . '/barbwire-security/admin/settings.php';
require_once dirname( __FILE__ ) . '/DisableXMLRPCPingBack.php';

require_once dirname( __FILE__ ) . '/LoginParameter.php';
require_once dirname( __FILE__ ) . '/AuthorArchive.php';
require_once dirname( __FILE__ ) . '/DisableRESTAPI.php';

use barbsecurity\Version as Version;
use barbsecurity\LoginParameter as LoginParameter;
use barbsecurity\AuthorArchive as AuthorArchive;
use barbsecurity\DisableRESTAPI as DisableRESTAPI;
use barbsecurity\DisableXMLRPCPingBack as DisableXMLRPCPingBack;

define( 'BARB_SECURITY_AUTHORITYSECURE', 'manage_options' );    //User level required in order to change the settings.
define( 'BARB_SECURITY_SAVE_TRANSIENT', Version::$name . "_SAVE" );
define( 'BARB_SECURITY_OPTION_TRANSIENT', Version::$name . "_OPTION" );

$version               = Version::get_version();
$barb_security_options = BarbwireSecurity::get_option();
function barb_security_plugins_loaded() {
	$result = load_plugin_textdomain( Version::$name, false, Version::$name . '/languages' );
}

add_action( 'plugins_loaded', 'barb_security_plugins_loaded' );


/**
 * check login
 */
function barb_security_login_init() {
	global $barb_security_options;

	/**
	 * @see wordpress wp-login.php
	 */
	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';
	if ( isset( $_GET['key'] ) ) {
		$action = 'resetpass';
	}
	if ( ! in_array( $action, array( 'postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login', 'confirmaction', 'entered_recovery_mode' ), true ) && false === has_filter( 'login_form_' . $action ) ) {
		$action = 'login';
	}

	if ( 'postpass' !== $action && isset( $barb_security_options['parameter_enable'] ) && $barb_security_options['parameter_enable'] == true ) {
		// リファラが空の場合はGETにパラメータがあることをチェックする
		if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			// check get parameter case referer is empty
			if ( ! LoginParameter::check_get_param() ) {
				exit_404();
			}
		} else if ( isset( $_SERVER['HTTP_REFERER'] ) && strpos( $_SERVER['HTTP_REFERER'], '/wp-login.php' ) !== false ) {
			/**
			 * リファラがwp-login.phpの場合はリファラかリクエストにパラメータがあることを確認する
			 */
			//$actions = array('postpass', 'lostpassword', 'retrievepassword', 'resetpass', 'rp');
			//if(isset($_GET['action']) && in_array($_GET['action'], $actions, true)){
			//    return;
			//}

			if ( ! LoginParameter::check_referer_param() || ! LoginParameter::check_get_param() ) {
				exit_404();
			}
		} else if ( isset( $_SERVER['HTTP_REFERER'] ) && strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/' ) !== false ) {
			// do nothing case referer is wp-admin
			return true;
		} else if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			// それ以外のリファラでGETにパラメータがあることをチェックする
			if ( ! LoginParameter::check_get_param() ) {
				exit_404();
			}
		} else {
			exit_404();
		}

	}

}

add_action( 'logadd_options_pagein_init', 'barb_security_login_init', 1 );


function barb_security_secure_auth_redirect() {
	global $barb_security_options;
	if ( isset( $barb_security_options['parameter_enable'] ) && $barb_security_options['parameter_enable'] == true ) {
		if ( strpos( $_SERVER['REQUEST_URI'], 'wp-admin' ) !== false && ! is_user_logged_in() ) {
			// wp-adminからの未ログインリダイレクトの場合は404で終了する
			exit_404();
		}
	}

}

add_action( 'secure_auth_redirect', 'barb_security_secure_auth_redirect' );

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




/*************** OTHER ***************/

/**
 * Exit error 404
 */
function exit_403() {
	echo '<html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1>' . __( 'Failed to login.', 'barbwire-security' ) . '</body></html>';
	status_header( 403 );
	exit();
}

/**
 * Exit error 403
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
