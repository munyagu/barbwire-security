<?php
/*************************************
 * SETTING SCREEN
 *************************************/
require_once dirname(__FILE__).'/../LoginParameter.php';
require_once dirname(__FILE__).'/../Version.php';
use barbsecurity\LoginParameter as LoginParameter;
use barbsecurity\Version as Version;

define('BARB_SECURITY_URL_REGEX', '/[^0-9a-zA-Z_-]/');
if(!defined('BARB_DEBUG')){
    define('BARB_DEBUG', false);
}



/**
 * Add admin style/javascript
 */
function barb_security_admin_print_scripts(){
    BarbTool::bp_log("barb_security_admin_print_scripts");
    wp_enqueue_style( 'barb_security_admin_style', plugins_url().'/barbwire-security/css/config.css' );
    wp_enqueue_script( 'barb_security_admin_script', plugins_url().'/barbwire-security/js/config.js', array('jquery'));
}

/**
 * Add security pack menu
 */
function barb_security_admin_menu() {
    BarbTool::bp_log("barb_security_admin_menu");
    $hook = add_menu_page(
        __('Barbwire Security Setting', Version::$name),
        __('Barbwire Security', Version::$name),
        BARB_SECURITY_AUTHORITYSECURE,
        'barb_secure_settings',
        'barb_disp_secure_settings',
        'dashicons-lock'
    );
    add_action( "admin_print_scripts-$hook", 'barb_security_admin_print_scripts' );
}
add_action('admin_menu', 'barb_security_admin_menu');

/**
 * Add help to setting page
 */
function barb_security_contextual_help($help, $screen_id, $screen){
    if($screen_id == 'toplevel_page_barb_secure_settings'){

        $content = '<p>';
        $content .= __('You can ward off tying for try to login for cracking, such as Brute-force attack.', Version::$name);
        $content .= '<br />';
        $content .= __('Adding any parameter to login URL so that login screen will hidden.', Version::$name);
        $content .= '</p>';

        $tab = array(
            'title' => __('Enable login url parameter function' , Version::$name),
            'id' => 'login_parameter',
            'content' => $content
        );
        $screen->add_help_tab($tab);

        $content = '<p>';
        $content .= __('WordPress leaks your login id because of redirect author archive page by author id to login id.', Version::$name);
        $content .= '<br />';
        $content .= '(' . __('If you enter "your-site-url/?author=1", you can try it.', Version::$name) . ')';
        $content .= '<br />';
        $content .= __('Simply hideing author archive page so that block to leak login id.', Version::$name);
        $content .= '</p>';

        $tab = array(
            'title' => __('Block the display of author archive page' , Version::$name),
            'id' => 'author_archive',
            'content' => $content
        );
        $screen->add_help_tab($tab);

        $content = '<p>';
        $content .= __('Block DDOS attacks against other sites with yor WordPress site, pingback enabled.', Version::$name);
        $content .= '<br />';
        $content .= __('<a href="http://www.digitalattackmap.com/#anim=1&color=0&country=ALL&list=2&time=17108&view=map" target="_blank">Sites around the world have been exposed to the threat of DDOS attack.</a>', Version::$name);
        $content .= '</p>';

        $tab = array(
            'title' => __('Suppress XML-RPC Pingback function' , Version::$name),
            'id' => 'pingback',
            'content' => $content
        );
        $screen->add_help_tab($tab);
    }
}
add_filter('contextual_help', 'barb_security_contextual_help', 900, 3);


/**
 * セキュリティパック管理画面表示
 */
function barb_disp_secure_settings(){
    include dirname(__FILE__).'/template/page-setting.php';
}

/**
 * 設定の保存
 */
function barb_security_admin_init(){
    BarbTool::bp_log("barb_security_save_setting");

    if(!empty($_POST['barb_secure'])){

        // CSRF対策のチェック
        if(! check_admin_referer(Version::$name, 'barb_secure')){
            exit_403();
        }

        $messages = new WP_Error();
        add_action( 'admin_notices', 'barb_security_admin_notices');

        if(!current_user_can(BARB_SECURITY_AUTHORITYSECURE)){
            $messages->add('error', __( 'Authority is missing.', Version::$name ));
            set_transient(BARB_SECURITY_SAVE_TRANSIENT, $messages, MINUTE_IN_SECONDS);
            return;
        }

        $options = array();

        /* ADMIN LOGIN PAGE URL PARAMETER */
        $options['parameter_enable'] = isset($_POST['parameter_enable']) && $_POST['parameter_enable'] == 1 ? true : false;

        // URLパラメータチェック
        // http://www.asahi-net.or.jp/~ax2s-kmtn/ref/uric.html
        if(preg_match(BARB_SECURITY_URL_REGEX, $_POST['param_name']) === 1){
            $messages->add('error', __( 'There is an error in the parameter name.', Version::$name ));
        }

        if(preg_match(BARB_SECURITY_URL_REGEX, $_POST['param_value']) === 1){
            $messages->add('error', __( 'There is an error in the parameter value.', Version::$name ));
        }

        $options['param_name'] = isset($_POST['param_name']) && $_POST['param_name'] != '' ? esc_sql($_POST['param_name']) : LoginParameter::$key;
        $options['param_value'] = isset($_POST['param_value']) && $_POST['param_value'] != '' ? esc_sql($_POST['param_value']) : LoginParameter::$val;

        /* LOGIN RETRY LIMIT COUNT */
        /* TODO Unimplemented
        $options['retry_times_enable'] = !empty($_POST['retry_times_enable']) && $_POST['retry_times_enable'] == 1 ? true : false;
        $options['retry_limit'] = isset($_POST['retry_limit']) ? $_POST['retry_limit'] : '';
        $options['retry_lock_period'] = isset($_POST['retry_lock_period']) ? $_POST['retry_lock_period'] : '';
        $options['retry_connection'] = isset($_POST['retry_connection']) ? $_POST['retry_connection'] : '';
        */

        /* block the display of author archive page */
        $options['block_author_archive'] = isset($_POST['block_author_archive']) && $_POST['block_author_archive'] == 1 ? true : false;

        /* PINGBACK */
        $options['pingback_suppress_enable'] = isset($_POST['pingback_suppress_enable']) && $_POST['pingback_suppress_enable'] == 1 ? true : false;

        /* CAPTCHA */
        /* TODO Unimplemented
        $options['captcha_enable'] = isset($_POST['captcha_enable']) && $_POST['captcha_enable'] == 1 ? true : false;
        */

        if(count($messages->errors)>0){
            set_transient(BARB_SECURITY_SAVE_TRANSIENT, $messages, MINUTE_IN_SECONDS);
            return;
        }

        if(get_option(Version::$name, null) === null){
            add_option( Version::$name, $options, '', 'no' );
            $messages->add('info', __( 'registered', Version::$name ));
        }else{
            update_option( Version::$name, $options );
            $messages->add('info', __( 'updated', Version::$name ));
        }

        if($options['parameter_enable']){
            add_filter('login_url',array('barbsecurity\LoginParameter', 'addParameter'),1);
        }else{
            remove_filter('login_url',array('barbsecurity\LoginParameter', 'addParameter'),1);
        }

        set_transient(BARB_SECURITY_SAVE_TRANSIENT, $messages, MINUTE_IN_SECONDS);
    }
}
add_action('admin_init', 'barb_security_admin_init');


function barb_security_admin_notices(){
    $messages = get_transient(BARB_SECURITY_SAVE_TRANSIENT);
    $errors = !empty($messages->errors['error']) ? $messages->errors['error'] : array();
    $infos = !empty($messages->errors['info']) ? $messages->errors['info'] : array();

    foreach($errors as $error){
        echo "<div class='error'><p>{$error}</p></div>";
    }
    foreach($infos as $info){
        echo "<div class='updated'><p>{$info}</p></div>";
    }
}

