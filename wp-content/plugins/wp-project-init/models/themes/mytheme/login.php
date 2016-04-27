<?php
/**
 * login form customisation
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

add_action('login_head', 'mytheme_logo_head');
function mytheme_logo_head(){
  echo '
  <link rel="shortcut icon" href="' . get_template_directory_uri(). '/images/favicon.ico" type="image/x-icon" />
  <style>
			.login h1 a {
				background-image: url("' . get_template_directory_uri().  '/images/logo_admin.png");
				background-size: contain;
				background-position: top center;
				background-repeat: no-repeat;
				width: auto;
				height: 100px;
				text-indent: -9999px;
				overflow: hidden;
				padding-bottom: 15px;
				display: block;
				}
				.login #nav a, .login #backtoblog a {
          color: #000;
        }
		</style>';
}
