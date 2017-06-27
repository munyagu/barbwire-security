=== Barbwire Security ===
Contributors: munyagu
Donate link: http://munyagu.com/donate/
Tags: security,admin
Requires at least: 3.8
Tested up to: 4.8
Stable tag: 1.2.1
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
  The REST API function is a feature not used by most users.
  If you do not recognize using it, let's disable it.

These features will be able to choose whether or not to enable.


This plug-in does not change the .htaccess, you can use with confidence.


[For Japanese users.]

このプラグインはワードプレスのセキュリティ機能を向上させます。
ブルートフォースアタックのような攻撃に対して特に効果的です。
以下のような機能を有しています。

１．ログイン画面のURLにパラメータを付与し、ログイン画面への攻撃を防ぎます。
  ブルートフォースアタックのような、あなたのサイトをクラックするためのログインの試行を避けることができます。
  Adding any parameter to login URL so that login screen will hidden.
  /wp-adminへのアクセスもログイン画面にリダイレクトされなくなります。

２．XML-RCP機能の一部を無効にし、この機能を利用したログインや改竄を防ぎます。
  pingbackを無効化することで、他のサイトへのDDOS攻撃の踏み台にされることを防ぎます。

３．作成者アーカイブを表示しないようにし、ログインIDの漏洩を防ぎます。
  作成者IDがセットされたURLからログインIDがセットされたURLにリダイレクトされることで、ログインIDが漏洩する可能性があります。
  サイトURLに/?author=1を付加することで、ログインIDがURLに表れるかどうかを試すことができます。
  単純に作成者アーカイブページを表示しないことで、ログインIDの漏洩を防ぎます。

４．REST API機能を無効にし、外部から攻撃を受けるリスクを低減します。
  REST API機能は、ほとんどのユーザーには使われていない機能です。
  もし利用している認識がないのなら、無効にしてみましょう。


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

= 1.1.1 =
fix readme.txt

= 1.2.0 =
Add new function, Disable the REST API.
Refactor source codes.

= 1.2.1 =
fix settings page duplication.