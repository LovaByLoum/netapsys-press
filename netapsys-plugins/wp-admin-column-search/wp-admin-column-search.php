<?php
/*
Plugin Name: WP Admin Column Search
Description: Ajout de champs de recherche par colonne dans les pages de listes en backend
Author: Johary (Netapsys)
Version: 0.1
*/

add_action('admin_menu', 'acs_add_custom_admin_page');
function acs_add_custom_admin_page(){
  add_submenu_page('options-general.php','Admin Column Search','Admin Column Search','manage_options','admin-column-search','acs_admin_page');
}
function acs_admin_page(){
  include('admin-page.php');

}

add_action('admin_init', 'acs_init');
function acs_init(){
  $post_types = get_post_types();
  foreach ($post_types as $pt) {
    add_filter('manage_edit-' . $pt . '_columns', 'acs_manage_columns',555);
  }
}

function acs_manage_columns($columns){
  global $current_screen;
  $pt = $current_screen->post_type;
  $acs_options = get_option('acs_options');
  
  if(!$acs_options)
  	return $columns;
  
  	
  if(isset($acs_options['enable']) && !in_array($pt,$acs_options['enable']))
    return  $columns;

  if(isset($acs_options['colonne'][$pt])){
    foreach ($columns as $k=>$col) {
      if($acs_options['colonne'][$pt][$k]==1){
        $columns[$k] = $columns[$k] . '<span class="acs_input_cible" data-col="'. $k .'">&nbsp;</span>';
      }
    }
  }
  return $columns;
}

add_action('admin_head', 'acs_admin_head');
function acs_admin_head(){
	global $pagenow;
  echo "\r\n
    <style>
      .acs_input,.acs_select{
        margin : 5px!important;
        height: 2em!important;
        padding-left: 5px!important;
        width: 95%;
      }
      #acs_search{
        margin: 2px;
        padding: 2px;
      }
      .acs_search_wrap{
        padding-top:3px;
        position: absolute;
        left: 3px;
      }
      .acs_row{
        background: #EAF2FA;
      border: #c7d7e2 solid 1px;
      }
    </style>
    ";
  echo "\r\n
  <script type=\"text/javascript\">";
      if(isset($_GET['acs_search_submit']) && isset($_GET['acs_search'])){
        $fillvalues = json_encode($_GET['acs_search']);
        echo "\r\n
          var acs_values=".$fillvalues.";";
      }

      if($pagenow=='edit.php'){
      	echo "\r\n
      	  var acs_dropbox=".acs_input_column().";";
      }

    echo "\r\n

      jQuery(document).ready(function(){\n
      	if(typeof acs_values != 'undefined'){
	        jQuery(window).load(function(){\n
	          jQuery.each(acs_values, function(index, value) {\n
	            jQuery('select[name=\"acs_search[' + index + ']\"]').val(value);\n
	            jQuery('select[name=\"acs_search[' + index + ']\"]').find('option[value=\"' + value + '\"]').prop('selected', true);\n
	          });\n
	        });\n
	    }
        jQuery('#posts-filter').keyup(function(e) {\n
            if(e.keyCode == 13) { // KeyCode de la touche entrée\n
                jQuery('.acs_search_submit').click();\n
            }\n
        });\n
        if(jQuery('.wp-list-table thead').find('.acs_input_cible').length>0){\n
          var td='';\n
          var classes;\n
          for(i=1;i<=jQuery('.wp-list-table thead tr th').length;i++){\n
            classes = jQuery('.wp-list-table thead tr th').eq(i-1).attr('class');\n
            classes = classes.match(/column-([a-z0-9_-]+)/);\n
            td+='<td class=\"column-'+classes[1]+'\"></td>';\n
          }\n
          jQuery('<tr class=\"acs_row\">'+td+'</tr>').insertBefore(jQuery('.wp-list-table tbody tr:first'));\n
          jQuery('.wp-list-table thead tr th').each(function(index){\n
            if(jQuery(this).find('.acs_input_cible').length>0){\n
              column = jQuery(this).find('.acs_input_cible').data('col');\n
              if(typeof acs_values != 'undefined'){\n
                values=acs_values[column];\n
              }else{\n
                values='';\n
              }\n
              if(acs_dropbox[column]){\n
                input_form = acs_dropbox[column];\n
              }else{\n
                input_form = '<input type=\"text\" name=\"acs_search['+column+']\" class=\"acs_input\" value=\"'+values+'\"/>';\n
              }\n
              jQuery('.wp-list-table tbody tr:first td').eq(index).append(input_form);\n
              jQuery(this).find('.acs_input_cible').remove();\n
            }\n
          });\n
          jQuery('.wp-list-table tbody tr:first td:first').append('<div class=\"acs_search_wrap\"><input type=\"submit\" id=\"acs_search\" name=\"acs_search_submit\" value=\"Go\" title=\"Rechercher\" class=\"acs_search_submit button-secondary\"/></div>');\n

          /*remove default wp category dropbox to fix conflict*/\n
          jQuery('#cat').remove();\n
        }\n
      });\n
    </script>\n";
}

add_action('save_post','acs_save_post');
function acs_save_post($post_id){
  global $wpdb;
  $wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%acs_input_column_%'");
}

function acs_input_column(){
	global $current_screen,$wpdb;
    $pt = $current_screen->post_type;
	$transient_name = 'acs_input_column_'.$pt;
	$inputform = get_transient($transient_name);
	if($inputform) return $inputform;
	
	$acs_options = get_option('acs_options');
    $inputform=array();

    //create table field drop box
    if(isset($acs_options['postdata_field'][$pt])){
    	foreach ($acs_options['postdata_field'][$pt] as $key=>$val) {
    		if(empty($val))continue;
    		if($val!='select') continue;
    		if(isset($_GET['acs_search'])){
				$current_value=$_GET['acs_search'][$key];
			}
    		$html='<select name="acs_search[' . $key . ']" class="acs_select">
					<option value="">--Séléctionnez--</option>';

                //compatibility jcpt
                $table = $wpdb->posts;
                if(function_exists('jcpt_whereis')){
                	global $jcpt_options;
					if(is_null($jcpt_options)){
					    $jcpt_options = get_option('jcpt_options');
					} 
					if(in_array($pt,$jcpt_options['enable'])){
						$table = $wpdb->prefix.$pt.'s';	
					}
                }
                $column = $acs_options['postdata'][$pt][$key];
                
                $options = apply_filters('acs_select_option_'.$pt.'_'.$column,null);

                if(is_null($options)){
	                $sql = "SELECT DISTINCT {$column} FROM {$table} WHERE post_status='publish'";
	    			$options = $wpdb->get_col($sql);
	    			$final_options = array();
	    			foreach ($options as $val) {
	    				$final_options[$val] = apply_filters('acs_select_label_'.$pt.'_'.$key,$val);
	    			}
                }else{
                	$final_options = $options;
                }

    			$final_options=array_filter($final_options);
    			asort($final_options);
    			foreach ($final_options as $fkey=>$val) {
                    if(is_null($fkey) ||empty($fkey))$fkey='0';
    				$html.='<option value="' .$fkey. '" >'.$val.'</option>';
    			}
    		$html.='</select>';
    		$inputform[$key]=$html;
    	}
    }
    
    //create taxonomy dropbox
    if(isset($acs_options['tax'][$pt])){
		foreach ($acs_options['tax'][$pt] as $key=>$val) {
			if(empty($val))continue;
			$terms = get_terms($val,array('orderby' => 'name', 'order' => 'ASC','hide_empty' => false));
			$current_value = null;
			if(isset($_GET['acs_search'])){
				$current_value=$_GET['acs_search'][$key];
			}
			$html='<select name="acs_search[' . $key . ']" class="acs_select">
					<option value="">--Séléctionnez--</option>';
				
			foreach ($terms as $term) {
				$html.='<option value="'.$term->term_id.'" >'.$term->name.'</option>';
			}	
			$html.='</select>';
			$inputform[$key]=$html;
		}
    }
    //create meta dropbox
    if(isset($acs_options['meta_field'][$pt])){
        //$metanames = jcpt_get_metanames($pt);
    	foreach ($acs_options['meta_field'][$pt] as $key=>$val) {
    		if(empty($val))continue;
    		if($val!='select') continue;
    		if(isset($_GET['acs_search'])){
				$current_value=$_GET['acs_search'][$key];
			}
    		$html='<select name="acs_search[' . $key . ']" class="acs_select">
					<option value="">--Séléctionnez--</option>';
				
    			$options = apply_filters('acs_select_option_'.$pt.'_'.$column,null);

                if(is_null($options)){
	                $sql = "SELECT DISTINCT pm.meta_value FROM {$wpdb->prefix}postmeta AS pm
											INNER JOIN {$wpdb->prefix}posts AS p ON (p.ID = pm.post_id)
											WHERE pm.meta_key = '" . $acs_options['meta'][$pt][$key] ."'
											AND p.post_type ='" .$pt . "'";

	                $options = $wpdb->get_col($sql);
	                $final_options = array();
	    			foreach ($options as $val) {
	    				$final_options[$val] = apply_filters('acs_select_label_'.$pt.'_'.$key,$val);
	    			}
                }else{
                	$final_options = $options;
                }
                
    			$final_options=array_filter($final_options);
    			asort($final_options);
    			foreach ($final_options as $fkey=>$val) {
    				$html.='<option value="' .$fkey. '">'.$val.'</option>';
    			}
    		$html.='</select>';
    		$inputform[$key]=$html;
    	}
    }
    $inputform = json_encode($inputform);
    set_transient($transient_name,$inputform,86400);
    return $inputform;
}

//add filter form
if(is_admin())
	add_filter( 'parse_query', 'acs_admin_posts_filter' );
function acs_admin_posts_filter( $query )
{
  global $current_screen,$pagenow;
  $pt = $current_screen->post_type;
  $acs_options = get_option('acs_options');
  if ( $pagenow=='edit.php' && is_admin() && isset($_GET['acs_search_submit']) && isset($_GET['acs_search'])) {
  	if(!isset($query->query['post_type']) ||$query->query['post_type']!=$pt) return $query;
    foreach ( $_GET['acs_search'] as $k=>$v) {
      if ($v=="") continue;
      
      //get priority
      //1 : post data	  
      if(!empty($acs_options['postdata'][$pt][$k])){

          global $wpdb;
          $fields = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}{$pt}s");
          if(!$fields){
              $fields = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}posts");
          }
          if($fields){
              $switch = "switch (\$acs_options['postdata'][\$pt][\$k]) {\n";
              $switch.= "case 'post_title':
                             \$query->query_vars['s']=\$v;
                             break;\n" ;
              foreach ($fields as $field) {
                  $switch.= "case '" . $field->Field ."':
                             \$query->query_vars['" . $field->Field ."']=\$v;
                             break;\n" ;
              }
              $switch.="default:
 				        break;
 				        }";
              eval($switch);
         }

      //2 : taxonomy
      }elseif(!empty($acs_options['tax'][$pt][$k])){
	      $tq = array (
	        'taxonomy' => $acs_options['tax'][$pt][$k],
	        'field' => 'id',
	        'terms' => $v,
	        'include_children' => true
	      );
  	      $query->query_vars['tax_query'][]= apply_filters('acs_meta_query_filter',$tq,$acs_options['tax'][$pt][$k],$v);
	      $query->query_vars['tax_query']['relation'] = 'AND';
 	  //3 : meta query		
      }else{
	      $mq= array(
	        'key' => $acs_options['meta'][$pt][$k],
	        'value' => $v,
	        'compare' => 'LIKE'
	      );
	      $query->query_vars['meta_query'][]= apply_filters('acs_meta_query_filter',$mq,$acs_options['meta'][$pt][$k],$v);
	      $query->query_vars['meta_query']['relation'] = 'AND';
      }
    }
    
    $query = apply_filters('acs_query',$query);
    
  }
}

//plugin compatibility
//The event calendar column compatibility
/*meta query filter for event*/
add_filter('acs_meta_query_filter', 'tec_acs_meta_query_filter',10,3);
function tec_acs_meta_query_filter($mq,$mk,$mv){
	if(is_admin()){
		if($mk=='_EventStartDate'){
            if(strpos($mv,'/'))$mv = substr($mv,6,4).'-'.substr($mv,3,2).'-'.substr($mv,0,2);
			$mq= array(
		        'key' => $mk,
		        'value' => $mv,
		        'type' => 'DATE',
		        'compare' => '>='
		      );
		}elseif($mk=='_EventEndDate'){
            if(strpos($mv,'/'))$mv = substr($mv,6,4).'-'.substr($mv,3,2).'-'.substr($mv,0,2);
			$mq= array(
		        'key' => $mk,
		        'value' => $mv,
		        'type' => 'DATE',
		        'compare' => '<='
		      );
		}
	}
	return $mq;
}
add_filter('parse_query','tec_acs_query');
function tec_acs_query($q){
		if($q->query_vars['meta_key']=='start-date'){
			$q->query_vars['meta_key'] = '_EventStartDate';
		}
		if($q->query_vars['meta_key']=='end-date'){
			$q->query_vars['meta_key'] = '_EventEndDate';
		}
	return $q;
}