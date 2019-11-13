<?php




$result = preg_match( '/\/wp-login.php.+?action=confirm_admin_email/', '/wp-login.php?action=confirm_admin_email', $matches );

var_dump($result);
var_dump($matches);
