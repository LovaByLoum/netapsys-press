<?php
/*
Plugin Name: jPress Gallery
Plugin URI: http://wordpress.org/extend/plugins/jpressgallery/
Description: Custom gallery image, Need WCK Custom Fields Creator plugin
Version: 0.1
Author: Johary Ranarimanana
*/
require_once(dirname(__FILE__).'/lib/jpress_gallery_api.php');

DEFINE ('LAZYLOAD_IMAGE', plugins_url('jpress-gallery').'/images/trans.gif');
global $jpress_gallery_img_width, $jpress_gallery_is_mobile;
$jpress_gallery_img_width = 174;
$jpress_gallery_is_mobile = jpress_gallery_api::isMobile(true);
add_action('admin_head', 'jpress_gallery_style');
function jpress_gallery_style(){
	?>
	<style>
		#menu-posts-album .wp-menu-image{
			background: transparent url('<?php echo plugins_url('jpress-gallery');?>/images/jpress_gallery_16_grey.png') no-repeat scroll 6px 6px!important;
		}
		#menu-posts-album .wp-menu-image:hover{
			background: transparent url('<?php echo plugins_url('jpress-gallery');?>/images/jpress_gallery_16_color.png') no-repeat scroll 6px 6px!important;
		}
	</style>
	<?php
}

add_action('admin_footer', 'jpress_gallery_admin_script');
function jpress_gallery_admin_script($hook_suffix){
	global $pagenow;
	if($pagenow == 'upload.php'){
		$albumlist = jpress_gallery_getAlbumList();
		$output = '';
		foreach ($albumlist as $album) {
			$output .= '<option value="' .$album['id'] .'">' . $album['title']. '</option>';
		}
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('.tablenav.top').find('.alignleft:last').each(function(){
				    jQuery('<select id="jpress_gallery_album_id" name="jpress_gallery_album_id"><?php echo $output;?></select><input type="button" name="" id="jpress_gallery_add_to_album" class="button-secondary" value="Ajouter &agrave; l\'album">').appendTo(jQuery(this));
				})
				jQuery('#jpress_gallery_add_to_album').click(function(){
					if(jQuery("#posts-filter input[type='checkbox'][name=*'media']:checked").length>0){
						albumId = jQuery("#jpress_gallery_album_id").val();
						var ids='';
						var glue='';
						jQuery("#posts-filter input[type='checkbox'][name=*'media']:checked").each(function(){
						    ids+= glue+jQuery(this).val();
						    glue = ',';
						})
						var data = {
							action:'jpress_gallery_add_to_album',
							albid: albumId,
							medids: ids
						};
						jQuery.post('<?php echo admin_url( 'admin-ajax.php' );?>', data,function(response){
							if(response=='1'){
								window.location.href = 'post.php?action=edit&post='+albumId;							
							}
						});
					}else{
						if(jQuery("#jpress_gallery_error").length>0){
							jQuery("#jpress_gallery_error").remove();
						}
						jQuery(".subsubsub").prepend('<div class="error" id="jpress_gallery_error"><p>Veuillez s&eacute;l&eacute;ctionner des images.</p></div>');
					}
				});
			});
		</script>
		<?php
	}
}

add_action( 'wp_ajax_jpress_gallery_add_to_album', 'jpress_gallery_add_to_album' );
function jpress_gallery_add_to_album() {
	if(isset($_POST['albid']) && isset($_POST['medids'])) {
		$album_id = $_POST['albid'];
		$media_ids = $_POST['medids'];
		$media_ids = explode(',',$media_ids);
		$media_ids = array_map('intval',$media_ids);
		$photos = jpress_gallery_getPhotoAlbum($album_id);
		if(is_array($photos) && !empty($photos)){
			$photos = array_map("jpress_gallery_custom_array_map_1",$photos);
			foreach ($media_ids as $media_id) {
				$mimetype = get_post_mime_type($media_id);
				if(!in_array($media_id,$photos) && strpos($mimetype,'image/')!==false){
					array_unshift($photos,$media_id);
				}
			}
			$photos = array_map("jpress_gallery_custom_array_map_2",$photos);
			update_post_meta($album_id,'photos-album',$photos);
		}else{
			$photos = array();
			foreach ($media_ids as $media_id) {
				$mimetype = get_post_mime_type($media_id);
				if( strpos($mimetype,'image/')!==false){
					$photos[] = array('photo'=>$media_id);
				}
			}
			add_post_meta($album_id,'photos-album',$photos);
		}
		echo '1';
	}else{
		echo '0';
	}
	die();
}
function jpress_gallery_custom_array_map_1($tab){
	return intval($tab["photo"]);
}
function jpress_gallery_custom_array_map_2($tab){
	return array("photo"=>$tab);
}


add_action('wp_footer', 'jpress_gallery_script');
function jpress_gallery_script(){
	global $jpress_gallery_img_width,$jpress_gallery_is_mobile;
	?>
	<?php if($jpress_gallery_is_mobile):?>
	<style>
		.jpress_gallery_main_container h4 { 
			font-family: 'HelveticaNeue-Roman'!important; 
		}
	</style>
	<?php endif;?>
	<script type="text/javascript">	
		if($('.masonry_container a').length>0){
			$(".masonry_container a").fancybox({
		        'overlayColor':'#000' 		
			});
		}
		
		$('.lazy').lazyload();
		
		jpress_gallery_calculate_img_width();
		if($('.masonry_container').length>0){
		    $('.masonry_container').masonry({
			    itemSelector : '.jpress_gallery_mansory'
		    });
		}
		
		function jpress_gallery_calculate_img_width(){
			$('.jpress_gallery_main_container').each(function(){
			        $container_width = $('.jpress_gallery_main_container').width();
       				$img_width = $container_width/5;
       				$(this).find('.jpress_gallery_mansory').width($img_width);
       				$(this).find('.jpress_gallery_mansory img').width($img_width);
       				$(this).find('.jpress_gallery_mansory').each(function(){
       					$height = $(this).height();	
       					$height = $height*$img_width/<?php echo $jpress_gallery_img_width;?>;
       					$(this).height($height);
       				});
       				
			})
		}
	</script>
	<?php
}

add_action( 'init', 'jpress_gallery_init' );
function jpress_gallery_init(){
	$labels = array(
		'name' => __( 'Albums', 'jpress'),
		'singular_name' => __( 'Album', 'jpress'),
		'add_new' => __( 'Ajouter', 'jpress' ),
		'add_new_item' => __( "Ajouter un nouvel album" ,'jpress'),
		'edit_item' => __( "Modifier l'album" ,'jpress') ,
		'new_item' => __( "Nouvel album" ,'jpress'),
		'all_items' => __( "Tous les albums" ,"jpress"),
		'view_item' => __( "Voir l'album", 'jpress' ),
		'search_items' => __( "Rechercher des albums", 'jpress' ),
		'not_found' =>  __( "Aucun album trouv&eacute;", 'jpress' ),
		'not_found_in_trash' => __( "Aucun album trouv&eacute; dans la corbeille", 'jpress'), 
		'parent_item_colon' => '',
		'menu_name' => 'Album'
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 	
		'has_archive' => false,
		'hierarchical' => false,									
		'capability_type' => 'post',
		'supports' => array( 'title' ,'editor' ),
		'exclude_from_search' => true
	);			
			
	register_post_type( 'album', $args );
	
	jpress_gallery_create_metabox();
	
	// Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "jpress_gallery_add_form_manager_tinymce_plugin");
     add_filter('mce_buttons', 'jpress_gallery_register_jpress_gallery_button');
   }
   
   wp_enqueue_script('jquery-ui', plugins_url('jpress-gallery').'/js/jquery-ui-1.8.17.custom.min.js','jquery','1.8.17');
   wp_enqueue_script('jquery-fancybox', plugins_url('jpress-gallery').'/js/fancybox/jquery.fancybox-1.3.4.js','jquery','1.3.4');
   wp_enqueue_script('jquery-mansory', plugins_url('jpress-gallery').'/js/jquery.masonry.min.js','jquery','2.1.08');
   wp_enqueue_script('jquery-lazyload', plugins_url('jpress-gallery').'/js/jquery.lazyload.min.js','jquery','1.8.0');
   wp_enqueue_style('jquery-fancybox', plugins_url('jpress-gallery').'/js/fancybox/jquery.fancybox-1.3.4.css','jquery','1.3.4');
   wp_enqueue_style('jpress_gallery_main', plugins_url('jpress-gallery').'/css/oan.css');
}

add_action('admin_menu', 'jpress_gallery_admin_menu');
function jpress_gallery_admin_menu(){
	global $submenu, $menu;
	add_submenu_page(
            'edit.php?post_type=album',
            __('Album Manager', 'jpress'),
            __('Album Manager', 'jpress'),
            'activate_plugins',
            'album-manager',
            'jpress_gallery_album_manager_page'
    );
}

function jpress_gallery_create_metabox(){
	global $wpdb;
	if($wpdb->get_var("SELECT ID FROM {$wpdb->prefix}posts WHERE  post_title='Photos Album' AND post_type='wck-meta-box' AND post_status='publish' LIMIT 1")){
		return;
	}
	
	$postarr = array(
		'post_status' => 'publish',
		'post_type'=> 'wck-meta-box',
		'post_title' => 'Photos Album'
	);
	$post_ID = wp_insert_post($postarr);
	$id = absint($post_ID);
		
	//args
	$meta = 'wck_cfc_args';
	$values = array(
		'meta-name'=> 'photos-album',
		'post-type'=> 'album',
		'repeater'=> true,
		'sortable'=> true,
	);
	$results[] = $values;
	update_post_meta( $id, 'wck_cfc_post_type_arg', $values['post-type'] );
	update_post_meta($id, $meta, $results);
	
	//fields
	unset($results);
	$meta = 'wck_cfc_fields';
	$values = array(
		'field-title'=> 'Photo',
		'field-type'=> 'upload',
	);
	$results[] = $values;
	update_post_meta($id, $meta, $results);
}

function jpress_gallery_add_form_manager_tinymce_plugin($plugin_array) {
   $plugin_array['jpress_gallery'] = plugins_url('mce_plugins/editor_plugin.js',__FILE__);
   return $plugin_array;
}
function jpress_gallery_register_jpress_gallery_button($buttons) {
   array_push($buttons, "|", "jpress_gallery");
   return $buttons;
}

function jpress_gallery_getAlbumList(){
	global $wpdb;
	$q = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='album' AND post_status='publish' ORDER BY `ID` ASC";
	$res = $wpdb->get_results($q);
	$albList=array();
	foreach ($res as $item) {
		$albList[]=array(
			'title' => stripslashes($item->post_title),
			'shortcode' => stripslashes($item->post_name),
			'id' => $item->ID,
		);		
	}
	return $albList;
}

function jpress_gallery_getAlbumBySlug($slug){
	global $wpdb;
	$q = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='album' AND post_status='publish' AND post_name='" . $slug."' LIMIT 1";
	$res = $wpdb->get_row($q);
	return $res;
}

function jpress_gallery_getPhotoAlbum($albid){
	$photos = get_post_meta($albid, 'photos-album',true);
	
	if($photos && !is_array($photos))
		$photos = unserialize($photos);

	return $photos;
}

add_image_size( 'jpress_gallery_thumbnail', $jpress_gallery_img_width, null,false );
add_image_size( 'jpress_gallery_large', 800, null,false );

//add_shortcode( 'album', 'jpress_gallery_shortcodeHandler' );
add_filter('the_content', 'jpress_gallery_the_content');
function jpress_gallery_the_content($content){
	$content = preg_replace("#<p>\[album(.*?)\]</p>#", "[album$1]",$content);
	$content = preg_replace_callback("#\[album(.*?)\]#",'jpress_gallery_replace_shortcode',$content);
	return $content;
}

function jpress_gallery_replace_shortcode($matches){
	$slug = trim($matches[1]);
	return jpress_doAlbumBySlug($slug);
}

function jpress_gallery_shortcodeHandler($atts){
	if ( !isset( $atts[ 0 ] ) ) {
		return sprintf(
			__("Le shortcode doit inclure le slug d'un album.  Exemple '%s'", 'jpress'), 
			"[album album-1]"
			);
	}
	return jpress_doAlbumBySlug( $atts[ 0 ] );
}
function jpress_doAlbumBySlug($slug, $echo = false){
	global $jpress_gallery_img_width;
	$html = '';
	$thealbum = jpress_gallery_getAlbumBySlug($slug);
	if ( jpress_gallery_post_password_required($thealbum->ID) ):
    	$html.='<h3>' . $thealbum->post_title . '</h3>';
		global $post;
		$old_post = $post;
		$post = $thealbum;
		$html.= str_replace(array('Cet article','lire'),array('Cet album','visualiser'),get_the_password_form());
		$post = $old_post ;
	else:
		$photos = jpress_gallery_getPhotoAlbum($thealbum->ID);
		$html.='<div class="jpress_gallery_main_container">';
		  $html.='<h4>'.$thealbum->post_title.'</h4>';
		  $html.='<div class="masonry_container">';
			$attachments = array();
			foreach ($photos as $key => $photo) {
				$attachment= get_post(intval($photo["photo"]));	
				$attachments[] = $attachment;
				list($vignette_size,$width,$height) = wp_get_attachment_image_src($attachment->ID, 'jpress_gallery_thumbnail');
				list($large_size) = wp_get_attachment_image_src($attachment->ID, 'jpress_gallery_large');
				$height = $height*$jpress_gallery_img_width/$width;
				$html.='<div class="jpress_gallery_mansory" style="height:'.$height.'px;">';
					$html.='<a rel="collection" href="'.$large_size.'">';
					if($key<=-1):
						$html.='<img src="' . $vignette_size. '" style="width:'. $jpress_gallery_img_width . 'px;"/>';
					else:
						$html.='<img src="' . LAZYLOAD_IMAGE .'" data-original="' .$vignette_size. '" class="lazy" style="width:'. $jpress_gallery_img_width . 'px;"/>';
					endif;
					$html.='</a>';
				$html.='</div>';
			}
		  $html.='</div>';
		  $html.='<div id="jpress_gallery_desc">';
		  	$html.= /*apply_filters('the_content',*/$thealbum->post_content/*)*/;
		  $html.='</div>';
		$html.='</div>';
	endif;
	
	if($echo){
		echo $html;
	}else{
		return $html;
	}
}

function jpress_gallery_post_password_required($post){
	global $wp_hasher;

	$post = get_post($post);

	if ( empty( $post->post_password ) )
		return false;

	if ( ! isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ) )
		return true;
	
	if( isset($_COOKIE["wp-postpass_form_".$post->ID])){
		return false;
	}
	
	if ( empty( $wp_hasher ) ) {
		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		// By default, use the portable hash from phpass
		$wp_hasher = new PasswordHash(8, true);
	}

	$hash = stripslashes( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] );

	if($wp_hasher->CheckPassword( $post->post_password, $hash )){
		if( $_COOKIE["wp-postpass_form_id"]==$post->ID){
			setcookie('wp-postpass_form_'. $post->ID,1, time() + 864000, COOKIEPATH);
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}

add_action ('login_form_postpass', 'jpress_gallery_login_form_postpass');
function jpress_gallery_login_form_postpass(){
	if(isset($_POST["post_id"])){
		setcookie('wp-postpass_form_id', $_POST["post_id"], time() + 864000, COOKIEPATH);
	}
}

add_filter('the_password_form','jpress_gallery_the_password_form');
function jpress_gallery_the_password_form($form){
	global $post;
	return str_replace('</form>','<input type="hidden" name="post_id" value="' . $post->ID .'"/></form>',$form);
}

function jpress_gallery_truncate_text($string, $char_limit =NULL){
  if($string && $char_limit){		
	  if(strlen($string) > $char_limit){
	  	$words = substr($string,0,$char_limit);
	  	return $words .' ...';
	  }else{
	  	return $string;
	  }
  }else{
  	return $string;	
  }
}
?>