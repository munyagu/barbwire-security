=== Barbwire Security ===
Contributors: munyagu
Donate link: http://munyagu.com/donate/
Tags: security,admin
Requires at least: 3.8
Tested up to: 4.6.1
Stable tag: 1.1.0
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


These features will be able to choose whether or not to enable.


This plug-in does not change the .htaccess, you can use with confidence.


[For Japanese users.]

このプラグインはワードプレスのセキュリティ機能を向上させます。
ブルートフォースアタックのような攻撃に対して特に効果的です。
以下のような機能を有しています。

１．ログイン画面のURLにパラメータを付与し、ログイン画面への攻撃を防ぎます。
　　/wp-adminへのアクセスもログイン画面にリダイレクトされなくなります。
２．XML-RCP機能の一部を無効にし、この機能を利用したログインや改竄を防ぎます。

これらの機能は有効/無効を選択することができます。

このプラグインは.htaccessファイルを書き換えませんので、安心してお使いいただけます。

※XRPCの一部を無効にする機能でアプリなどからの投稿ができなくなった場合、この機能のみを無効にしてください。
※Simple WordPress Membershipなどのログイン機能を提供している場合に、WordPressのログアウト画面を利用しているとログアウト画面のURLがパラメータを付与したURLになり、ログイン画面URL漏洩の原因になります。
　そのような機能をお使い場合、一般ユーザーにはAdminbarを表示しないなど、プラグインが提供しているか、独自のログアウト画面を利用するように設定ください。



(photo by Keoni Cabral https://www.flickr.com/photos/keoni101/)
== Installation ==

1. Install and activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Setting

== Changelog ==

= 1.0.0 =
First release.

= 1.0.1 =
fix error error of removing the plugin.

= 1.0.2 =
fix php warning message.

= 1.0.3 =
fix login page will divulge, when using Permalink settings.
Thanks to @nyarocom pointed out.(https://wordpress.org/support/topic/login-page-will-divulge/)

= 1.1.0 =
fix disnable pingback function was not working.
add function block the display of author archive page.
add help documentation.