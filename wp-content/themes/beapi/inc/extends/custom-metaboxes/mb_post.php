<?php
/**
 * Exemple de champs personnalisés custom
 *
 * @package WordPress
 * @subpackage beapi
 * @since beapi 1.0
 * @author : Netapsys
 */

/**
 * Initialisation
 */
add_action('add_meta_boxes','beapi_init_metabox_post');
function beapi_init_metabox_post(){
  add_meta_box('mb_note_post', 'Note', 'beapi_field_note_post', 'post', 'normal');
}

/**
 * Ajout des champs
 */
function beapi_field_note_post($post){
  echo '<select name="' . FIELD_POST_POST_NOTE . '">';
  $notes = range(1,5);
  $selected_value = get_post_meta( $post->ID, FIELD_POST_POST_NOTE, true );
  foreach ( $notes as $note ) {
    echo '<option value="' . $note . '" ' . ( $selected_value==$note ? ' selected ' : '' ) . '>' . $note . '</option>';
  }
  echo '</select>';
}

/** sauvegarde valeur champ */
add_action( 'save_post', 'beapi_save_posts_mb_post_value', 10, 2);
function beapi_save_posts_mb_post_value( $post_id, $post ){
  if ( is_admin() && $post->post_type == POST_TYPE_ARTICLE && isset($_POST[FIELD_POST_POST_NOTE]) ){
    update_post_meta($post->ID, FIELD_POST_POST_NOTE, intval($_POST[FIELD_POST_POST_NOTE]) );
  }
}
