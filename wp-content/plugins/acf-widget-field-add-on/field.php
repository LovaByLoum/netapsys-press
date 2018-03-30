<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Johary
 * Date: 30/06/16
 * Time: 14:47
 * To change this template use File | Settings | File Templates.
 */

if ( class_exists( 'acf_field' ) ){
  class acf_widget_object extends acf_field
  {

    function __construct()
    {
      // vars
      $this->name = 'widget_object';
      $this->label = __( "Widget", 'acf' );
      $this->category = __( "Choice", 'acf' );
      parent::__construct();
    }


  /*--------------------------------------------------------------------------------------
  *
  *	create_field
  *
  *
  *-------------------------------------------------------------------------------------*/

    function create_field( $field )
    {
      global $acf_widgets;
      ?>
      <div class="acf_widget_field_block">
        <input type="hidden" name="<?php echo $field['name'];?>" value="" class="acf_widget_true_value">
        <input type="hidden" name="<?php echo $field['name'];?>[type]" value="<?php if(isset($field['value']['type'])){echo $field['value']['type'];}?>" class="acf_widget_type">
        <table class="acf_input widefat acf_widget_container">
          <tbody>
          <?php if( $acf_widgets && isset($field['value']) && !empty($field['value']) ):
            $widget_type = $field['value']['type'];
            $widget = $acf_widgets[$widget_type];
            $data = $field['value']['data'];
            ob_start();
            if(is_array($widget->callback)){
              call_user_func("{$widget->callback[0]}::{$widget->callback[1]}", $data);
            }else{
              call_user_func("{$widget->callback}", $data);
            }

            $output = ob_get_contents();
            ob_get_clean();
            $output = preg_replace('/name="(.*?)"/','name="'.$field['name'].'[$1]"',$output);
            echo $output;
          elseif($acf_widgets && !empty($acf_widgets)):?>
            <tr>
              <td class="label"><label>Type de widget</label></td>
              <td>
                <select class="choice_widget" style="width:90%;">
                  <option calue="0">Aucun</option>
                  <?php foreach ($acf_widgets as $widget):?>
                    <option value="<?php echo $widget->slug;?>"><?php echo $widget->name;?></option>
                  <?php endforeach;?>
                </select>
                <img class="acf_loading" src="<?php echo plugin_dir_url(__FILE__);?>/images/loading.gif">
              </td>
            </tr>
          <?php else:?>
            <tr><td class="label"><label>Aucun widget enregistré.</label></td></tr>
          <?php endif;?>
          </tbody>
        </table>
      </div>

    <?php

    }


    /*--------------------------------------------------------------------------------------
    *
    *	create_options
    *
    *
    *-------------------------------------------------------------------------------------*/

    function create_options( $field )
    {

    }


    /*--------------------------------------------------------------------------------------
    *
    *	get_value_for_api
    *
    *
    *-------------------------------------------------------------------------------------*/

    function format_value_for_api($value, $post_id, $field)
    {
      // no value?
      if( !$value )
      {
        return false;
      }


      // null?
      if( $value == 'null' )
      {
        return false;
      }


      // external / internal
      if(is_array($value) && isset($value['link'])){
        $post_id = $value['link'];
        if(is_numeric($post_id)){
          $post = get_post($post_id);
          $url = get_permalink($post->ID);
          $value['link']= $url;
          if(empty($value['label'])){
            $label = $post->post_title;
            $value['label']=$label;
          }
        }
      }


      // return the value
      return $value;
    }

    /*--------------------------------------------------------------------------------------
    *
    *	update_value
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

    function update_value( $value, $post_id, $field){

      $object = array();
      $object['type'] = $value['type'];
      unset($value['type']);
      $object['data'] = $value;

      return $object;
    }

    //************** get field name by key ***********
    function get_acf_field($fieldkey)
    {
      // vars
      global $wpdb;


      // get field from postmeta
      $result = $wpdb->get_var( $wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s",$fieldkey ));

      if( $result )
      {
        $result = maybe_unserialize($result);
        return $result;
      }


      // return
      return false;

    }

  }
  new acf_widget_object();
}
