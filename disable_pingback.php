<?php
/*************** PINGBACK ***************/
/**
 * remove pingback functions
 * test command curl http://barbpack.barbwire.jp/xmlrpc.php -d '<methodCall><methodName>pingback.ping</methodName><params></params></methodCall>'
 */
function barb_security_remove_xmlrpc_methods($methods){
    BarbTool::bp_log("barb_security_remove_xmlrpc_methods");
    // refer: http://sakuratan.biz/archives/1208
    // refer:http://z9.io/2008/06/08/did-your-wordpress-site-get-hacked/
    unset( $methods['pingback.ping'] );
    unset( $methods['pingback.extensions.getPingbacks'] );
    return $methods;
}
if(isset($barb_security_options['pingback_suppress_enable']) && $barb_security_options['pingback_suppress_enable'] == true) {
    add_filter('xmlrpc_methods', 'barb_security_remove_xmlrpc_methods', 1, 1);
}

/**
 * remeve X-Pingback from HTTP header
 * @param $headers
 * @return mixed
 */
function barb_security_wp_headers( $headers ) {
    BarbTool::bp_log("barb_security_wp_headers");
    unset( $headers['X-Pingback'] );
    return $headers;
}
if(isset($barb_security_options['pingback_suppress_enable']) && $barb_security_options['pingback_suppress_enable'] == true) {
    add_filter('wp_headers', 'barb_security_wp_headers');
}