<?php
/**
 * Exemple de shortcode
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */
add_shortcode('document', 'beapi_render_document_shortcode');
function beapi_render_document_shortcode($attr){
  $list_id = explode(',', $attr['id']);
  ob_start();
  ?>

     <!-- do html here -->

    <?php

  $content = ob_get_contents();
  ob_clean();

  return $content;
}

