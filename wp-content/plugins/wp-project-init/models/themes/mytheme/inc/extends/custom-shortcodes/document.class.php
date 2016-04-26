<?php

add_shortcode('document', 'mytheme_render_document_shortcode');
function mytheme_render_document_shortcode($attr){
  $list_id = explode(',',$attr['id']);
  ob_start();
  ?>

     <!-- do html here -->

    <?php

  $content = ob_get_contents();
  ob_clean();

  return $content;
}

