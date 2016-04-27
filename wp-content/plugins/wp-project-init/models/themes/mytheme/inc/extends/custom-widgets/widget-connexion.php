<?php
/**
 * Exemple de widget
 *
 * @package WordPress
 * @subpackage mytheme
 * @since mytheme __WPI__THEME__VERSION__
 * @author : __WPI__THEME__AUTHOR__
 */

class WP_Widget_Connexion extends WP_Widget{
  function __construct() {
    parent::WP_Widget(FALSE, 'Connexion');
  }

  //fonction d'affichage dans le BO
  function form($instance){
    $title = esc_attr( $instance['title'] );
    ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
    <p>
    <?php
  }

  //fonction de mis à jour
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }

  //function de rendu en front
  public function widget($args, $instance) {
    global $current_user;

    $title =  $instance['title'] ;

    echo $args['before_widget'];
    if ( $title ) {
      echo $args['before_title'] . $title . $args['after_title'];
    }
    if( is_user_logged_in() ):?>
      <?php echo __( 'Bonjour ', 'mytheme') . $current_user->data->display_name;?>
    <?php else:?>
      <?php wp_login_form(); ?>
    <?php endif;
    echo $args['after_widget'];
  }
}

register_widget( 'WP_Widget_Connexion' );
?>
