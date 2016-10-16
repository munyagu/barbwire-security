<?php
namespace barbsecurity;

include_once dirname(__FILE__).'/barbwire-security.php';

class Version {

	public static $name = 'barbwire-security';

	public static function getVersion(){
		$ver = BARB_SECURITY_VERSION;
		return $ver;
	}	
}
