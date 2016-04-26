<?php
/*
Plugin Name: WP Infinite Loading
Version: 1.1
Description: Tools and utils for loading data list infinitely via ajax, include filters and sorting actions
Author: Johary
Text Domain: wp-infinite-loading
Domain Path: /lang
*/

/* EXAMPLE OF USE
	
	$object = new WP_Infinite_Loading('product-infinite-box');
    //configuration
    //set list or table view
    $object->setListView('list'); 
    //number of item on first load
    $object->setItemNumberOnLoad(3);
    //number of item on clicking load button
    $object->setItemNumberToLoad(5);
    //container class
    $object->setContainerClasses('wrapper');
    //item classes
    $object->setItemClasses('test');
    //id an template of infinite load button
    $object->setInfiniteLoadButton(
    	array(
    		'id'=>'load-more',
    		'tpl'=>'<div class="footer" id="load-more"><a href="javascript:;" class="more-link">&nbsp;</a></div>'
    	),
    	array(
    		'id'=>'no-load-more',
    		'tpl'=>'<div id="no-load-more" class="footer-hide-button"></div>'
    	)
    );
    //set function callback for customize item template
    $object->setRenderItemCallback(array(CProduct, 'renderItemCallback'));
    //set function callback for getting items by offset, limit, filter and sort
    $object->setGetItemsCallback(array(CProduct, 'getItemsCallback'));
    //add class to retrieve sorting element
    $object->addSorting('product-sort-date');
    //add class to retrieve filter element
    $object->addFilter('product-filtre-category');
    
    //display infinite loading box
    $object->displayItems();
    //display the infinite loading button
    $object->displayInfiniteLoadButton();
*
*
* see other functions :  setOnLoadSorting, setOnLoadFilter, removeMask, removeFirstMask, setSuccessDelay
*
*
*/

class WP_Infinite_Loading{
	
	// infinite load box id
	public $id;
	//cleanid for function concatenation
	public $clean_id;
	public $clean_js_id;
	// list or table view
	public $list_view = 'other';
	//number of items to load when click on the infinite load button
	public $item_number_to_load = 3;
	//number of items on first display
	public $item_number_on_load = 5;
	//item classes
	public $item_classes;
	//do animation
	public $do_anim = true;
	public $do_first_anim = true;
  //container classes
  public $container_classes;
	//sorting
	public $sorting_datas = array();
	public $on_load_sort_value = array();
	//extraargs
	public $extra_args = array();
	//filters data
	public $filters_datas = array();
	public $on_load_filter_value = array();
	//function callback to get items, function must return array of id
	public $_getItemsCallback;
	//function callback for customize item rendering, the functuion must return html (no printing)
	public $_renderItemCallback;
	public $success_delay = 500;
	//infinite button id
	private $_infinite_button_id = 'load-more';
	//infinite button template
	private $_infinite_button_tpl = '<a href="javascript:;" id="load-more">Load more</a>';
	//infinite button id
	private $_infinite_button_hide_id = 'no-data';
	// template id button hide
	private $_infinite_button_hide_tpl = '<p id="no-data">No more data</p>';
	//required for displaying infinite load button on first load, if items less than limit
	private $_display_infinite_load_button = true;

    //message not found element
    public $messageNotFound = '<p id="wpil-no-results">Aucun résultat</p>';
	
	public function __construct($id){
		$this->id = $id;
		$this->clean_id = str_replace('-','_',$id);
		$this->clean_js_id = strtoupper($this->clean_id);
		add_action("wp_footer",array($this, 'getScript'));
	}
	
	//set list type view
	public function setListView($view = 'other'){
		if(in_array($view,array('list','table'))){
			$this->list_view = $view;
		}else{
			$this->list_view = 'other';
		}
	}
	
	//set item classes
	public function setItemClasses($classes){
		$this->item_classes = $classes;
	}
	
	//set extra args
	public function setExtraArgs($args){
		$this->extra_args = $args;
	}

  //set container classes
  public function setContainerClasses($classes){
    $this->container_classes = $classes;
  }

  //set number item to load
	public function setItemNumberToLoad($value){
		$this->item_number_to_load = intval($value);
	}
	//set number item on load
	public function setItemNumberOnLoad($value){
		$this->item_number_on_load = intval($value);
	}


	//set number item on load
	public function setSuccessDelay($value){
		$this->success_delay = intval($value);
	}
	
	//don't do animation
	public function removeMask(){
		$this->do_anim = false;
	}
	//don't do first animation
	public function removeFirstMask(){
		$this->do_first_anim = false;
	}
	
	//set on load sorting data
	public function setOnLoadSorting($order, $orderby){
		$this->on_load_sort_value = array(
			'order' => $order,
			'orderby' => $orderby
		);
	}
	
	//set on load filter data
	public function setOnLoadFilter($filters){
		$this->on_load_filter_value = $filters;
	}
	
	//render data list with infinite loading box
	public function displayItems(){
		//get on load sort/filter value
		$sortings = !empty($this->on_load_sort_value)?$this->on_load_sort_value:null;
		$filters = !empty($this->on_load_filter_value)?$this->on_load_filter_value:null;
		
		$class = '';
	    if($this->do_first_anim){
	    	$class = 'loading anim';
	    }
	    
		//get items
		$results = $this->getItems( 0, $this->item_number_on_load, $filters, $sortings);
		extract($results);
		if($this->list_view == 'list'){
			$html = '<ul id="wpil_item_container_'.$this->id.'" class="' . $this->container_classes .'">'.$html.'</ul>';
		}elseif ($this->list_view == 'table'){
			$html = '<tbody id="wpil_item_container_'.$this->id.'" class="' .$this->container_classes .'">'.$html.'</tbody>';
	    }else{
	      $html = '<div id="wpil_item_container_'.$this->id.'" class="' . $this->container_classes .'">'.$html.'</div>';
	    }
	    
	    if ($this->list_view == 'table'){
	    	echo $html;
	    }else{
			echo '<div class="infinite-loading-mask '.$class.'">' . $html . '</div>';
	    }
	}
	
	//get items
	public function getItems($offset, $limit, $filters,$sorting){
		//get items
		if($this->_getItemsCallback){
			add_filter('wpil_get_items_'.$this->clean_id,$this->_getItemsCallback,10,5);
		}else{
			add_filter('wpil_get_items_'.$this->clean_id,array($this,'defaultGetItemsCallback'),10,5);
		}
		$results = apply_filters('wpil_get_items_'.$this->clean_id,$offset, $limit, $filters,$sorting, $this->extra_args);
		
		if(isset($results['posts']) && isset($results['count'])){
			$item_ids = $results['posts'];
			$count = $results['count'];
			$hidebuttn = ($offset+$limit)>=$count;
		}else{
			$item_ids = $results;
			$count = sizeof($item_ids);
			$hidebuttn = $count<$limit;
		}
		
		//calculate the first displaying status of the infinite load button
		if($hidebuttn){
			$this->_display_infinite_load_button = false;
		}
		
		$html = '';
		if(!empty($item_ids)){
			foreach ($item_ids as $id) {
				$html.= $this->displayItem(intval($id));	
			}
		}
		
		return array(
			'count' => $count,
			'html' => $html
		);
	}
	
	//render infinite load button
	public function displayInfiniteLoadButton(){
		echo $this->_infinite_button_tpl;
		echo $this->_infinite_button_hide_tpl;
	}
	
	//set infinite load button infoand template
	public function setInfiniteLoadButton( $template = null, $template_button_hide = null){
		if($template && isset($template['id']) && isset($template['tpl'])){
			$this->_infinite_button_id = $template['id'];
			$this->_infinite_button_tpl = $template['tpl'];
		}
		if($template_button_hide && isset($template_button_hide['id']) && isset($template_button_hide['tpl'])){
			$this->_infinite_button_hide_id = $template_button_hide['id'];
			$this->_infinite_button_hide_tpl = $template_button_hide['tpl'];
		}
	}
	
	//add sorting configuration, the element must have data-order and data-orderby attributes
	public function addSorting($className){
   array_push($this->sorting_datas, $className);
	}
	
	//add filter configuration, the element must have data-filter and data-filterby attributes
	public function addFilter($className){
		array_push($this->filters_datas, $className);
	}

    //set message is not found
    public function setMessageNotFound($value){
        $this->messageNotFound = addslashes($value);
    }
	
	//display an item
	private function displayItem($id){
		if($this->_renderItemCallback){
			add_filter('wpil_display_item_'.$this->clean_id ,$this->_renderItemCallback);
		}else{
			add_filter('wpil_display_item_'.$this->clean_id, array($this,'defaultRenderItemCallback'));
		}
		$html = apply_filters('wpil_display_item_'.$this->clean_id,$id);
		
		$classes = ($this->item_classes)?' class="wpil_item '.$this->item_classes.'" ':' class="wpil_item" ';
		if($this->list_view == 'list'){
			$html = '<li '.$classes.'>'.$html.'</li>';
		}elseif($this->list_view == 'table'){
			$html = '<tr '.$classes.'>'.$html.'</tr>';
    }else{
      //no container
    }
		return $html;
	}

	//set a function callback to get items, function must return array of id
	public function setGetItemsCallback($function){
		$this->_getItemsCallback = $function;
	}
	
	//default getItemsCallback
	public function defaultGetItemsCallback($offset, $limit, $filters,$sorting ){
		$args = array(
			'post_type' => 'any',
			'post_status' => 'publish',
			'numberposts' => $limit,
			'offset' => $offset,
			'order'=> isset($sorting['order'])?$sorting['order']:'date',
			'orderby' => isset($sorting['orderby'])?$sorting['orderby']:'DESC',
			'fields' => 'ids'
		);
		$posts = get_posts($args);
		return $posts;
	}
	
	//set function callback for customize item rendering, the functuion must return html (no printing)
	public function setRenderItemCallback($function){
		$this->_renderItemCallback = $function;
	}
	
	//default RenderItemCallback
	public function defaultRenderItemCallback($id){
		$post = get_post(intval($id));
		if($this->list_view == 'list'){
			$html = '<div>
						<a href="'.get_permalink($post->ID).'">
							<h3>' . $post->post_title .'</h3>
						</a>
						<p>' . (empty($post->post_excerpt)?substr(strip_tags(strip_shortcodes($post->post_content)),0,200).'...':$post->post_excerpt) .'</p>
					</div>'; 
		}else{
			$html = '<td>
						<a href="'.get_permalink($post->ID).'">
							<h3>' . $post->post_title .'</h3>
						</a>
					</td>
					<td>
						<p>' . (empty($post->post_excerpt)?substr(strip_tags(strip_shortcodes($post->post_content)),0,200).'...':$post->post_excerpt) .'</p>
					</td>'; 
		}
		return $html;
	}
	
	//ajax callback for loading more items
	public static function  ajax_request(){
		$object = $_REQUEST["object"];
		$object = $object;
		$infinite_load_object = new WP_Infinite_Loading($object->id);
		$infinite_load_object->loadFromObject($object);
		
		$sortings = array();
		$sortings['order'] = $_REQUEST["order"];
		$sortings['orderby'] = $_REQUEST["orderby"];
		
		$filters = $_REQUEST["filters"];

		$results = $infinite_load_object->getItems( $_REQUEST["offset"], $_REQUEST["limit"], $filters,$sortings);
		extract($results);
		$results = new stdClass();
		$results->end = !$infinite_load_object->_display_infinite_load_button;
		$results->count = $count;
		$results->items = $html;
		echo json_encode($results);die();
	}
	
	//load from object
	public function loadFromObject($object){
		foreach ($object as $propriety => $value) {
			$this->$propriety = $value;	
		}
	}
	
	//style for loading infinitely
	public static function getStyle(){
		$style =
		"<style>
			.infinite-loading-mask.anim{
				min-height:400px;
			}
			.infinite-loading-mask{	
				background:none;
			}
			.infinite-loading-mask.loading{
				background:#F0F0F0 url(\"". plugins_url( "/loading.gif", __FILE__) . "\") no-repeat center center;
			}
			.infinite-loading-mask.anim > ul,
			.infinite-loading-mask.anim > tbody,
			.infinite-loading-mask.anim > div{
				display:none;
			}
			.infinite-loading-mask #wpil-no-results {
			    display:block;
                text-align: center;
                height: 100px;
			}
		</style>";
		echo $style;
	}
	
	//script for loading infinitely
	public function getScript(){
		//on load filter
		$filters = !empty($this->on_load_filter_value)?json_encode($this->on_load_filter_value):'{}';
		$order = isset($this->on_load_sort_value['order'])?'"'.$this->on_load_sort_value['order'].'"':'null';
		$orderby = isset($this->on_load_sort_value['orderby'])?'"'.$this->on_load_sort_value['orderby'].'"':'null';

		
		$script = 
		"<script type=\"text/javascript\">
			var {$this->clean_js_id}_OBJECT = ".json_encode($this).";
			var {$this->clean_js_id}_LIMIT = {$this->item_number_to_load};
			var {$this->clean_js_id}_OFFSET = {$this->item_number_on_load};
			var {$this->clean_js_id}_ORDER = {$order};
			var {$this->clean_js_id}_ORDERBY = {$orderby};
			var {$this->clean_js_id}_FILTERS = {$filters};
			var WPIL_BUTTON_LOCKED = false;
			jQuery(document).ready(function($){
				//loadmore button
				jQuery(\"#{$this->_infinite_button_id}\").click(function(){
					if(WPIL_BUTTON_LOCKED==true) return false;
					WPIL_BUTTON_LOCKED = true;
					{$this->clean_js_id}_onload();
					".apply_filters('ajax_loading_'.$this->clean_id,'')."
					jQuery.ajax({
				        url: '". admin_url('admin-ajax.php')."',
				        data: {
				          'action':'wpil_load_more',
				          'object': {$this->clean_js_id}_OBJECT,
				          'limit': {$this->clean_js_id}_LIMIT,
				          'offset': {$this->clean_js_id}_OFFSET,
				          'order': {$this->clean_js_id}_ORDER,
				          'orderby': {$this->clean_js_id}_ORDERBY,
				          'filters': {$this->clean_js_id}_FILTERS
				        },
				        dataType:'json',
				        success:function(data) {
				        	jQuery(\"#wpil_item_container_{$this->id}\").append(data.items);
				        	if(data.end){
				        		{$this->clean_js_id}_change_button_status(false);
				        		".apply_filters('no_data_event_'.$this->clean_id,'')."
				        	}else{
				        		{$this->clean_js_id}_change_button_status(true);
				        	}
				        	{$this->clean_js_id}_OFFSET += {$this->clean_js_id}_LIMIT;
				        	WPIL_BUTTON_LOCKED = false;
				        	{$this->clean_js_id}_success();
				        	".apply_filters('ajax_success_'.$this->clean_id,'')."
				        }
			      	});
				});";
			//sorting
			$sorting_classes = '';
			$glue = '';
			foreach ($this->sorting_datas as $className) {
				$sorting_classes.=$glue.'.'.$className;
				$glue=',';
			}
			if(!empty($sorting_classes)){	
				$script.=
				"//sorting button
				jQuery(\"{$sorting_classes}\").click(function(){
					if(WPIL_BUTTON_LOCKED==true) return false;
					WPIL_BUTTON_LOCKED = true;
					{$this->clean_js_id}_ORDER = jQuery(this).data('order');
					{$this->clean_js_id}_ORDERBY = jQuery(this).data('orderby');
					{$this->clean_js_id}_onload();
					".apply_filters('ajax_loading_'.$this->clean_id,'')."
					jQuery.ajax({
				        url: '". admin_url('admin-ajax.php')."',
				        data: {
				          'action':'wpil_load_more',
				          'object': {$this->clean_js_id}_OBJECT,
				          'limit': {$this->clean_js_id}_OFFSET,
				          'offset': 0,
				          'order': {$this->clean_js_id}_ORDER,
				          'orderby': {$this->clean_js_id}_ORDERBY,
				          'filters': {$this->clean_js_id}_FILTERS
				        },
				        dataType:'json',
				        success:function(data) {
				        	jQuery(\"#wpil_item_container_{$this->id}\").html(data.items);
				        	if(data.end){
				        		{$this->clean_js_id}_change_button_status(false);
				        		".apply_filters('no_data_event_'.$this->clean_id,'')."
				        	}else{
				        		{$this->clean_js_id}_change_button_status(true);
				        	}
				        	WPIL_BUTTON_LOCKED = false;
				        	{$this->clean_js_id}_success();
				        	".apply_filters('ajax_success_'.$this->clean_id,'')."
				        }
			      	});
				});";
			}
			//filtering
			$filter_classes = '';
			$glue = '';
			foreach ($this->filters_datas as $className) {
				$filter_classes.=$glue.'.'.$className;
				$glue=',';
			}
			if(!empty($filter_classes)){
				$script.=
				"//filtering button
				jQuery(\"{$filter_classes}\").each(function($){
					if(jQuery(this).get(0).tagName.toLowerCase() == 'option'){
						//coming soon
					}else if(jQuery(this).get(0).tagName.toLowerCase() == 'checkbox' || jQuery(this).get(0).tagName.toLowerCase() == 'radio'){	
						//coming soon
					}else{
						jQuery(this).click(function($){
							if(WPIL_BUTTON_LOCKED==true) return false;
							WPIL_BUTTON_LOCKED = true;
							_filter = jQuery(this).data('filter');
							_filterby = jQuery(this).data('filterby');

							//check if filter is active
						    _activefiltervalue = {$this->clean_js_id}_FILTERS[_filter];
						    if(_activefiltervalue == _filterby){
						        WPIL_BUTTON_LOCKED = false;
                                 return;
						    }

							{$this->clean_js_id}_FILTERS[_filter] = _filterby;

							{$this->clean_js_id}_onload();
							".apply_filters('ajax_loading_'.$this->clean_id,'')."
							jQuery.ajax({
						        url: '". admin_url('admin-ajax.php')."',
						        data: {
						          'action':'wpil_load_more',
						          'object': {$this->clean_js_id}_OBJECT,
						          'limit': {$this->clean_js_id}_OFFSET,
						          'offset': 0,
						          'order': {$this->clean_js_id}_ORDER,
						          'orderby': {$this->clean_js_id}_ORDERBY,
						          'filters': {$this->clean_js_id}_FILTERS
						        },
						        dataType:'json',
						        success:function(data) {
						        	if (data.items){
						        	    jQuery(\"#wpil_item_container_{$this->id}\").html(data.items);
						        	}else{
						        	    jQuery(\"#wpil_item_container_{$this->id}\").html(\"{$this->messageNotFound}\");
						        	}

						        	if(data.end){
						        		{$this->clean_js_id}_change_button_status(false);
						        		".apply_filters('no_data_event_'.$this->clean_id,'')."
						        	}else{
						        		{$this->clean_js_id}_change_button_status(true);
						        	}
						        	WPIL_BUTTON_LOCKED = false;
						        	{$this->clean_js_id}_success();
						        	".apply_filters('ajax_success_'.$this->clean_id,'')."
						        }
					      	});
						})
		        	}
	        	})";
			}
			
		//button load more status	
		if($this->_display_infinite_load_button){
			$script.=  "
						{$this->clean_js_id}_change_button_status(true);
						";
		}else{
			$script.=  "
						{$this->clean_js_id}_change_button_status(false);
						";
		}
		
		$script.=	
			"});
			function {$this->clean_js_id}_onload(){
				if(". (($this->do_anim)?'true':'false') ."){
            jQuery(\"#wpil_item_container_{$this->id}\").css({'opacity':'0.1'});
            jQuery(\"#wpil_item_container_{$this->id}\").parent().addClass('loading');
				}
			}
			function {$this->clean_js_id}_success(){
				if(". (($this->do_anim)?'true':'false') ."){
 				  setTimeout(
				    function(){
		              jQuery(\"#wpil_item_container_{$this->id}\").css({'opacity':'1'});
		              jQuery(\"#wpil_item_container_{$this->id}\").parent().removeClass('loading');
		            },
		            {$this->success_delay}
		          );
				}
			}
			function {$this->clean_js_id}_change_button_status(show){
				if(show){
					jQuery(\"#{$this->_infinite_button_id}\").show();
					jQuery(\"#{$this->_infinite_button_hide_id}\").hide();
				}else{
					jQuery(\"#{$this->_infinite_button_id}\").hide();
					jQuery(\"#{$this->_infinite_button_hide_id}\").show();
				}
			}
			jQuery(window).on('load',function(){
				_containter = jQuery(\"#wpil_item_container_{$this->id}\");
				_containter.parents('.infinite-loading-mask').removeClass('loading');
				_containter.show();
			})
		</script>";
		echo $script;
	}
	
}
add_action("wp_ajax_wpil_load_more", array(WP_Infinite_Loading,'ajax_request'));
add_action("wp_ajax_nopriv_wpil_load_more", array(WP_Infinite_Loading,'ajax_request'));
add_action("wp_head",array(WP_Infinite_Loading, 'getStyle'));