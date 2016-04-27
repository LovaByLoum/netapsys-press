<?php
/**
Plugin Name: Login Captcha
Plugin URI:
Version: v1.0
Author: Johary Ranarimanana
Description: Met en place un captcha pour le formulaire de login
 */

add_action( 'login_form', 'loc_login_display' );
function loc_login_display(){
  if( strpos(dirname(__FILE__), 'mu-plugins') ){
    $captcha_url = site_url() . '/wp-content/mu-plugins/captcha_source/captcha_code_file.php';
  }else{
    $captcha_url = plugins_url('captcha') . '/captcha_source/captcha_code_file.php';
  }
  ?>
  <p>
    <label for="user_pass">Code à recopier<br>
      <input type="password" name="captcha" id="captcha" class="input" value="" size="20">
      <img src="<?php echo $captcha_url; ?>" style="width:100%;">
    </label>
  </p>
  <?php
}

add_action( 'authenticate', 'loc_login_check', 21, 1 );
function loc_login_check( $user ) {
  @session_start();
  if ( isset($_POST['wp-submit']) ){
    if ( isset($_POST['captcha']) ) {
      if( $_POST['captcha'] == $_SESSION['captcha'] ){
        return $user;
      }else{
        return new WP_Error( 'captcha_error', 'Code à recopier non identique.' );
      }
    } else {
      return new WP_Error( 'captcha_error', 'Code à recopier obligatoire.' );
    }
  }else{
    return $user;
  }
}