<?php
/**
 * login form customisation
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */

add_action('login_head', 'beapi_logo_head');
function beapi_logo_head(){
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
