<?php
/**
 * Class DisableXMLRPCPingBack
 * remove pingback functions
 * test command curl http://barbpack.barbwire.jp/xmlrpc.php -d '<methodCall><methodName>pingback.ping</methodName><params></params></methodCall>'
 */
class DisableXMLRPCPingBack {

    /**
     * activate disnable xmlrpc ping-back and remove X-Pingback header
     */
    public static function activate(){
        add_filter('xmlrpc_methods', array('DisableXMLRPCPingBack', 'disnable_ping_back'), 1, 1);
        add_filter('wp_headers', array('DisableXMLRPCPingBack', 'remove_XPingback_header'), 1, 1);
    }

    /**
     * disnable xmlrcp ping back
     * @param $methods
     * @return mixed
     */
    public static function disnable_ping_back($methods){
        BarbTool::bp_log("disnable_ping_back");

        // refer: http://sakuratan.biz/archives/1208
        // refer:http://z9.io/2008/06/08/did-your-wordpress-site-get-hacked/
        unset( $methods['pingback.ping'] );
        unset( $methods['pingback.extensions.getPingbacks'] );
        return $methods;
    }

    /**
     * remove X-Pingback header
     * @param $headers
     * @return mixed
     */
    public static function remove_XPingback_header($headers){
        BarbTool::bp_log("remove_XPingback_header");

        unset( $headers['X-Pingback'] );
        return $headers;
    }
}