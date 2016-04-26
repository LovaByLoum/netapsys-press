<?php
/**
Plugin Name: WP Widget Repeater
Description: Classes utilitaires pour la création de widget repeteur
Author: Netapsys
Version: 1.0
*/

class wp_repeater_widget
{

	/**
	 * Database identifier for the plugin options.
	 *
	 * @var string
	 */
	public  $option_db_id ;

	/**
	 * Number display item.
	 *
	 * @var integer
	 */
	public  $nb_display_item = 3;

  /**
   * Database identifier for widget.
   *
   * @var string
   */
  public  $id ;

  /**
   * label
   *
   * @var string
   */
  public  $labels ;
  /**
   * if true field repeater
   *
   * @var boolean
   */
  private $repeater;

	/**
	 * Install plugin actions and/or filters hooks in the constructor
	 * @return void
	 */
	function __construct(
    $optionname ,
    $id,
    $labels = array(
      "widget_title"=>"Widget repeteur",
      "widget_item_label"=>"item"
    ) ,
    $fields = array(
      "URL image"=>"image",
      "Texte"=>"text",
      "Sélection"=>"select",
      "Zone de texte"=>"textarea",
    ) ,
    $nb_display_item = 3,
    $repeater = true
  ){
      $this->id = $id;
      $this->option_db_id = $optionname;
      $this->nb_display_item = $nb_display_item;
      $this->labels = $labels;
      $this->fields = $fields;
      $this->repeater = $repeater;
      add_action('init', array(&$this, 'init'));
      add_filter('admin_enqueue_scripts',array(&$this, 'admin_enqueue_scripts'));
	}

	/**
	 * Registers wp_repeater_widget as a sidebar widget.
	 * If the gm-init plugin is not activated, the widget is not registered.
	 * @return void
	 * @uses gm-init plugin
	 */
	function init(){

        $widget_options = array('classname'=>'wp_repeater_widget',
                                'description'=> $this->labels["widget_title"]);

        wp_register_sidebar_widget($this->id, $this->labels["widget_title"],
                                    array(&$this, 'widget_content'),
                                    $widget_options);

        wp_register_widget_control( $this->id, $this->labels["widget_title"], array(&$this, 'widget_control'));

        if(is_admin()) {
            wp_register_script( 'wp_repeater_widget',  plugins_url('js/widget_repeater.js', __FILE__));
            wp_enqueue_script( 'wp_repeater_widget' );
        }
	}

    /**
     * Add the loading of needed javascripts for admin part.
     *
     */
    function admin_enqueue_scripts() {
        if(is_admin()) {
            // chargement des styles
            wp_register_style('thickbox-css', '/wp-includes/js/thickbox/thickbox.css');
            wp_enqueue_style('thickbox-css');
            // Chargement des javascripts
            wp_enqueue_script('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('quicktags');
        }
    }

	/**
	 * Adds options to the widget
	 */
    function widget_control() {

        $options = get_option($this->option_db_id);

        if(isset($_POST["wr_widget_repeater_nb_item"])) {
            $options["nb_item"] = $_POST["wr_widget_repeater_nb_item"];
        }

        if(!empty($options["nb_item"]) && $this->nb_display_item != $options["nb_item"])
            $this->nb_display_item = $options["nb_item"];

		$flux = '';
        $flux.= '<input type="hidden" name="wr_widget_repeater_nb_item" id="wr_widget_repeater_nb_item" value="'.$this->nb_display_item.'">';
        for($cpt=1;$cpt<=$this->nb_display_item;$cpt++) {

            if(isset($_POST["wr_widget_repeater_nb_item"])) {
                $options = $this->saveField($cpt,$options);
            }
            $flux.= '<fieldset style="border:1px dotted #DFDFDF;padding:5px;">';
            if($this->repeater){
              $flux.= '<legend style="padding : 0 10px;"><span>'.$this->labels["widget_item_label"].'</span> n°'.($cpt);
                $flux.='<a class="wr_del_item" style="background: url(images/xit.gif) no-repeat -10px 0px;padding:0 3px;margin-left:5px;cursor:pointer;">&nbsp;</a>';
              $flux.= '</legend>';
            }
            $flux .= '<input type="hidden" class="wr_item_numero" value="'. $cpt .'"/>';

            $flux .= $this->createField($cpt);

            $flux.= '</fieldset>';
        }
      if($this->repeater)
        $flux.='<a href="#" title="Ajouter ' . $this->labels["widget_item_label"]. '" class="wr_add_item">Ajouter ' . $this->labels["widget_item_label"]. '</a>';

      update_option($this->option_db_id, $options);

      echo $flux;
    }

    /**
	 * Echoes the widget content
	 */
	function widget_content(){
        $options = get_option($this->option_db_id);
        do_action('widget_repeater_content_'.$this->id , $options);
  }

    /*createfield*/
  function createField($cpt){
    $options = get_option($this->option_db_id);
    $options = maybe_unserialize($options);
    $html = "";
    foreach ( $this->fields as $name => $field) {
      $key = sanitize_title($name);
      switch($field){
        case 'text' :
          $html .= '<label style="font-weight:bold">'.$name.'</label><br /><input type="text" name="wp_repeater_widget_'.$key.'_'.$cpt.'" value="'.$options["{$key}_{$cpt}"].'" style="width:98%;"/>';
          break;
        case 'select' :
          $html .= '<label style="font-weight:bold">'.$name.'</label><br /><select name="wp_repeater_widget_'.$key.'_'.$cpt.'"></select>';
          break;
        case 'texarea' :
          $html .= '<label style="font-weight:bold">'.$name.'</label><br /><textarea name="wp_repeater_widget_'.$key.'_'.$cpt.'">'.$options["{$key}_{$cpt}"].'</textarea>';
          break;
        case 'image' :
          $html .= '<div><label style="font-weight:bold">'.$name.'</label><a class="wr_widget_repeater_image_field" href="media-upload.php?TB_iframe=true&amp;type=image&amp;tab=library&amp;height=500&amp;width=640" title="Ajouter une image"><img src="images/media-button-image.gif" alt="Ajouter une image" style="margin-left:5px;" /></a><br /><input type="text" name="wp_repeater_widget_'.$key.'_'.$cpt.'"  value="'.$options["{$key}_{$cpt}"].'" style="width:98%;"/>
            <img class="preview" src="'.$options["{$key}_{$cpt}"].'" width="210" alt="Image">
            </div>';
          break;
        }
    }
    return $html;
  }

  function saveField($cpt,$options){
    foreach ( $this->fields as $name => $field) {
      $key = sanitize_title($name);
      $options["{$key}_{$cpt}"] = $_POST["wp_repeater_widget_{$key}_{$cpt}"];
    }
    return $options;
  }

}

?>