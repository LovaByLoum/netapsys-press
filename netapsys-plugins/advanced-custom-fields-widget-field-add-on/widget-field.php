<?php
/*
* Plugin Name: Advanced Custom Fields - Widget Field add-on
* Description: This plugin is an add-on for Advanced Custom Fields. It allows you to add widget field
* Author:      Johary Ranarimanana
* Version:     1.0
* Text Domain: acf
* Domain Path: /lang/
*/
if( !class_exists( 'ACF_WIDGET_Field' ) && class_exists( 'acf_Field' ) ) :
add_action('admin_init', 'acf_widget_object_admin_init');
function acf_widget_object_admin_init(){
  wp_enqueue_script('jquery-livequery',plugin_dir_url(__FILE__).'js/jquery.livequery.js');
  wp_enqueue_style('acf_widget_field_style',plugin_dir_url(__FILE__).'css/acf_widget.css');
  wp_enqueue_script('json2');
  wp_enqueue_script('acf_widget_field',plugin_dir_url(__FILE__).'js/acf_widget.js');
}  

//*API*//
function acf_register_widget($name,$callback){
	global $acf_widgets;
	$widget = new stdClass();
	$widget->slug = sanitize_title($name);
	$widget->name = $name;
	$widget->callback = $callback;
	if(is_null($acf_widgets)) $acf_widgets=array();
	$acf_widgets[$widget->slug] = $widget;
}

add_action('wp_ajax_acf_load_widget','acf_load_widget');
function acf_load_widget(){
	$val = $_POST['value'];
	global $acf_widgets;
	if(isset($acf_widgets[$val])){
		$widget = $acf_widgets[$val];
		if(is_array($widget->callback)){
			eval("{$widget->callback[0]}::{$widget->callback[1]}();");
		}else{
			eval("$widget->callback();");
		}
	}else{
		echo 'widget non trouvé';
	}
	die;
}

class acf_widget_object extends acf_Field
{
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'widget_object';
		$this->title = __("Widget",'acf');
		
   	}
   	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		global $acf_widgets;
		?>
		<div class="acf_widget_field_block">
			<input type="hidden" name="<?php echo $field['name'];?>" value="" class="acf_widget_true_value">
			<input type="hidden" name="<?php echo $field['name'];?>[type]" value="<?php if(isset($field['value']['type'])){echo $field['value']['type'];}?>" class="acf_widget_type">
			<table class="acf_input widefat acf_widget_container">
				<tbody>
					<?php if($acf_widgets && isset($field['value']) && !empty($field['value'])):
						$widget_type = $field['value']['type'];
						$widget = $acf_widgets[$widget_type];
						$data = $field['value']['data'];
						ob_start();
						if(is_array($widget->callback)){
							eval("{$widget->callback[0]}::{$widget->callback[1]}(\$data);");
						}else{
							eval("$widget->callback(\$data);");
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
	
	function create_options($key, $field)
	{	
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// get value
		$value = parent::get_value($post_id, $field);
		
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
	
	function update_value($post_id, $field, $value){
		
		$object = array();
		$object['type'] = $value['type'];
		unset($value['type']);
		$object['data'] = $value;
		
		parent::update_value($post_id, $field, $object);
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
endif;

/*
 * Advanced Custom Fields - URL Field Helper
 * 
 *
 */
if( !class_exists( 'ACF_WIDGET_Field_Helper' ) ) :
class ACF_WIDGET_Field_Helper {
	/*
	 * Singleton instance
	 * @var ACF_URL_Field_Helper
	 *
	 */
	private static $instance;
	
	/*
	 * Returns the ACF_URL_Field_Helper singleton
	 * 
	 * <code>$obj = ACF_URL_Field_Helper::singleton();</code>
	 * @return ACF_URL_Field_Helper
	 *
	 */
	public static function singleton() 
	{
		if( !isset( self::$instance ) ) 
		{
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
	
	/*
	 * Prevent cloning of the ACF_URL_Field_Helper object
	 * @internal
	 *
	 */
	private function __clone() 
	{
		
	}
	
	/*
	* WordPress Localization Text Domain
	*
	* Used in wordpress localization and translation methods.
	* @var string
	*
	*/
	const L10N_DOMAIN = 'acf-widget-field';
	
	/*
	 * Language directory path
	 * 
	 * Used to build the path for WordPress localization files.
	 * @var string
	 *
	 */
	private $lang_dir;
	
	/*
	 * Constructor
	 *
	 */
	private function __construct() 
	{
		$this->lang_dir = rtrim( dirname( realpath( __FILE__ ) ), '/' ) . '/lang';
		
		add_action( 'init', array( &$this, 'register_field' ),  5, 0 );
		add_action( 'init', array( &$this, 'load_textdomain' ), 2, 0 );
	}
	
	/*
	 * Registers the Field with Advanced Custom Fields
	 *
	 */
	public function register_field() 
	{
		if( function_exists( 'register_field' ) ) 
		{
			register_field( 'acf_widget_object', __FILE__ );
		}
	}
	
	/*
	 * Loads the textdomain for the current locale if it exists
	 *
	 */
	public function load_textdomain() 
	{
		$locale = get_locale();
		$mofile = $this->lang_dir . '/' . self::L10N_DOMAIN . '-' . $locale . '.mo';
		load_textdomain( self::L10N_DOMAIN, $mofile );
	}
}
endif; //class_exists 'ACF_WIDGET_Field_Helper'

//Instantiate the Addon Helper class
ACF_WIDGET_Field_Helper::singleton();
?>