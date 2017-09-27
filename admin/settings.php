<?php
/*************************************
 * SETTING SCREEN
 *************************************/
require_once WP_PLUGIN_DIR . '/barbwire-security/inc/LoginParameter.php';
require_once WP_PLUGIN_DIR . '/barbwire-security/inc/Version.php';

use barbsecurity\LoginParameter as LoginParameter;
use barbsecurity\Version as Version;

define( 'BARB_SECURITY_URL_REGEX', '/[^0-9a-zA-Z_-]/' );

/**
 * Add admin style/javascript
 */
function barb_security_admin_print_scripts() {

	wp_enqueue_style( 'barb_security_admin_style', plugins_url() . '/barbwire-security/admin/css/config.css' );
	wp_enqueue_script( 'barb_security_admin_script', plugins_url() . '/barbwire-security/admin/js/config.js', array( 'jquery' ) );
}

/**
 * Add security pack menu
 */
function barbwire_security_admin_menu() {

	$hook = add_options_page( __( 'Barbwire Security Setting', 'barbwire-security' ),
		__( 'Barbwire Security', 'barbwire-security' ),
		BARB_SECURITY_AUTHORITYSECURE,
		'barb_secure_settings',
		'barbwire_security_settings'
	);

	add_action( "admin_print_scripts-$hook", 'barb_security_admin_print_scripts' );

}

add_action( 'admin_menu', 'barbwire_security_admin_menu' );

/**
 * Add help to setting page
 *
 * @param string $help Help text that appears on the screen.
 * @param string $screen_id Screen ID.
 * @param WP_Screen $screen Current WP_Screen instance.
 */
function barbwire_security_contextual_help( $help, $screen_id, $screen ) {
	if ( 'settings_page_barb_secure_settings' === $screen_id ) {

		$content = '<p>';
		$content .= __( 'You can ward off tying for try to login for cracking, such as Brute-force attack.', 'barbwire-security' );
		$content .= '<br />';
		$content .= __( 'Adding any parameter to login URL so that login screen will hidden.', 'barbwire-security' );
		$content .= '<br />';
		$content .= __( 'It also prevents direct access to /wp-admin/ page etc.', 'barbwire-security' );
		$content .= '</p>';

		$tab = array(
			'title'   => __( 'Enable login url parameter function', 'barbwire-security' ),
			'id'      => 'login_parameter',
			'content' => $content,
		);
		$screen->add_help_tab( $tab );

		$content = '<p>';
		$content .= __( 'WordPress leaks your login id because of redirect author archive page by author id to login id.', 'barbwire-security' );
		$content .= '<br />';
		$content .= '(' . __( 'If you enter "your-site-url/?author=1", you can try it.', 'barbwire-security' ) . ')';
		$content .= '<br />';
		$content .= __( 'Simply hideing author archive page so that block to leak login id.', 'barbwire-security' );
		$content .= '</p>';

		$tab = array(
			'title'   => __( 'Block the display of author archive page', 'barbwire-security' ),
			'id'      => 'author_archive',
			'content' => $content
		);
		$screen->add_help_tab( $tab );

		$content = '<p>';
		$content .= __( 'Block DDOS attacks against other sites with yor WordPress site, pingback enabled.', 'barbwire-security' );
		$content .= '<br />';
		$content .= __( '<a href="http://www.digitalattackmap.com/#anim=1&color=0&country=ALL&list=2&time=17108&view=map" target="_blank">Sites around the world have been exposed to the threat of DDOS attack.</a>', 'barbwire-security' );
		$content .= '</p>';

		$tab = array(
			'title'   => __( 'Restrict XML-RPC Pingback function', 'barbwire-security' ),
			'id'      => 'pingback',
			'content' => $content
		);
		$screen->add_help_tab( $tab );

		$content = '<p>';
		$content .= __( '\'REST\' is REpresentational State Transfer function.<br>It is simple http request and respons API.', 'barbwire-security' ) . '<br>';
		$content .= __( 'It has been incorporated into the WordPress core since version 4.7 and is used in several functions.', 'barbwire-security' ) . '<br>';
		$content .= __( 'Various functions are now using the REST API, it is not always right to invalidate everything.', 'barbwire-security' ) . '<br><br>';
		$content .= __( 'Restricting the REST function can lower the risk of attacking the REST API..', 'barbwire-security' ) . '<br>';
		$content .= __( 'It is best to disable anonymous REST requests and enable them for specific functions.', 'barbwire-security' );
		$content .= '</p>';

		$tab = array(
			'title'   => __( 'Disable REST API', 'barbwire-security' ),
			'id'      => 'restapi',
			'content' => $content
		);
		$screen->add_help_tab( $tab );

	}
}

add_filter( 'contextual_help', 'barbwire_security_contextual_help', 900, 3 );


/**
 * Display setting page
 */
function barbwire_security_settings() {
	include dirname( __FILE__ ) . '/template/page-setting.php';
}

/**
 * Get and format posted potion values
 *
 * @return array posted option values
 */
function barbwire_security_get_admin_posted_option() {
	$options = array();

	/* ADMIN LOGIN PAGE URL PARAMETER */
	$options['parameter_enable'] = isset( $_POST['parameter_enable'] ) && $_POST['parameter_enable'] === '1' ? true : false;
	$options['param_name']       = isset( $_POST['param_name'] ) && $_POST['param_name'] !== '' ? esc_sql( $_POST['param_name'] ) : LoginParameter::$key;
	$options['param_value']      = isset( $_POST['param_value'] ) && $_POST['param_value'] !== '' ? esc_sql( $_POST['param_value'] ) : LoginParameter::$val;

	/* LOGIN RETRY LIMIT COUNT */
	/* TODO Unimplemented
	$options['retry_times_enable'] = !empty($_POST['retry_times_enable']) && $_POST['retry_times_enable'] == 1 ? true : false;
	$options['retry_limit'] = isset($_POST['retry_limit']) ? $_POST['retry_limit'] : '';
	$options['retry_lock_period'] = isset($_POST['retry_lock_period']) ? $_POST['retry_lock_period'] : '';
	$options['retry_connection'] = isset($_POST['retry_connection']) ? $_POST['retry_connection'] : '';
	*/

	/* block the display of author archive page */
	$options['block_author_archive'] = isset( $_POST['block_author_archive'] ) && $_POST['block_author_archive'] === '1' ? true : false;

	/* PINGBACK */
	$options['pingback_suppress_enable'] = isset( $_POST['pingback_suppress_enable'] ) && $_POST['pingback_suppress_enable'] === '1' ? true : false;

	/* REST API */
	$options['disable_rest_api'] = isset( $_POST['disable_rest_api'] ) ? $_POST['disable_rest_api'] : 0;

	$options['installed_end_point'] = isset( $_POST['installed_end_point'] ) ? $_POST['installed_end_point'] : array();

	$options['end_points'] = array();
	if ( isset( $_POST['end_points'] ) ) {
		// Format the value.
		$values = explode( "\n", $_POST['end_points'] );
		$values = array_map( 'trim', $values );
		$values = array_filter( $values, 'strlen' );

		$options['end_points'] = $values;
	}

	return $options;

}

/**
 * Save settings
 */
function barbwire_security_admin_init() {

	if ( ! empty( $_POST['barb_secure'] ) ) {

		// Check CSRF.
		if ( ! check_admin_referer( Version::$name, 'barb_secure' ) ) {
			exit_403();
		}

		$messages = new WP_Error();
		add_action( 'admin_notices', 'barbwire_security_admin_notices' );

		if ( ! current_user_can( BARB_SECURITY_AUTHORITYSECURE ) ) {
			$messages->add( 'error', __( 'Authority is missing.', 'barbwire-security' ) );
			set_transient( BARB_SECURITY_SAVE_TRANSIENT, $messages, MINUTE_IN_SECONDS );

			return;
		}

		$options = wp_parse_args( barbwire_security_get_admin_posted_option(), BarbwireSecurity::get_option() );


		// Check URL parameters
		// http://www.asahi-net.or.jp/~ax2s-kmtn/ref/uric.html
		if ( preg_match( BARB_SECURITY_URL_REGEX, $_POST['param_name'] ) === 1 ) {
			$messages->add( 'error', __( 'There is an error in the parameter name.', 'barbwire-security' ) );
		}

		if ( preg_match( BARB_SECURITY_URL_REGEX, $_POST['param_value'] ) === 1 ) {
			$messages->add( 'error', __( 'There is an error in the parameter value.', 'barbwire-security' ) );
		}


		/* CAPTCHA */
		/* TODO Unimplemented
		$options['captcha_enable'] = isset($_POST['captcha_enable']) && $_POST['captcha_enable'] == 1 ? true : false;
		*/

		if ( count( $messages->errors ) > 0 ) {
			set_transient( BARB_SECURITY_SAVE_TRANSIENT, $messages, MINUTE_IN_SECONDS );
			set_transient( BARB_SECURITY_OPTION_TRANSIENT, $options, MINUTE_IN_SECONDS );

			return;
		}

		BarbwireSecurity::update_option( $options );

		if ( $options['parameter_enable'] ) {
			add_filter( 'login_url', array( 'barbsecurity\LoginParameter', 'add_parameter' ), 1 );
		} else {
			remove_filter( 'login_url', array( 'barbsecurity\LoginParameter', 'add_parameter' ), 1 );
		}

		delete_transient( BARB_SECURITY_SAVE_TRANSIENT );
		delete_transient( BARB_SECURITY_OPTION_TRANSIENT );

		//set_transient( BARB_SECURITY_SAVE_TRANSIENT, $messages, MINUTE_IN_SECONDS );
	}
}

add_action( 'admin_init', 'barbwire_security_admin_init' );

/**
 * Show notice messages.
 */
function barbwire_security_admin_notices() {
	$messages = get_transient( BARB_SECURITY_SAVE_TRANSIENT );
	$errors   = ! empty( $messages->errors['error'] ) ? $messages->errors['error'] : array();
	$infos    = ! empty( $messages->errors['info'] ) ? $messages->errors['info'] : array();

	foreach ( $errors as $error ) {
		echo "<div class='error'><p>{$error}</p></div>";
	}
	foreach ( $infos as $info ) {
		echo "<div class='updated'><p>{$info}</p></div>";
	}
}

