<?php

class CListe {
  public static function admin_init(){
    add_filter('mce_external_plugins', array('CListe','addScriptTinymce'));
    add_filter('mce_buttons', array('CListe','addButtonToRegister'));
  }

  public static function addButtonToRegister($buttons){
    array_push($buttons, "|", "customListe");

    return $buttons;
  }

  public static function formulaireListe(){
    ob_start();
    ?>
    <p>Vous pouvez inserer un gabarit de liste correspondant au rendue ci-dessous : </p>
    <img src="<?php echo get_template_directory_uri();?>/inc/shortcode/js/images/liste-type.png" width="350">
    <br><br>
    <label>Veuillez spécifier le nombre de ligne à inserer : </label>
    <input type="text" id="nombre-ligne" name="nombre-ligne" value="">
    <input id="submit-liste"  type="submit" value="Inserer">
    <p>Vous pouvez ensuite remplacer le texte par défaut et appliquer differents styles de textes.</p>
    <?php
    $content = ob_get_contents();
    ob_clean();

    echo $content;
    die();
  }

  public static function addScriptTinymce($script){
    $script['customListe'] = get_template_directory_uri() . '/inc/shortcode/js/liste.js';

    return $script;
  }
}

add_action( 'wp_ajax_addShortcodeListe', array( 'CListe', 'formulaireListe' ) );
add_action( 'admin_init', array ('CListe', 'admin_init' ));