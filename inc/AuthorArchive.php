<?php

/**
 * Block showing author archive page.
 * For stop divulging user name.
 * refer:http://www.authorsure.com/827/wordpress-username-security
 */

namespace barbsecurity;

/**
 * Class AuthorArchive
 * For disable author archive page
 *
 * @package barbsecurity
 */
class AuthorArchive {

	/**
	 * Activate block showing author archive page.
	 */
	public static function activete() {
		add_filter( 'parse_query', array( 'barbsecurity\AuthorArchive', 'block_author_archive' ), 1, 2 );
	}

	/**
	 * Block showing author archive page.
	 *
	 * @param string $location url.
	 *
	 * @return mixed url or 404 exit
	 */
	public static function block_author_archive( $location ) {
		if ( ! is_admin() && is_author() ) {
			exit_404();
		} else {
			return $location;
		}
		return '';
	}

}