<?php
namespace barbsecurity;

require_once dirname(__FILE__).'/barb_libs.php';
require_once dirname(__FILE__).'/Version.php';

use \BarbTool as BarbTool;

/**
 * パラメータチェック用クラス
 * Author nagasawa@barbwire.co.jp
 * Copyright barbwire.co.jp
 */
class LoginParameter {
	
	public static $key = 'secure';
	public static $val = 'true';

	/**
	 * GETリクエストにパラメータが含まれるかどうかをチェックする
	 * @return bool
	 */
	public static function checkGetParam(){
        BarbTool::bp_log("checkParam");
		$options = get_option(Version::$name);
		$key = $options['param_name'];
		$val = $options['param_value'];

		if(! isset($_GET[$key]) || $_GET[$key] != $val){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * リファラにパラメータが含まれるかどうかをチェックする
	 * @return bool
	 */
	public static function checkRefererParam(){
        BarbTool::bp_log("checkParam");
		$options = get_option(Version::$name);

		$key = $options['param_name'];
		$val = $options['param_value'];

		if(strpos($_SERVER['HTTP_REFERER'], (urlencode($key).'='.urlencode($val))) !== false){
			return true;
		}else{
			return false;
		}

	}



	public static function activate(){
        BarbTool::bp_log("activeteLoginParameter");

		add_filter('login_url',array('barbsecurity\LoginParameter', 'filter_login_url'),1, 3);
		add_filter('logout_redirect',array('barbsecurity\LoginParameter', 'filter_logout_redirect'),1);
		add_filter('lostpassword_redirect',array('barbsecurity\LoginParameter', 'filter_lostpassword_redirect'),1);
		add_filter('lostpassword_url',array('barbsecurity\LoginParameter', 'filter_lostpassword_url'),1);
		add_filter('site_url',array('barbsecurity\LoginParameter', 'filter_site_url'),1);


		//add_filter('network_site_url',array('barbsecurity\LoginParameter', 'filter_network_site_url'),3); todo not tested

	}


	public static function filter_login_url($login_url, $redirect, $force_reauth){
        BarbTool::bp_log('filter_login_url');

        // not login or redirect login url
        // refer to wordpress/wordpress/wp-includes/canonical.php wp_redirect_admin_locations
        if(!is_user_logged_in()){
            $logins = array(
                home_url( 'wp-admin', 'relative' ),
                home_url( 'dashboard', 'relative' ),
                site_url( 'admin', 'relative' ),
                home_url( 'wp-login.php', 'relative' ),
                home_url( 'login', 'relative' ),
            );

            if ( in_array( untrailingslashit( $_SERVER['REQUEST_URI'] ), $logins ) ) {
                exit_404();
            }
        }
		return self::addParameter($login_url, $redirect, $force_reauth);
	}

	public static function filter_logout_redirect($redirect_to){
        BarbTool::bp_log('filter_logout_redirect');
		return self::addParameter($redirect_to);
	}

	public static function filter_lostpassword_redirect($lostpassword_redirect){
        BarbTool::bp_log('filter_lostpassword_redirect');
		return self::addParameter($lostpassword_redirect);
	}

	public static function filter_lostpassword_url($redirect){
        BarbTool::bp_log('filter_lostpassword_url');
		return self::addParameter($redirect);
	}


	public static function filter_site_url($url){
        BarbTool::bp_log('filter_site_url');
		if(strpos($url, 'action=lostpassword') !== false) {
			// case action of lostpassword form
			return self::addParameter($url);
		}elseif(strpos($url, 'action=rp') !== false) {
			// case action of lostpassword message
			return self::addParameter($url);
		}elseif(strpos($url, 'action=resetpass') !== false) {
			// case action of reset password form
			return self::addParameter($url);
		}elseif(preg_match("/\/wp-login.php$/", $url)){
			// case action of login form
			return self::addParameter($url);
		}else{
			return $url;
		}

	}

	public static function filter_network_site_url($url, $path, $scheme ){
        BarbTool::bp_log('filter_network_site_url');
		return self::addParameter($url, $path);
	}


	public static function addParameter($login_url, $redirect = '', $force_reauth = false) {
        BarbTool::bp_log("login_url={$login_url}");
        BarbTool::bp_log("redirect={$redirect}");
        BarbTool::bp_log("force_reauth={$force_reauth}");

		if(empty($login_url)){
			$login_url = '/wp-login.php';
		}


		// case forth auth, redirect invalid(default) login page.
		//if (!$force_reauth) {
		$options = get_option(Version::$name);

        if($options['parameter_enable'] == 1){
            $key = $options['param_name'];
            $val = $options['param_value'];
            if(strpos($login_url, "{$key}={$val}") === false){
                $login_url .= strpos($login_url, '?') === false ? '?' : '&';
                $login_url .= "{$key}={$val}";
            }
        }

		//}
		return $login_url;
	}

/* never tested
	public static function addParameterSecond($errors, &$redirect_to){
		if(count($errors) > 0){
			$options = get_option(Version::$name);
			$key = $options['param_name'];
			$val = $options['param_value'];
			$redirect_to .= strpos($redirect, '?') === false ? '?' : '&';
			$redirect_to .= "{$key}={$val}";
		}
		return $errors;
	}
 */
}
