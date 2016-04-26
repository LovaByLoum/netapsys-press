<?php
/*
* Plugin Name: Advanced Custom Fields - URL Field add-on
* Description: This plugin is an add-on for Advanced Custom Fields. It allows you to choose URL between posts link and external link
* Author:      Johary Ranarimanana
* Version:     1.0
* Text Domain: acf
* Domain Path: /lang/
*/
if( !class_exists( 'ACF_URL_Field' ) && class_exists( 'acf_Field' ) ) :
add_action('admin_init', 'acf_url_object_admin_init');
function acf_url_object_admin_init(){
  wp_enqueue_script('livequery',plugin_dir_url(__FILE__).'js/jquery.livequery.js');
  wp_enqueue_script('json2');
  wp_enqueue_script('acf_url_field',plugin_dir_url(__FILE__).'js/acf_url.js');
}  

class acf_url_object extends acf_Field
{
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'url_object';
		$this->title = __("URL Object",'acf');
		
   	}
   	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// vars
		$args = array(
			'numberposts' => -1,
			'post_type' => null,
			'orderby' => 'title',
			'order' => 'ASC',
			'post_status' => array('publish', 'private', 'draft', 'inherit', 'future'),
			'suppress_filters' => false,
		);
		
		$defaults = array(
			'multiple'		=>	0,
			'post_type' 	=>	false,
			'taxonomy' 		=>	array('all'),
			'allow_null'	=>	0,
		);
		

		$field = array_merge($defaults, $field);
		
		
		// validate taxonomy
		if( !is_array($field['taxonomy']) )
		{
			$field['taxonomy'] = array('all');
		}
		
		// load all post types by default
		if( !$field['post_type'] || !is_array($field['post_type']) || $field['post_type'][0] == "" )
		{
			$field['post_type'] = $this->parent->get_post_types();
		}
		
		
		// create tax queries
		if( ! in_array('all', $field['taxonomy']) )
		{
			// vars
			$taxonomies = array();
			$args['tax_query'] = array();
			
			foreach( $field['taxonomy'] as $v )
			{
				
				// find term (find taxonomy!)
				// $term = array( 0 => $taxonomy, 1 => $term_id )
				$term = explode(':', $v); 
				
				
				// validate
				if( !is_array($term) || !isset($term[1]) )
				{
					continue;
				}
				
				
				// add to tax array
				$taxonomies[ $term[0] ][] = $term[1];
				
			}
			
			
			// now create the tax queries
			foreach( $taxonomies as $k => $v )
			{
				$args['tax_query'][] = array(
					'taxonomy' => $k,
					'field' => 'id',
					'terms' => $v,
				);
			}
		}
		
		
		// Change Field into a select
		$field['type'] = 'select';
		$field['choices'] = array();
		$field['optgroup'] = false;
		
		
		foreach( $field['post_type'] as $post_type )
		{
			// set post_type
			$args['post_type'] = $post_type;
			
			
			// set order
			if( is_post_type_hierarchical($post_type) && !isset($args['tax_query']) )
			{
				$args['sort_column'] = 'menu_order, post_title';
				$args['sort_order'] = 'ASC';

				$posts = get_pages( $args );
			}
			else
			{
				$posts = get_posts( $args );
			}
			
			
			if($posts)
			{
				foreach( $posts as $post )
				{
					// find title. Could use get_the_title, but that uses get_post(), so I think this uses less Memory
					$title = '';
					$ancestors = get_ancestors( $post->ID, $post->post_type );
					if($ancestors)
					{
						foreach($ancestors as $a)
						{
							$title .= '–';
						}
					}
					$title .= ' ' . apply_filters( 'the_title', $post->post_title, $post->ID );
					
					
					// status
					if($post->post_status != "publish")
					{
						$title .= " ($post->post_status)";
					}
					
					// WPML
					if( defined('ICL_LANGUAGE_CODE') )
					{
						$title .= ' (' . ICL_LANGUAGE_CODE . ')';
					}
					
					// add to choices
					if( count($field['post_type']) == 1 )
					{
						$field['choices'][ $post->ID ] = $title;
					}
					else
					{
						// group by post type
						$post_type_object = get_post_type_object( $post->post_type );
						$post_type_name = $post_type_object->labels->name;
					
						$field['choices'][ $post_type_name ][ $post->ID ] = $title;
						$field['optgroup'] = true;
					}
					
					
				}
				// foreach( $posts as $post )
			}
			// if($posts)
		}
		// foreach( $field['post_type'] as $post_type )
		
		//wp_enqueue_script('acf_url_field',plugin_dir_url(__FILE__).'js/acf_url.js');

		if(is_array($field['value']) && isset($field['value']['link'])){
			$value = $field['value']['link'];
			$label = $field['value']['label'];
			$field['value'] = $value;
		}else{
			$value = $field['value'];
			$label = ""; 
		}
		$internal= (is_numeric($field['value']) && strpos($field['value'],"http")==false);
		?>
		<div class="acf_url_field_block">
			<input type="hidden" name="<?php echo $field['name'];?>" value="" class="acf_url_true_value">
			<table class="acf_input widefat">
				<tbody>
					<tr>
						<td class="label"><label for="">URL</label></td>
						<td>
							<ul class="acf_url_field_choice radio_list radio horizontal">
								<li><label><input type="radio" name="choice_<?php echo $field['id'];?>[]" value="1" <?php if($internal):?>checked="checked"<?php endif;?>><?php echo __("Interne",'acf');?></label></li>
								<li><label><input type="radio" name="choice_<?php echo $field['id'];?>[]" value="0" <?php if(!$internal):?>checked="checked"<?php endif;?>><?php echo __("Externe",'acf');?></label></li>
							</ul>
							<div class="acf_url_field_internal">
								<?php
								// create field
								do_action('acf/create_field', $field );
								?>
							</div>
							<div class="acf_url_field_external">
								<?php
								echo '<input type="text" value="' . (($internal)?'http://':$value) . '" id="text-' . $field['id'] . '" class="' . $field['class'] . '" />
									 <span>Veuillez spécifier le http://</span>';
								?>
							</div>					
						</td>
					</tr>
					<tr>
						<td class="label"><label>Libellé</label></td>
						<td>
							<?php
							$b =  preg_match_all("#(\[.*?\])#",$field['name'],$matches);
							$newfieldname = "";
							$glue = "";
							$hierarchy_field = array();
							foreach ($matches[1] as $key => $fieldname) {
								if(strpos($fieldname,'field_')){
									$fieldname = trim($fieldname,'[]');
									$fieldinfo = $this->get_acf_field($fieldname);
									if($fieldinfo && isset($fieldinfo['name'])){
                    $hierarchy_field[] = $fieldname;
										$newfieldname.=$glue.$fieldinfo['name'];
									}elseif(!empty($hierarchy_field)){
                    $parentname = end($hierarchy_field);
                    $currentfieldinfo = $this->get_acf_field($parentname);
										$name = $currentfieldinfo['sub_fields'][$fieldname]['name'];
										$newfieldname.=$glue.$name;
									}
									$glue = "_";
								}else{
									$fieldname = trim($fieldname,'[]');
									$newfieldname.=$glue.$fieldname;
									$glue = "_";
								}
							}
							echo '<input type="text" class="acf_url_label" value="' . $label . '" />';
							?>
						</td>
					</tr>
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
		// defaults
		$defaults = array(
			'post_type' 	=>	'',
			'multiple'		=>	0,
			'allow_null'	=>	0,
			'taxonomy' 		=>	array('all'),
		);
		
		$field = array_merge($defaults, $field);

		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Post Type",'acf'); ?> (Lien interne)</label>
			</td>
			<td>
				<?php 
				
				$choices = array(
					''	=>	__("All",'acf')
				);
				$choices = array_merge( $choices, $this->parent->get_post_types() );
				
				
				do_action('acf/create_field', array(
					'type'	=>	'select',
					'name'	=>	'fields['.$key.'][post_type]',
					'value'	=>	$field['post_type'],
					'choices'	=>	$choices,
					'multiple'	=>	1,
				));
				
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Filter from Taxonomy",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$choices = array(
					'' => array(
						'all' => __("All",'acf')
					)
				);
				$choices = array_merge($choices, $this->parent->get_taxonomies_for_select());
				
				do_action('acf/create_field', array(
					'type'	=>	'select',
					'name'	=>	'fields['.$key.'][taxonomy]',
					'value'	=>	$field['taxonomy'],
					'choices' => $choices,
					'optgroup' => true,
					'multiple'	=>	1,
				));
				
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Allow Null?",'acf'); ?></label>
			</td>
			<td>
				<?php
				
				do_action('acf/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][allow_null]',
					'value'	=>	$field['allow_null'],
					'choices'	=>	array(
						1	=>	__("Yes",'acf'),
						0	=>	__("No",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				
				?>
			</td>
		</tr>
		<?php
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
		$object = stripslashes($value);
		$object = json_decode($object);
		if(isset($object->link)){
			$value = (array)$object;
		}
		parent::update_value($post_id, $field, $value);
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
if( !class_exists( 'ACF_URL_Field_Helper' ) ) :
class ACF_URL_Field_Helper {
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
	const L10N_DOMAIN = 'acf-url-field';
	
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
			register_field( 'acf_url_object', __FILE__ );
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
endif; //class_exists 'ACF_URL_Field_Helper'

//Instantiate the Addon Helper class
ACF_URL_Field_Helper::singleton();

?>