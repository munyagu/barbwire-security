<?php

namespace barbsecurity;

/**
 * Class DisableXMLRPCPingBack
 * remove pingback functions
 * test command curl http://barbpack.barbwire.jp/xmlrpc.php -d '<methodCall><methodName>pingback.ping</methodName><params></params></methodCall>'
 */
class DisableXMLRPCPingBack {

	/**
	 * activate disnable xmlrpc ping-back and remove X-Pingback header
	 */
	public static function activate() {
		add_filter( 'xmlrpc_methods', array( 'barbsecurity\DisableXMLRPCPingBack', 'disnable_ping_back' ), 1, 1 );
		add_filter( 'wp_headers', array( 'barbsecurity\DisableXMLRPCPingBack', 'remove_XPingback_header' ), 1, 1 );
	}

	/**
	 * Disnable xmlrcp ping back
	 *
	 * @param array $methods An array of XML-RPC methods.
	 *
	 * @return mixed
	 */
	public static function disnable_ping_back( $methods ) {

		/* refer: http://sakuratan.biz/archives/1208 */
		/* refer:http://z9.io/2008/06/08/did-your-wordpress-site-get-hacked/ */
		unset( $methods['pingback.ping'] );
		unset( $methods['pingback.extensions.getPingbacks'] );

		return $methods;
	}

	/**
	 * Remove X-Pingback header
	 *
	 * @param array $headers The list of headers to be sent.
	 *
	 * @return mixed
	 */
	public static function remove_XPingback_header( $headers ) {

		unset( $headers['X-Pingback'] );

		return $headers;
	}
}