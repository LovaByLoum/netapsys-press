<?php
/**
 * Exemple de shortcode
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */
add_shortcode('document', 'mytheme_render_document_shortcode');
function mytheme_render_document_shortcode($attr){
  $list_id = explode(',', $attr['id']);
  ob_start();
  ?>

     <!-- do html here -->

    <?php

  $content = ob_get_contents();
  ob_clean();

  return $content;
}

