<?php

namespace barbsecurity;

use \BarbwireSecurity;

class ReCaptcha {

	const RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	const THRESHOLD = 0.50;

	const REST_ROOT_TEST = 'recaptcha_test';


	/**
	 * @see https://developers.google.com/recaptcha/docs/verify#error_code_reference
	 */
	const ERROR_DESCRIPTIONS = [
		'missing-input-secret'   => 'The secret parameter is missing.',
		'invalid-input-secret'   => 'The secret parameter is invalid or malformed.',
		'missing-input-response' => 'The response parameter is missing.',
		'invalid-input-response' => 'The response parameter is invalid or malformed.',
		'bad-request'            => 'The request is invalid or malformed.',
		'timeout-or-duplicate'   => 'The response is no longer valid: either is too old or has been used previously.'
	];

	public static function init() {
		add_action( 'login_enqueue_scripts', [ __CLASS__, 'load_recaptcha_script' ] );
		add_action( 'login_form', [ __CLASS__, 'login_form' ] );
        add_action( 'lostpassword_form', [ __CLASS__, 'lostpassword_form' ] );
		add_filter( 'authenticate', [ __CLASS__, 'check_login_recaptcha' ], 99 );
		add_action( 'rest_api_init', [ __CLASS__, 'add_endpoint' ] );
	}

	public static function is_recaptcha_enabled(){
		$options = BarbwireSecurity::get_option();

		$enabled = isset( $options['captcha_enable'] ) && true === $options['captcha_enable'];
		$site_key_enabled = isset( $options['recaptcha_site_key'] ) && '' !== $options['recaptcha_site_key'];
		$secret_key_enabled = isset( $options['recaptcha_secret_key'] ) && '' !== $options['recaptcha_secret_key'];
		$environment_enable = true;
		if(function_exists('wp_get_environment_type')){
            $environment_enable = 'local' !== wp_get_environment_type();
        }

		return $enabled && $site_key_enabled && $secret_key_enabled && $environment_enable;
    }

	private static function get_site_key() {
		$options = BarbwireSecurity::get_option();

		return isset( $options['recaptcha_site_key'] ) ? $options['recaptcha_site_key'] : '';
	}

	private static function get_secret_key() {
		$options = BarbwireSecurity::get_option();

		return isset( $options['recaptcha_secret_key'] ) ? $options['recaptcha_secret_key'] : '';
	}

	public static function load_recaptcha_script() {
		if ( 'wp-login.php' === $GLOBALS['pagenow'] ) {
			$enabled = self::is_recaptcha_enabled();
			if( $enabled ) {
				$site_key = self::get_site_key();
			?>
            <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_attr( $site_key ); ?>"></script>
            <script>
                window.onload = function () {
                    document.getElementById('wp-submit').addEventListener('click', barb_sec_recatpcha);
                }

                function barb_sec_recatpcha(event) {
                    event.preventDefault();
                    grecaptcha.ready(function () {
                        var action_type = document.getElementById('barb_sec_action').value;
                        grecaptcha.execute('<?php echo esc_attr( $site_key ); ?>', {action: action_type}).then(function (token) {
                            var token_input = document.getElementById('barb_sec_token');
                            token_input.value = token;
                            var form_id = 'login' === action_type ? 'loginform' : 'lostpassword' === action_type ? 'lostpasswordform': '';
                            document.getElementById(form_id).submit();
                        });
                    });
                }

            </script>
			<?php
            }
		}
	}

	/**
	 * add hidden input
	 */
	public static function login_form() {
		self::__login_form('login');
	}

	public static function lostpassword_form(){
        self::__login_form('lostpassword');
    }

	public static function __login_form($form_type){
        ?>
        <input type="hidden" id="barb_sec_token" name="barb_sec_recaptcha_response">
        <input type="hidden" id="barb_sec_action" name="barb_sec_action" value="<?php echo esc_attr($form_type);?>">
        <?php
    }

	/**
	 * add endpoint for reCaptcha admin page.
	 */
	public static function add_endpoint() {
		register_rest_route(
			BarbwireSecurity::REST_NAMESPACE . BarbwireSecurity::REST_CURRENT_VERSION,
			'/recaptcha_test',
			[
				'methods'  => 'post',
				'callback' => [ __CLASS__, 'test_recaptcha' ],
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				}
			]
        );
	}

	public static function test_recaptcha( $request ){

		$token = $request->get_param('token');
		$secret_key = $request->get_param('secret_key');

		$response_data = self::get_recaptcha_response( $token, $secret_key );
		$verified = self::verify( $response_data );

        return new \WP_REST_Response($verified);

    }

	/**
	 * check reCaptcha
	 *
	 * @param $user
	 *
	 * @return mixed failed set to null;
	 */
	public static function check_login_recaptcha( $user ) {

		$enabled = self::is_recaptcha_enabled();

		if( $enabled ) {
			if ( isset( $_POST['barb_sec_recaptcha_response'] )
			     && ! is_wp_error( $user ) ) {

				$response_data = self::get_recaptcha_response( $_POST['barb_sec_recaptcha_response'] );

				$verified = self::verify( $response_data );
				if ( ! $verified['is_human'] ) {
					$user = false;
				}
			}
		}


		return $user;
	}

	/**
     * Request reCaptcha and return response data.
     *
	 * @param null $secret_key
	 *
	 * @return mixed
	 */
	public static function get_recaptcha_response( $token, $secret_key = null  ) {

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_URL, self::RECAPTCHA_VERIFY_URL );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		if( null === $secret_key ){
			$secret_key = self::get_secret_key();
		}

		$query     = [
			'secret'   => $secret_key,
			'response' => $token
		];

		$remote_ip = self::get_remote_ip();
		if ( null !== $remote_ip ) {
			$query['remoteip'] = $remote_ip;
		}

		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $query ) );
		$response = curl_exec( $ch );

		return json_decode( $response );

	}

	/**
	 * Get client ip address.
	 * @return mixed|null
	 */
	private static function get_remote_ip() {

		$remote_ip = null;

		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip_array  = explode( ",", $_SERVER['HTTP_X_FORWARDED_FOR'] );
			$remote_ip = $ip_array[0];
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$remote_ip = $_SERVER['REMOTE_ADDR'];
		}

		return $remote_ip;
	}

	/**
	 * Get threshold for reCaptcha score.
	 * @return mixed
	 */
	private static function get_threshold() {
		return apply_filters( 'barb_sec_recaptcha_threshold', self::THRESHOLD );
	}


	/**
	 * Check the response from google.
	 *
	 * @param $response_data
	 *
	 * @return array
	 */
	private static function verify( $response_data ) {

		$result = [
		    'success' => $response_data->success,
			'errors'   => [],
			'is_human' => false
		];

		$is_human = false;

		if ( false === $response_data->success ) {
			$is_human = false;
			if ( isset( $response_data->{'error-codes'} ) ) {

				foreach ( $response_data->{'error-codes'} as $error_code ) {
				    $descriptions = self::ERROR_DESCRIPTIONS;
					$description                     = isset( $descriptions[ $error_code ] ) ? $descriptions[ $error_code ] : '';
					$result['errors'][ $error_code ] = $description;
					/*
					BarbwireSecurity::log(
						sprintf(
							'reCaaptch error error_code=%s description=%s',
							$error_code,
							$description
						)
					);
					*/
				}

			}
		} else {
			$threshold = self::get_threshold();
			if ( $threshold > $response_data->score ) {
				$is_human = false;
				/*
				BarbwireSecurity::log(
					sprintf(
						'reCaaptch error score=%s remote_ip=%s useragent=%s',
						$response_data->score,
						self::get_remote_ip(),
						$_SERVER['HTTP_USER_AGENT']
					)
				);
				*/
			} else {
				$is_human = true;
			}
		}

		$result['is_human'] = $is_human;

		return $result;
	}


}

