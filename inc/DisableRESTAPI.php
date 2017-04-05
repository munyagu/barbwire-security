<?php

namespace barbsecurity;

/**
 * Class DisableRESTAPI
 * @package barbsecurity
 *
 * test url http://hostname/wp-json/wp/v2/posts
 *
 */
class DisableRESTAPI {

	/**
	 * activate disnable rest api
	 */
	public static function activate() {

		if ( defined( 'JSON_API_VERSION' ) && version_compare( JSON_API_VERSION, '2.0', '<' ) ) {
			add_filter( 'json_enabled', '__return_false' );
			add_filter( 'json_jsonp_enabled', '__return_false' );
		} else if ( defined( 'REST_API_VERSION' ) && version_compare( REST_API_VERSION, '2.0', '>=' ) ) {

			if ( version_compare( get_bloginfo('version'), '4.7', '<' ) ) {
				add_filter( 'rest_enabled', '__return_false' ); // Deprecated in WordPress 4.7
				add_filter( 'rest_jsonp_enabled', '__return_false' ); // Deprecated in WordPress 4.7
			}

			add_filter( 'rest_authentication_errors', array(
				'barbsecurity\DisableRESTAPI',
				'rest_authentication_errors'
			) );
		} else {
			// not exists rest api
		}

	}


	/**
	 * Always return error at check_authentication.
	 * It always called in serve_request rest api version 2.0.
	 * @return \WP_Error
	 */
	public static function rest_authentication_errors() {

		if ( function_exists( 'rest_authorization_required_code' ) ) {
			$error_code = rest_authorization_required_code();
		} else {
			$error_code = 401;
		}

		return new \WP_Error( 'rest_authentication_error', __( 'You are not authenticated.' ), array( 'status' => $error_code ) );
	}

}