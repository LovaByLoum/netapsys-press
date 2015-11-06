<?php

class CLayer {
  public static function admin_init(){
    add_filter('mce_external_plugins', array('CLayer','addScriptTinymce'));
    add_filter('mce_buttons', array('CLayer','addButtonToRegister'));
  }

  public static function addButtonToRegister($buttons){
    array_push($buttons, "|", "customLayer");

    return $buttons;
  }

  public static function formulaireLayer(){
    ob_start();
    ?>
    <p>Vous pouvez inserer du texte au dessus d'une image correspondant au rendue ci-dessous : </p>
    <img src="<?php echo get_template_directory_uri();?>/inc/shortcode/js/images/layer-type.png" width="100">
    <p>Séléctionner et remplacer ensuite le texte inseré et appliquer differents styles sur le texte.</p>
    <br>
    <input id="submit-liste"  type="submit" value="Inserer">
    <?php
    $content = ob_get_contents();
    ob_clean();

    echo $content;
    die();
  }

  public static function addScriptTinymce($script){
    $script['customLayer'] = get_template_directory_uri() . '/inc/shortcode/js/layer.js';

    return $script;
  }
}

add_action( 'wp_ajax_addShortcodelayer', array( 'CLayer', 'formulaireLayer' ) );
add_action( 'admin_init', array ('CLayer', 'admin_init' ));