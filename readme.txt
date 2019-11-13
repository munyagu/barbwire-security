=== Barbwire Security ===
Contributors: munyagu
Donate link: http://munyagu.com/donate/
Tags: security,admin,Brute Force,admin rename,xmlrcp,rest api
Requires at least: 3.8
Tested up to: 5.3
Stable tag: 1.4.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



== Description ==

This plugin enhances the WordPress security.
Effective such as the brute force attack.
Includes the following specific functions.

1.Change the URL of the login screen, to avoid attacks on the login screen.
  You can ward off tying for try to login for cracking, such as Brute-force attack.
  Adding parameter to login URL so that default login url will hidden.

2.Block the display of author archive page
  WordPress leaks your login id because of redirect author archive page by author id to login id.
  (If you enter "your-site-url/?author=1", you can try it.)
  Simply hideing author archive page so that block to leak login id.

3.To disable the part of the XML-RCP feature prevents the attack.
  Block DDOS attacks against other sites with yor WordPress site, pingback enabled.

4.Disable the REST API function and reduce the risk of receiving external attacks.
  You can disable all REST APIs and you can partially disable them.

These features will be able to choose whether or not to enable.

This plug-in does not change the .htaccess
You can use with confidence.
Also it works in both Apache and nginx.



(photo by Keoni Cabral https://www.flickr.com/photos/keoni101/)
== Installation ==

1. Install and activate the plugin through the 'Plugins' menu in WordPress.
2. Go to Settings > Barbwire Security.
3. Perform the necessary settings and press the Save button.

== Screenshots ==

1. Setting

2. Setting

3. Help

== Changelog ==

= 1.4.6 =
update For WordPress 5.3

= 1.4.5.1 =
fix Fetal error.

= 1.4.5 =
fix Bug prevent access to password-protected content.

= 1.4.4 =
fix Error when the version of WordPress does not support REST API.

= 1.4.3 =
fix Error on setting screen in Version 4.6.x or earlier.

= 1.4.0 =
change Possible to finely set the restriction of REST API
change Move menu to submenu of option
fix Remove Notice Error in setting page
add Link to setting page to plugin list page
Refactored
Specify support for version 4.9

= 1.3.0 =
Skipped

= 1.2.1 =
fix settings page duplication
Specify support for version 4.8.2

= 1.2.0 =
Add new function, Disable the REST API
Refactor source codes

= 1.1.1 =
fix readme.txt

= 1.1.0 =
fix disnable pingback function was not working
add function block the display of author archive page
add help documentation

= 1.0.3 =
fix login page will divulge, when using Permalink settings
Thanks to @nyarocom pointed out.(https://wordpress.org/support/topic/login-page-will-divulge/)

= 1.0.2 =
fix php warning message

= 1.0.1 =
fix error error of removing the plugin

= 1.0.0 =
First release
