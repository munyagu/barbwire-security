<?php

namespace barbsecurity;

require_once dirname( __FILE__ ) . '/Version.php';

use \BarbwireSecurity as BarbwireSecurity;

/**
 * パラメータチェック用クラス
 * Author nagasawa@barbwire.co.jp
 * Copyright barbwire.co.jp
 */
class LoginParameter {

	/**
	 * Default parameter key name
	 *
	 * @var string
	 */
	public static $key = 'secure';

	/**
	 * Default parameter value name
	 *
	 * @var string
	 */
	public static $val = 'true';

	/**
	 * Check GET parameter in login access
	 *
	 * @return bool
	 */
	public static function check_get_param() {
		$options = BarbwireSecurity::get_option();
		$key     = $options['param_name'];
		$val     = $options['param_value'];

		if ( ! isset( $_GET[ $key ] ) || $_GET[ $key ] !== $val ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Check referer parameter in login access
	 *
	 * @return bool
	 */
	public static function check_referer_param() {
		$options = BarbwireSecurity::get_option();

		$key = $options['param_name'];
		$val = $options['param_value'];

		if ( strpos( $_SERVER['HTTP_REFERER'], ( rawurlencode( $key ) . '=' . rawurlencode( $val ) ) ) !== false ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Activate Login parameter check
	 */
	public static function activate() {

		add_filter( 'login_url', array( 'barbsecurity\LoginParameter', 'filter_login_url' ), 1, 3 );
		add_filter( 'logout_redirect', array( 'barbsecurity\LoginParameter', 'filter_logout_redirect' ), 1 );
		add_filter( 'lostpassword_redirect', array(
			'barbsecurity\LoginParameter',
			'filter_lostpassword_redirect',
		), 1 );
		add_filter( 'lostpassword_url', array( 'barbsecurity\LoginParameter', 'filter_lostpassword_url' ), 1 );
		add_filter( 'site_url', array( 'barbsecurity\LoginParameter', 'filter_site_url' ), 1 );

	}

	/**
	 * Disable redirection to login url
	 *
	 * @param string $login_url The login URL. Not HTML-encoded.
	 * @param string $redirect The path to redirect to on login, if supplied.
	 * @param bool   $force_reauth Whether to force re authorization, even if a cookie is present.
	 *
	 * @return string
	 */
	public static function filter_login_url( $login_url, $redirect, $force_reauth ) {

		// not login or redirect login url
		/* refer to wordpress/wordpress/wp-includes/canonical.php wp_redirect_admin_locations */
		if ( ! is_user_logged_in() ) {
			$logins = array(
				home_url( 'wp-admin', 'relative' ),
				home_url( 'dashboard', 'relative' ),
				site_url( 'admin', 'relative' ),
				home_url( 'wp-login.php', 'relative' ),
				home_url( 'login', 'relative' ),
			);

			if ( in_array( untrailingslashit( $_SERVER['REQUEST_URI'] ), $logins, true ) ) {
				exit_404();
			}
		}

		return self::add_parameter( $login_url, $redirect, $force_reauth );
	}

	/**
	 * Redirect to chenged url after logout
	 *
	 * @param string $redirect_to original logout url.
	 *
	 * @return string
	 */
	public static function filter_logout_redirect( $redirect_to ) {
		return self::add_parameter( $redirect_to );
	}

	/**
	 * Redirect to chenged url of lost password page
	 *
	 * @param string $lostpassword_redirect original logout url.
	 *
	 * @return string
	 */
	public static function filter_lostpassword_redirect( $lostpassword_redirect ) {
		return self::add_parameter( $lostpassword_redirect );
	}

	/**
	 * Change the Lost Password URL.
	 *
	 * @param string $lostpassword_url The lost password page URL.
	 *
	 * @return string
	 */
	public static function filter_lostpassword_url( $lostpassword_url ) {
		return self::add_parameter( $lostpassword_url );
	}


	/**
	 * Change site url
	 *
	 * @param string $url     The complete site URL including scheme and path.
	 *
	 * @return string
	 */
	public static function filter_site_url( $url ) {

		if ( strpos( $url, 'action=lostpassword' ) !== false ) {
			// case action of lostpassword form.
			return self::add_parameter( $url );
		} elseif ( strpos( $url, 'action=rp' ) !== false ) {
			// case action of lostpassword message.
			return self::add_parameter( $url );
		} elseif ( strpos( $url, 'action=resetpass' ) !== false ) {
			// case action of reset password form.
			return self::add_parameter( $url );
		} elseif ( 1 === preg_match( '/\/wp-login.php$/', $url ) ) {
			// case action of login form.
			return self::add_parameter( $url );
		} elseif ( 1 === preg_match( '/\/wp-login.php.+?action=confirm_admin_email/', $url ) ) {
			// case action of login from admin email confirmation.
			return self::add_parameter( $url );
		} else {
			return $url;
		}

	}


	/**
	 * Add parameter to login url
	 *
	 * @param string $login_url original login url.
	 * @param string $redirect TODO UNUSED.
	 * @param bool   $force_reauth TODO UNUSED.
	 *
	 * @return string
	 */
	public static function add_parameter( $login_url, $redirect = '', $force_reauth = false ) {
		if ( empty( $login_url ) ) {
			$login_url = '/wp-login.php';
		}

		// case forth auth, redirect invalid(default) login page.
		$options = BarbwireSecurity::get_option();

		if ( $options['parameter_enable'] == 1 ) {
			$key = $options['param_name'];
			$val = $options['param_value'];
			if ( strpos( $login_url, "{$key}={$val}" ) === false ) {
				$login_url .= strpos( $login_url, '?' ) === false ? '?' : '&';
				$login_url .= "{$key}={$val}";
			}
		}

		return $login_url;
	}

	/* never tested
		public static function addParameterSecond($errors, &$redirect_to){
			if(count($errors) > 0){
				$options = BarbwireSecurity::getOption();
				$key = $options['param_name'];
				$val = $options['param_value'];
				$redirect_to .= strpos($redirect, '?') === false ? '?' : '&';
				$redirect_to .= "{$key}={$val}";
			}
			return $errors;
		}
	 */
}
