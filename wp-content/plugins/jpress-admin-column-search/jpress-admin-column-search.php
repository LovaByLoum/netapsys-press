<?php
/*
 * Plugin Name: jPress Admin Column Search
 * Plugin URI:
 * Text Domain: jpress-acs
 * Description: Add an advanced data search and filter on admin page list content for each declared columns
 * Author: Johary Ranarimanana (Netapsys)
 * Author URI: http://www.netapsys.fr/
 * Version: 1.0.1
 * License: GPLv2 or later
 * Domain Path: /languages/
*/

//add an admin page menu
add_action( 'admin_menu', 'jpress_acs_add_custom_admin_page' );
function jpress_acs_add_custom_admin_page() {
  add_submenu_page( 'options-general.php', __('Admin Column Search', 'jpress-acs'), __('Admin Column Search', 'jpress-acs'), 'manage_options', 'admin-column-search', 'jpress_acs_admin_page' );
}
//admin page callback
function jpress_acs_admin_page() {
  include( 'pages/admin-page.php' );
}

//admin  init action
add_action('admin_init', 'jpress_acs_init');
function jpress_acs_init() {
  //manage columns values for all post type
  $post_types = get_post_types();
  foreach ( $post_types as $pt ) {
    add_filter( 'manage_edit-' . $pt . '_columns', 'jpress_acs_manage_columns', 555 );
  }

  //admin styles
  wp_enqueue_style( 'jpress-acs-style', plugins_url( basename( dirname( __FILE__) ) ) . '/assets/css/acs-styles.css' );

  //admin script
  wp_enqueue_script( 'jpress-acs-script', plugins_url( basename( dirname( __FILE__) ) ) . '/assets/js/acs-script.js' );

}
//callback for manage columns values for all post type
function jpress_acs_manage_columns( $columns ) {
  global $current_screen;
  $pt = $current_screen->post_type;
  $acs_options = get_option( 'jpress_acs_options' );

  //if options is not loaded
  if ( !$acs_options )
  	return $columns;
  
  //if admin search column is not active for the post type
  if ( isset( $acs_options['enable'] ) && ! in_array( $pt, $acs_options['enable'] ) )
    return  $columns;

  if ( isset( $acs_options['colonne'][$pt] ) ) {
    foreach ( $columns as $k => $col ) {
      if ( $acs_options['colonne'][$pt][$k] == 1 ) {
        $columns[$k] = $columns[$k] . '<span class="acs_input_cible" data-col="' . $k . '">&nbsp;</span>';
      }
    }
  }
  return $columns;
}

//add css styles
add_action( 'admin_head', 'jpress_acs_admin_head' );
function jpress_acs_admin_head() {
	global $pagenow;

  //add scripts
  echo "\r\n<script type=\"text/javascript\">";

      if ( isset( $_GET['acs_search_submit'] ) && isset( $_GET['acs_search'] ) ) {
        $fillvalues = json_encode( $_GET['acs_search'] );
        echo "\r\nvar acs_values = " . $fillvalues . ";";
      }

      if ( $pagenow == 'edit.php' ) {
      	echo "\r\nvar acs_dropbox = " . jpress_acs_input_column() . ";";
      }

  echo "</script>\n";
}

//save post action
add_action( 'save_post', 'acs_save_post' );
function acs_save_post( $post_id ) {
  global $wpdb;
  //delete transient on content update
  $wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '%acs_input_column_%'" );
}

//manage input field column search
function jpress_acs_input_column(){
	global $current_screen, $wpdb;

  //use transient to load input data
  $pt = $current_screen->post_type;
	$transient_name = 'acs_input_column_' . $pt;
	$inputform = get_transient( $transient_name );
	if ( $inputform ) return $inputform;
	
	$acs_options = get_option( 'jpress_acs_options' );
  $inputform = array();

  //create table field drop box
  if ( isset( $acs_options['postdata_field'][$pt] ) ) {
    foreach ( $acs_options['postdata_field'][$pt] as $key => $val) {
      if ( empty($val) ) continue;
      if ( $val != 'select' ) continue;
      if ( isset( $_GET['acs_search'] ) ) {
        $current_value = $_GET['acs_search'][$key];
      }
      $html =
        '<select name="acs_search[' . $key . ']" class="acs_select">
          <option value="">--' . __( "Séléctionnez", "jpress-acs" ) . '--</option>';

          //compatibility with jcpt plugins
          $table = $wpdb->posts;
          if ( function_exists( 'jcpt_whereis' ) ) {
            global $jcpt_options;
            if ( is_null( $jcpt_options ) ) {
                $jcpt_options = get_option( 'jcpt_options' );
            }
            if ( in_array( $pt, $jcpt_options['enable'] ) ) {
              $table = $wpdb->prefix . $pt . 's';
            }
          }
          $column = $acs_options['postdata'][$pt][$key];

          $options = apply_filters( 'jpress_acs_select_option_' . $pt . '_' . $column, null );

          if ( is_null( $options ) ) {
            $sql = "SELECT DISTINCT {$column} FROM {$table} WHERE post_status = 'publish'";
            $options = $wpdb->get_col( $sql );
            $final_options = array();
            foreach ( $options as $val ) {
              $final_options[$val] = apply_filters( 'acs_select_label_' . $pt . '_' . $key, $val );
            }
          } else {
            $final_options = $options;
          }

          $final_options = array_filter( $final_options );
          asort( $final_options );
          foreach ( $final_options as $fkey => $val ) {
            if ( is_null( $fkey ) || empty( $fkey ) ) $fkey = '0';
            $html .= '<option value="' . $fkey . '">' . $val . '</option>';
          }
      $html .= '</select>';
      $inputform[$key] = $html;
    }
  }

  //create taxonomy dropbox
  if ( isset( $acs_options['tax'][$pt] ) ) {
    foreach ( $acs_options['tax'][$pt] as $key => $val ) {
      if ( empty( $val ) ) continue;
      $terms = get_terms(
        $val,
        array(
          'orderby' => 'name',
          'order' => 'ASC',
          'hide_empty' => false
        )
      );
      $current_value = null;
      if ( isset( $_GET['acs_search'] ) ) {
        $current_value = $_GET['acs_search'][$key];
      }
      $html =
      '<select name="acs_search[' . $key . ']" class="acs_select">
        <option value="">--' . __( "Séléctionnez", "jpress-acs" ) . '--</option>';

      foreach ( $terms as $term ) {
        $html .= '<option value="' . $term->term_id . '" >' . $term->name . '</option>';
      }
      $html .= '</select>';
      $inputform[$key] = $html;
    }
  }

  //create meta dropbox
  if ( isset( $acs_options['meta_field'][$pt] ) ) {
    foreach ( $acs_options['meta_field'][$pt] as $key => $val ) {
      if ( empty( $val ) ) continue;
      if ($val!='select') continue;
      if ( isset( $_GET['acs_search'] ) ) {
        $current_value = $_GET['acs_search'][$key];
      }
      $html =
      '<select name="acs_search[' . $key . ']" class="acs_select">
        <option value="">--' . __( "Séléctionnez", "jpress-acs" ) . '--</option>';

        $options = apply_filters( 'acs_select_option_' . $pt . '_' . $column, null );

        if ( is_null( $options ) ) {
          $sql = "SELECT DISTINCT pm.meta_value FROM {$wpdb->prefix}postmeta AS pm
              INNER JOIN {$wpdb->prefix}posts AS p ON (p.ID = pm.post_id)
              WHERE pm.meta_key = '" . $acs_options['meta'][$pt][$key] . "'
              AND p.post_type ='" . $pt . "'";

          $options = $wpdb->get_col( $sql );
          $final_options = array();
          foreach ( $options as $val ) {
            $final_options[$val] = apply_filters( 'acs_select_label_' . $pt . '_' . $key, $val );
          }
        } else {
          $final_options = $options;
        }

        $final_options = array_filter( $final_options );
        asort( $final_options );
        foreach ( $final_options as $fkey => $val ) {
          $html .= '<option value="' .$fkey. '">' . $val . '</option>';
        }
      $html .= '</select>';
      $inputform[$key] = $html;
    }
  }

  $inputform = json_encode( $inputform );
  set_transient( $transient_name, $inputform, 86400 );
  return $inputform;
}

//add filter form
if ( is_admin() ) add_filter( 'parse_query', 'acs_admin_posts_filter' );
function acs_admin_posts_filter( $query ) {
  global $current_screen, $pagenow;
  $pt = $current_screen->post_type;
  $acs_options = get_option( 'jpress_acs_options' );
  if ( $pagenow == 'edit.php' && is_admin() && isset( $_GET['acs_search_submit'] ) && isset( $_GET['acs_search'] ) ) {
  	if ( ! isset( $query->query['post_type'] ) || $query->query['post_type'] != $pt ) return $query;
    foreach ( $_GET['acs_search'] as $k => $v) {
      if ( $v == "" ) continue;
      
      //get priority
      //1 : post data	  
      if ( !empty( $acs_options['postdata'][$pt][$k] ) ) {
        global $wpdb;
        $fields = $wpdb->get_results( "SHOW COLUMNS FROM {$wpdb->prefix}{$pt}s" );
        if ( ! $fields ) {
            $fields = $wpdb->get_results( "SHOW COLUMNS FROM {$wpdb->prefix}posts" );
        }
        if ( $fields ) {
            $switch = "switch ( \$acs_options['postdata'][\$pt][\$k] ) {\n";
            $switch .= "case 'post_title' :
                           \$query->query_vars['s'] = \$v;
                           break;\n" ;
            foreach ( $fields as $field ) {
                $switch .= "case '" . $field->Field . "' :
                           \$query->query_vars['" . $field->Field . "'] = \$v;
                           break;\n" ;
            }
            $switch .= "default : break;
            \n}";
            eval( $switch );
        }

      //2 : taxonomy
      } elseif ( ! empty( $acs_options['tax'][$pt][$k] ) ) {
	      $tq = array (
	        'taxonomy' => $acs_options['tax'][$pt][$k],
	        'field' => 'id',
	        'terms' => $v,
	        'include_children' => true
	      );
  	    $query->query_vars['tax_query'][] = apply_filters( 'jpress_acs_meta_query_filter', $tq, $acs_options['tax'][$pt][$k], $v );
	      $query->query_vars['tax_query']['relation'] = 'AND';

 	    //3 : meta query
      } else {
	      $mq = array(
	        'key' => $acs_options['meta'][$pt][$k],
	        'value' => $v,
	        'compare' => 'LIKE'
	      );
	      $query->query_vars['meta_query'][] = apply_filters( 'jpress_acs_meta_query_filter', $mq, $acs_options['meta'][$pt][$k], $v );
	      $query->query_vars['meta_query']['relation'] = 'AND';
      }
    }
    
    $query = apply_filters( 'jpress_acs_query', $query );
  }
}

//plugin compatibility
//The event calendar column compatibility
/*meta query filter for event*/
add_filter( 'jpress_acs_meta_query_filter', 'tec_acs_meta_query_filter', 10, 3 );
function tec_acs_meta_query_filter ($mq, $mk,$mv ) {
	if ( is_admin() ) {
		if ( $mk == '_EventStartDate' ) {
      if ( strpos( $mv, '/' ) ) $mv = substr( $mv, 6, 4 ) . '-' . substr( $mv, 3, 2 ) . '-' . substr( $mv, 0, 2 );
			$mq = array(
        'key' => $mk,
        'value' => $mv,
        'type' => 'DATE',
        'compare' => '>='
      );
		} elseif ( $mk == '_EventEndDate' ) {
      if ( strpos( $mv, '/' ) ) $mv = substr( $mv, 6, 4 ) . '-' . substr( $mv, 3, 2 ) . '-' . substr( $mv, 0, 2 );
			$mq = array(
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
function tec_acs_query( $q ) {
  if ( $q->query_vars['meta_key'] == 'start-date' ) {
    $q->query_vars['meta_key'] = '_EventStartDate';
  }
  if ( $q->query_vars['meta_key'] == 'end-date' ) {
    $q->query_vars['meta_key'] = '_EventEndDate';
  }
	return $q;
}