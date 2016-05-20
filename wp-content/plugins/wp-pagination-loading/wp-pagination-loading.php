<?php
/*
Plugin Name: WP Pagination Loading
Version: 1.1
Description: Tools and utils for loading data with  pagination via ajax, include filters and sorting actions
Author: Johary
Text Domain: wp-pagination-loading
Domain Path: /lang
*/

/* EXAMPLE OF USE
	
	$object = new WP_Pagination_Loading('product-pagination-box');
    //configuration
    //set list or table view
    $object->setListView('list'); 
    //number of item on first load
    $object->setItemPerPage(3);
    //container class
    $object->setContainerClasses('wrapper');
    //item classes
    $object->setItemClasses('test');
    //set function callback for customize item template
    $object->setRenderItemCallback(array(CProduct, 'renderItemCallback'));
    //set function callback for getting items by offset, limit, filter and sort, must return array('posts', 'count')
    $object->setGetItemsCallback(array(CProduct, 'getItemsCallback'));
    //add class to retrieve sorting element
    $object->addSorting('product-sort-date');
    //add class to retrieve filter element
    $object->addFilter('product-filtre-category');
    $object->configPagination(array('first_text'=>"<<", 'last_text'=>">>"));

    //display pagination loading box
    $object->displayItems();
    //display the pagination loading button
    $object->displayPaginationLoadButton();

    //hook class item
    // $class = class name element
    //$key = position element
    //apply_filters('wppl_item_class_' . $this->clean_id,$class,$key);

    //Filtre type select ajout data-filter="<nom-filtre>" et class
    //Filtre input radio/checkbox ajout data-filter="<nom-filtre>" et class
*
*
* see other functions :  setOnLoadSorting, setOnLoadFilter, removeMask
*
*
*/

class WP_Pagination_Loading{

	// pagination load box id
	public $id;
	//cleanid for function concatenation
	public $clean_id;
	public $clean_js_id;
	// list or table view
	public $list_view = 'other';
	//number of items on first display
	public $item_per_page = 5;
  //offset
  public $item_offset = 0;
  //total
  public $total_posts = 0;
	//item classes
	public $item_classes;
	//do animation
	public $do_anim = true;
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
	//pagination button id
	private $_pagination_button_id = 'paginatio-button';
	//required for displaying pagination load button on first load, if items less than limit
	private $_display_pagination_load_button = true;
	public $pagination_args = array();
  public $class_wrapper_container;

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

  //set extra args
  public function setExtraArgs($args){
    $this->extra_args = $args;
  }

	//set item classes
	public function setItemClasses($classes){
		$this->item_classes = $classes;
	}

  //set container classes
  public function setContainerClasses($classes){
    $this->container_classes = $classes;
  }

	//set number item on load
	public function setItemPerPage($value){
		$this->item_per_page = intval($value);
	}

	//set pagination args
  /**
  'before' => '',
  'after' => '',
  'echo' => true,

  'style' => 1,
  'always_show' => false,
  'num_pages' => 2,
  'num_larger_page_numbers' => 2,
  'larger_page_numbers_multiple' => 5,

  'pages_text' => '', // use "%CURRENT_PAGE%", "%TOTAL_PAGES%" rewrite tag
  'first_text' => '<<', // use "%TOTAL_PAGES%" rewrite tag
  'prev_text' => '<',
  'dotleft_text' => '...',
  'dotright_text' => '...',
  'current_text' => '%PAGE_NUMBER%', // use "%PAGE_NUMBER%" rewrite tag
  'page_text' => '%PAGE_NUMBER%',
  'next_text' => '>',
  'last_text' => '>>', // use "%TOTAL_PAGES%" rewrite tag
   */
  public function configPagination($args){
		$this->pagination_args = $args;
	}

  //set numero page on load
  public function setPageNumero($value){
    $this->item_offset = (intval($value)-1)*$this->item_per_page;
  }
	
	//don't do animation
	public function removeMask(){
		$this->do_anim = false;
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

  public function setClassWrapperContainer($class){
    $this->class_wrapper_container = $class;
  }
	
	//render data list with pagination loading box
	public function displayItems(){
		//get on load sort/filter value
		$sortings = !empty($this->on_load_sort_value)?$this->on_load_sort_value:null;
		$filters = !empty($this->on_load_filter_value)?$this->on_load_filter_value:null;
		
		//get items
		$results = $this->getItems( 0, $this->item_per_page, $filters, $sortings);
		extract($results);
		if($this->list_view == 'list'){
			$html = '<ul id="wppl_item_container_'.$this->id.'" class="' . $this->container_classes .'">'.$html.'</ul>';
		}elseif ($this->list_view == 'table'){
      $extra = apply_filters('wppl_item_container_extra_' . $this->clean_id,'');
			$html = '<table ' . $extra . ' id="wppl_item_container_'.$this->id.'" class="' . $this->container_classes .'">'.$html.'</table>';
	    }else{
	      $html = '<div id="wppl_item_container_'.$this->id.'" class="' . $this->container_classes .'">'.$html.'</div>';
	    }
		echo '<div class="pagination-loading-mask loading ' . $this->class_wrapper_container . '">' . $html . '</div>';
	}

	//get items
	public function getItems($offset, $limit, $filters ,$sorting){
		//get items
		if($this->_getItemsCallback){
			add_filter('wppl_get_items_'.$this->clean_id,$this->_getItemsCallback,10,5);
		}else{
			add_filter('wppl_get_items_'.$this->clean_id,array($this,'defaultGetItemsCallback'),10,5);
		}
		$results = apply_filters('wppl_get_items_'.$this->clean_id,$offset, $limit, $filters,$sorting, $this->extra_args);
    $item_ids = $results['posts'];
		//calculate the first displaying status of the pagination load button
		$count = $results['count'];
    $this->total_posts = $count;
    $this->item_offset = $offset;

		$html = '';
		if(!empty($item_ids)){
			foreach ($item_ids as $key => $id) {
        $class = apply_filters('wppl_item_class_' . $this->clean_id,'item',$key);
				$html.= $this->displayItem(intval($id),$class);
			}
		}
		
		return array(
			'count' => $count,
			'html' => $html
		);
	}
	
	//render pagination load button, based on wp pagenavi plugin
	public function displayPaginationLoadButton(){
		$args = wp_parse_args( $this->pagination_args, array(
			'before' => '',
			'after' => '',
			'echo' => true,
			'style' => 1,
			'always_show' => false,
			'num_pages' => 2,
			'num_larger_page_numbers' => 2,
			'larger_page_numbers_multiple' => 5,
			'pages_text' => '', // use "%CURRENT_PAGE%", "%TOTAL_PAGES%" rewrite tag
			'first_text' => '<<', // use "%TOTAL_PAGES%" rewrite tag
			'prev_text' => '<',
			'dotleft_text' => '...', 
			'dotright_text' => '...', 
			'current_text' => '%PAGE_NUMBER%', // use "%PAGE_NUMBER%" rewrite tag
			'page_text' => '%PAGE_NUMBER%',
			'next_text' => '>',
			'last_text' => '>>', // use "%TOTAL_PAGES%" rewrite tag
      'container_class' => 'pager_container',
      'container' => 'div',
		) );

		$posts_per_page = $this->item_per_page;
		$paged = intval($this->item_offset/$this->item_per_page +1);
		$total_pages = ceil($this->total_posts/$this->item_per_page);


		if ( 1 == $total_pages && !$args['always_show'])
			return;


		$pages_to_show = absint( $args['num_pages'] );
		$larger_page_to_show = absint( $args['num_larger_page_numbers'] );
		$larger_page_multiple = absint( $args['larger_page_numbers_multiple'] );
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start = floor( $pages_to_show_minus_1/2 );
		$half_page_end = ceil( $pages_to_show_minus_1/2 );
		$start_page = $paged - $half_page_start;
	
		if ( $start_page <= 0 )
			$start_page = 1;
	
		$end_page = $paged + $half_page_end;
	
		if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 )
			$end_page = $start_page + $pages_to_show_minus_1;
	
		if ( $end_page > $total_pages ) {
			$start_page = $total_pages - $pages_to_show_minus_1;
			$end_page = $total_pages;
		}
	
		if ( $start_page < 1 )
			$start_page = 1;
	
		$out = '';
		switch ( intval( $args['style'] ) ) {
			// Normal
			case 1:
				// Text
				if ( !empty( $args['pages_text'] ) ) {
					$pages_text = str_replace(
						array( "%CURRENT_PAGE%", "%TOTAL_PAGES%" ),
						array( number_format_i18n( $paged ), number_format_i18n( $total_pages ) ),
            $args['pages_text'] );
					$out .= "<span class='pages'>$pages_text</span>";
				}

        $out .= '<div class="pager prev">';
          if ( $start_page >= 2 && $pages_to_show < $total_pages && !empty( $args['first_text'] ) ) {
            // First
            $first_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $total_pages ), $args['first_text'] );
            $out .= $this->get_single( 1, 'first', $first_text, '%TOTAL_PAGES%', 'first' );
          }

          // Previous
          if ( $paged > 1 && !empty( $args['prev_text'] ) )
            $out .= $this->get_single( $paged - 1, 'previouspostslink', $args['prev_text'] , null, 'prev');

          if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
            if ( !empty( $args['dotleft_text'] ) )
              $out .= "<span class='extend'>{$args['dotleft_text']}</span>";
          }
        $out .= '</div>';

				// Smaller pages
				$larger_pages_array = array();
				if ( $larger_page_multiple )
					for ( $i = $larger_page_multiple; $i <= $total_pages; $i+= $larger_page_multiple )
						$larger_pages_array[] = $i;
	
				$larger_page_start = 0;
				if(sizeof($larger_pages_array)>0){
					foreach ( $larger_pages_array as $larger_page ) {
						if ( $larger_page < ($start_page - $half_page_start) && $larger_page_start < $larger_page_to_show ) {
							$out .= $this->get_single( $larger_page, 'smaller page', $args['page_text'] );
							$larger_page_start++;
						}
					}
				}
	
				if ( $larger_page_start )
					$out .= "<span class='extend'>{$args['dotleft_text']}</span>";
	
				// Page numbers
				$timeline = 'smaller';
				if($start_page<=$end_page){
          $out .= "<span>";
					foreach ( range( $start_page, $end_page ) as $i ) {
						if ( $i == $paged && !empty( $args['current_text'] ) ) {
							$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $args['current_text'] );
							//$out .= "<span>$current_page_text</span>";
	            $out .= $this->get_single( $i, "page $timeline active", $args['page_text'] ,'%PAGE_NUMBER%','',true);
							$timeline = 'larger';
						} else {
							$out .= $this->get_single( $i, "page $timeline", $args['page_text'] );
						}
					}
          $out .= "</span>";
				}
				
				// Large pages
				$larger_page_end = 0;
				$larger_page_out = '';
				if(sizeof($larger_pages_array)>0){
					foreach ( $larger_pages_array as $larger_page ) {
						if ( $larger_page > ($end_page + $half_page_end) && $larger_page_end < $larger_page_to_show ) {
							$larger_page_out .= $this->get_single( $larger_page, 'larger page', $args['page_text'] );
							$larger_page_end++;
						}
					}
				}
	
				if ( $larger_page_out ) {
					$out .= "<span class='extend'>{$args['dotright_text']}</span>";
				}
				$out .= $larger_page_out;
	
				if ( $end_page < $total_pages ) {
					if ( !empty( $args['dotright_text'] ) )
						$out .= "<span class='extend'>{$args['dotright_text']}</span>";
				}

        $out .= '<div class="pager next">';
          // Next
          if ( $paged < $total_pages && !empty( $args['next_text'] ) )
            $out .= $this->get_single( $paged + 1, 'nextpostslink', $args['next_text'],null, 'next' );

          if ( $end_page < $total_pages  && !empty( $args['last_text'] )) {
            // Last
            $out .= $this->get_single( $total_pages, 'last', $args['last_text'], '%TOTAL_PAGES%', 'last' );
          }
        $out .= '</div>';
				break;

      //Custom
      case 2:
        $array_args = array(
          'start_page' => $start_page,
          'end_page' => $end_page,
          'pages_to_show' => $pages_to_show,
          'larger_page_multiple' => $larger_page_multiple,
          'larger_page_to_show' => $larger_page_to_show,
          'half_page_start' => $half_page_start,
          'half_page_end' => $half_page_end,
          'paged' => $paged,
          'total_pages' => $total_pages
        );

        $out .= apply_filters( 'wppl_custom_out', $array_args );

        break;
		}

		$out = $args['before'] . "<" . $args['container'] . " id='wppl-pagination-button-box-" . $this->clean_id ."' class='pager nums " . $args['container_class'] ."'>\n$out\n</" . $args['container'] . ">" . $args['after'];

		$out = apply_filters( 'wppl_pagenavi', $out );
	
		if ( !$args['echo'] )
			return $out;
	
		echo $out;
	}
	public function get_single( $page, $class, $raw_text, $format = '%PAGE_NUMBER%', $spanclass = '' ,$current=false,$span = true) {
		if ( empty( $raw_text ) )
			return '';

		$text = str_replace( $format, number_format_i18n( $page ), $raw_text );
    $wpplclass = '';
    if(!$current) $wpplclass = "pagination-button-". $this->clean_id ;
    if($span)
		  return "<span class='". $spanclass ."'><a href='javascript:;' data-page='" . $page . "' class='". $wpplclass ." " . $class . "'>$text</a></span>";
    else
      return "<a href='javascript:;' data-page='" . $page . "' class='". $wpplclass ." " . $class . "'>$text</a>";
	}
	
	//add sorting configuration, the element must have data-order and data-orderby attributes
	public function addSorting($className){
   array_push($this->sorting_datas, $className);
	}
	
	//add filter configuration, the element must have data-filter and data-filterby attributes
	public function addFilter($className){
		array_push($this->filters_datas, $className);
	}
	
	//display an item
	private function displayItem($id,$class=""){
		if($this->_renderItemCallback){
			add_filter('wppl_display_item_'.$this->clean_id,$this->_renderItemCallback,1,2);
		}else{
			add_filter('wppl_display_item_'.$this->clean_id, array($this,'defaultRenderItemCallback'));
		}
		$html = apply_filters('wppl_display_item_'.$this->clean_id,$id,$class);
		
		$classes = ($this->item_classes)?' class="wppl_item '  .$this->item_classes . ' ' . $class . '" ':' class="wppl_item ' . $class . '" ';
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
		$pagination_load_object = new WP_Pagination_Loading($object->id);
		$pagination_load_object->loadFromObject($object);
		
		$sortings = array();
		$sortings['order'] = $_REQUEST["order"];
		$sortings['orderby'] = $_REQUEST["orderby"];
		
		$filters = $_REQUEST["filters"];
		$results = $pagination_load_object->getItems( $_REQUEST["offset"], $_REQUEST["limit"], $filters,$sortings);
		extract($results);
		$results = new stdClass();
		$results->count = $count;
		$results->items = $html;
    if($results->count == 0){
      $results->items = apply_filters('wppl_message_erreur_' . $pagination_load_object->clean_id,'<p>Aucun résultat ne correspond à ce(s) critère(s)<p>');
    }
    $pagination_load_object->pagination_args['echo'] = false;
    $results->pager = $pagination_load_object->displayPaginationLoadButton();
		echo json_encode($results);die();
	}
	
	//load from object
	public function loadFromObject($object){
		foreach ($object as $propriety => $value) {
			$this->$propriety = $value;	
		}
	}
	
	//style for loading paginationly
	public static function getStyle(){
		$style =
		"<style>
			.pagination-loading-mask{
				/*min-height:400px;*/
				background:none;
			}
			.pagination-loading-mask.loading{
				background: url(\"". plugins_url( "/images/loading.gif", __FILE__) . "\") no-repeat center center;
			}
			.pagination-loading-mask > ul,
			.pagination-loading-mask > tbody,
			.pagination-loading-mask > div{
				display:none;
			}
		</style>";
		echo $style;
	}
	
	//script for loading paginationly
	public function getScript(){
		//on load filter
		$filters = !empty($this->on_load_filter_value)?json_encode($this->on_load_filter_value):'{}';
		$order = isset($this->on_load_sort_value['order'])?'"'.$this->on_load_sort_value['order'].'"':'null';
		$orderby = isset($this->on_load_sort_value['orderby'])?'"'.$this->on_load_sort_value['orderby'].'"':'null';

		
		$script =
		"<script type=\"text/javascript\">
			var {$this->clean_js_id}_OBJECT = ".json_encode($this).";
			var {$this->clean_js_id}_LIMIT = {$this->item_per_page};
			var {$this->clean_js_id}_OFFSET = {$this->item_offset};
			var {$this->clean_js_id}_ORDER = {$order};
			var {$this->clean_js_id}_ORDERBY = {$orderby};
			var {$this->clean_js_id}_FILTERS = {$filters};
			var WPPL_BUTTON_LOCKED = false;
			jQuery(document).ready(function($){
				";
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
					if(WPPL_BUTTON_LOCKED==true) return false;
					WPPL_BUTTON_LOCKED = true;
					{$this->clean_js_id}_ORDER = jQuery(this).data('order');
					{$this->clean_js_id}_ORDERBY = jQuery(this).data('orderby');
					{$this->clean_js_id}_onload();
					".apply_filters('ajax_loading_'.$this->clean_id,'')."
					jQuery.ajax({
				        url: '". admin_url('admin-ajax.php')."',
				        data: {
				          'action':'wppl_pagination_action',
				          'object': {$this->clean_js_id}_OBJECT,
				          'limit': {$this->clean_js_id}_LIMIT,
				          'offset': {$this->clean_js_id}_OFFSET,
				          'order': {$this->clean_js_id}_ORDER,
				          'orderby': {$this->clean_js_id}_ORDERBY,
				          'filters': {$this->clean_js_id}_FILTERS
				        },
				        dataType:'json',
				        success:function(data) {
				        	jQuery(\"#wppl_item_container_{$this->id}\").html(data.items);
				        	WPPL_BUTTON_LOCKED = false;
				        	{$this->clean_js_id}_success(data);
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
					if(jQuery(this).get(0).tagName.toLowerCase() == 'select'){
						jQuery(this).change(function($){
              if(WPPL_BUTTON_LOCKED==true) return false;
              WPPL_BUTTON_LOCKED = true;

              jQuery(\"{$filter_classes}\").each(function($){
                if(jQuery(this).prop('tagName') == 'SELECT'){
                  _filter = jQuery(this).data('filter');
                  _filterby = jQuery(this).val();
                }else{
                  _filter = jQuery(this).data('filter');
                  _name = jQuery(this).attr('name');
                  var _val = [];
                  jQuery('input[name='+_name+']:checked').each(function(){
                      _val.push(jQuery(this).val());
                  })
                   _filterby = _val;
                }
                {$this->clean_js_id}_FILTERS[_filter] = _filterby;
              });

              {$this->clean_js_id}_onload();
              ".apply_filters('ajax_loading_'.$this->clean_id,'')."
              jQuery.ajax({
                url: '". admin_url('admin-ajax.php')."',
                data: {
                  'action':'wppl_pagination_action',
                  'object': {$this->clean_js_id}_OBJECT,
                  'limit': {$this->clean_js_id}_LIMIT,
                  'offset': 0,
                  'order': {$this->clean_js_id}_ORDER,
                  'orderby': {$this->clean_js_id}_ORDERBY,
                  'filters': {$this->clean_js_id}_FILTERS
                },
                dataType:'json',
                success:function(data) {
                  jQuery(\"#wppl_item_container_{$this->id}\").html(data.items);
                  WPPL_BUTTON_LOCKED = false;
                  {$this->clean_js_id}_success(data);
                  ".apply_filters('ajax_success_' . $this->clean_id, '')."
                }
              });
            })
					}else if(jQuery(this).get(0).tagName.toLowerCase() == 'input'){
             jQuery(this).change(function($){
                if(WPPL_BUTTON_LOCKED==true) return false;
                WPPL_BUTTON_LOCKED = true;
                _filter = jQuery(this).data('filter');
                _name = jQuery(this).attr('name');
                var _val = [];
                jQuery('input[name='+_name+']:checked').each(function(){
                    _val.push(jQuery(this).val());
                })
                _filterby = _val;
                {$this->clean_js_id}_FILTERS[_filter] = _filterby;
                {$this->clean_js_id}_onload();
                ".apply_filters('ajax_loading_'.$this->clean_id,'')."
                jQuery.ajax({
                  url: '". admin_url('admin-ajax.php')."',
                  data: {
                    'action':'wppl_pagination_action',
                    'object': {$this->clean_js_id}_OBJECT,
                    'limit': {$this->clean_js_id}_LIMIT,
                    'offset': 0,
                    'order': {$this->clean_js_id}_ORDER,
                    'orderby': {$this->clean_js_id}_ORDERBY,
                    'filters': {$this->clean_js_id}_FILTERS
                  },
                  dataType:'json',
                  success:function(data) {
                    jQuery(\"#wppl_item_container_{$this->id}\").html(data.items);
                    WPPL_BUTTON_LOCKED = false;
                    {$this->clean_js_id}_success(data);
                    ".apply_filters('ajax_success_' . $this->clean_id, '')."
                  }
                });
              })
					}else{
						jQuery(this).click(function($){
							if(WPPL_BUTTON_LOCKED==true) return false;
							WPPL_BUTTON_LOCKED = true;
							_filter = jQuery(this).data('filter');
							_filterby = jQuery(this).data('filterby');
							{$this->clean_js_id}_FILTERS[_filter] = _filterby;
							{$this->clean_js_id}_onload();
							".apply_filters('ajax_loading_'.$this->clean_id,'')."
							jQuery.ajax({
						        url: '". admin_url('admin-ajax.php')."',
						        data: {
						          'action':'wppl_pagination_action',
						          'object': {$this->clean_js_id}_OBJECT,
						          'limit': {$this->clean_js_id}_LIMIT,
						          'offset': 0,
						          'order': {$this->clean_js_id}_ORDER,
						          'orderby': {$this->clean_js_id}_ORDERBY,
						          'filters': {$this->clean_js_id}_FILTERS
						        },
						        dataType:'json',
						        success:function(data) {
						        	jQuery(\"#wppl_item_container_{$this->id}\").html(data.items);
						        	WPPL_BUTTON_LOCKED = false;
						        	{$this->clean_js_id}_success(data);
						        	".apply_filters('ajax_success_' . $this->clean_id, '')."
						        }
					      	});
						})
		        	}
	        	})";
			}
			
		$script.=	
			"
        //pagination button
        jQuery(\".pagination-button-{$this->clean_id}\").livequery(function(){
          jQuery(this).click(function(){
            if(WPPL_BUTTON_LOCKED==true) return false;
            WPPL_BUTTON_LOCKED = true;
            _page = jQuery(this).data('page');
            _offset = (_page-1)*{$this->clean_js_id}_LIMIT;
            {$this->clean_js_id}_OFFSET = _offset;
            {$this->clean_js_id}_onload();
            ".apply_filters('ajax_loading_'.$this->clean_id,'')."
            jQuery.ajax({
                  url: '". admin_url('admin-ajax.php')."',
                  data: {
                    'action':'wppl_pagination_action',
                    'object': {$this->clean_js_id}_OBJECT,
                    'limit': {$this->clean_js_id}_LIMIT,
                    'offset': {$this->clean_js_id}_OFFSET,
                    'order': {$this->clean_js_id}_ORDER,
                    'orderby': {$this->clean_js_id}_ORDERBY,
                    'filters': {$this->clean_js_id}_FILTERS
                  },
                  dataType:'json',
                  success:function(data) {
                    jQuery(\"#wppl_item_container_{$this->id}\").html(data.items);
                    WPPL_BUTTON_LOCKED = false;
                    {$this->clean_js_id}_success(data);
                    ".apply_filters('ajax_success_'.$this->clean_id,'')."
                  }
            });
          });
        })


			});
			function {$this->clean_js_id}_onload(){
				if(". (($this->do_anim)?'true':'false') ."){
					jQuery(\"#wppl_item_container_{$this->id}\").css({'opacity':'0.1'});
					jQuery(\"#wppl_item_container_{$this->id}\").parent().addClass('loading');
				}
			}
			function {$this->clean_js_id}_success(data){
				if(". (($this->do_anim)?'true':'false') ."){
					jQuery(\"#wppl_item_container_{$this->id}\").css({'opacity':'1'});
					jQuery(\"#wppl_item_container_{$this->id}\").parent().removeClass('loading');
				}

				_content = jQuery(data.pager).html();
        jQuery(\"#wppl-pagination-button-box-{$this->clean_id}\").html(_content);

			}
			jQuery(window).on('load',function(){
				_containter = jQuery(\"#wppl_item_container_{$this->id}\");
				_containter.parents('.pagination-loading-mask').removeClass('loading');
				_containter.show();
			})
		</script>";
		echo $script;
	}

  public static function wp_enqueue_scripts(){
    wp_enqueue_script('jquery_livequery', plugins_url('js/jquery.livequery.js', __FILE__)  , array('jquery'));
  }
	
}
add_action("wp_ajax_wppl_pagination_action", array(WP_Pagination_Loading,'ajax_request'));
add_action("wp_ajax_nopriv_wppl_pagination_action", array(WP_Pagination_Loading,'ajax_request'));
add_action("wp_head",array(WP_Pagination_Loading, 'getStyle'));
add_action( 'wp_enqueue_scripts', array(WP_Pagination_Loading, 'wp_enqueue_scripts') );