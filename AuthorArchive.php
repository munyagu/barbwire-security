<?php
/**
 * Block showing author archive page.
 * For stop divulging user name.
 * refer:
 * http://www.authorsure.com/827/wordpress-username-security
 *
 */
namespace barbsecurity;


class AuthorArchive {

    /**
     * activate block showing author archive page.
     */
    public static function activete(){
        add_filter('parse_query', array('barbsecurity\AuthorArchive', 'blockAuthorArchive'),1 ,2 );
    }

    /**
     * Block showing author archive page.
     * @param $location url
     * @return mixed url or 404 exit
     */
    public static function blockAuthorArchive($location){
        if( ! is_admin() && is_author()){
            exit_404();
        }else{
            return $location;
        }
    }

}