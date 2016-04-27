<?php
/*
* Plugin Name: Wordpress Utils
* Description: Met en place des outils utiles pour un site (log, ... )
* Version: 1.0
* Author: Johary
*/

/**
 * pour le favicon créer votre favicon que vous renommerez en favicon.ico et le mettre dans le repertoire images de votre theme
 * pour le logo_admin créer votre logo que vous renommerez en  logo_admin.png et le mettre dans le repertoire images de votre theme
 *
 */
require_once( dirname(__FILE__) . '/lib/cssmin.php' );
require_once( dirname(__FILE__)  . '/lib/jsmin.php' );
class Wordpress_Utils
{
	function __construct(){
		//enlever le p auto et br auto
		add_filter('tiny_mce_before_init', array($this,'wpu_tiny_mce_before_init'),10,2);

		//favicon et admin logo
		add_action('wp_head', array($this,'wpu_favicon'));
		add_action('admin_head', array($this,'wpu_favicon'));
		//add_filter('login_headerurl', array($this,'wpu_custom_login_headerurl'));
		//add_filter('login_headertitle', array($this,'wpu_custom_login_headertitle'));

		// supprimer les notifications du core
		add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
		// supprimer les notifications de plugins
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );

		//suprimmer des widget du dashboard
		add_action('wp_dashboard_setup', array($this,'wpu_remove_dashboard_widgets' ));

        //remplacer jquery par une version recente
        if(!is_admin()){
            wp_deregister_script('jquery');
//            wp_register_script('jquery', get_option('siteurl') .'/wp-content/mu-plugins/lib/jquery-1.9.min.js', false, '1.9.0',true);
//            wp_enqueue_script('jquery', get_option('siteurl') .'/wp-content/mu-plugins/lib/jquery-1.9.min.js', false, '1.9.0',true);
        }

		//charger les images en lazy load
		/*if( !is_admin() && !strpos( $_SERVER['REQUEST_URI'], 'wp-login.php')  && !strpos( $_SERVER['REQUEST_URI'], 'wp-register.php') ){
		    wp_enqueue_script('jquery-lazyload', get_option('siteurl') .'/wp-content/mu-plugins/lib/jquery.lazyload.min.js', array('jquery'), '1.8.0' , true);
		}
		add_filter( 'the_content', array($this,'wpu_the_content'),9);*/

		//ajout de cache js
		add_action('wp_ajax_jcache', array($this,'wpu_jcache'));
		add_action('wp_ajax_nopriv_jcache', array($this,'wpu_jcache'));
//		wp_enqueue_script('jcache', get_option('siteurl') .'/wp-content/mu-plugins/lib/jCache.js', array('jquery'), '1.0' );
		wp_localize_script('jcache','wpu_ajax',array('url' => admin_url( 'admin-ajax.php' )));
	}

	function wpu_tiny_mce_before_init($mceInit, $editor_id){
		$mceInit["wpautop"] = false;
		$mceInit["remove_linebreaks"] = false;
		$mceInit["apply_source_formatting"] = true;
		return $mceInit;
	}

	/**
	 * creer un fichier et insere un contenu
	 *
	 * @param string $filename
	 * @param string $somecontent
	 * @return bool
	 */
	function wpu_create_file($filename, $somecontent, $openmode = "w"){
	    if (!$handle = @fopen($filename, $openmode)) {
	         return false;
	    }
	    if (@fwrite($handle, $somecontent) === FALSE) {
			return false;
	    }
	    @fclose($handle);
		return true;
	}

	/**
	 * log
	*/
	function log($data){
		$uploadir = wp_upload_dir();
		$path_log = realpath($uploadir['basedir']. '/..');
		@mkdir($path_log.'/logs');
		$path_log.= '/logs/log.txt';
		if(is_array($data)){
			$str = http_build_query($data);
		}else{
			$str = $data;
		}
		$str = date('Y-m-d H:i:s') . '     ' . $str;
		$somecontent .= "$str\r\n";
		$this->wpu_create_file($path_log,$somecontent,'a');
	}

	/**
	 * set cache
	*/
	function cache($name, $data, $expired){
		$uploadir = wp_upload_dir();
		$path = realpath($uploadir['basedir']. '/..');
		@mkdir($path.'/cache');
		$path.= '/cache/'. $name . '.cache';
		$expired_timestamp = strtotime("+". $expired ." minutes");

		$optionvalue = get_option('jcache_expired');
		if($optionvalue){
			$option = array();
			$option[$name] = $expired_timestamp;
			$optionvalue = array_merge(unserialize($optionvalue),$option);
			update_option('jcache_expired', serialize($optionvalue));
		}else{
			$option = array();
			$option[$name] = $expired_timestamp;
			add_option('jcache_expired', serialize($option));
		}
		$this->wpu_create_file($path,$data,'w');
	}

	/**
	 * get cache
	*/
	function getcache($name){
		$uploadir = wp_upload_dir();
		$path = realpath($uploadir['basedir']. '/..');
		$path.= '/cache/'. $name . '.cache';

		$optionvalue = unserialize(get_option('jcache_expired'));
		$expired = $optionvalue[$name];
		if ($expired){
			$now = strtotime(date('Y-m-d H:i:s'));
			if(($now - $expired)<0 && is_file($path)){
				return @file_get_contents($path);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	function wpu_custom_login_headerurl($url) {
		return get_bloginfo('url') . '/';
	}
	function wpu_custom_login_headertitle($title) {
		return get_bloginfo('name');
	}

	function wpu_remove_dashboard_widgets() {
		 // Globalize the metaboxes array, this holds all the widgets for wp-admin
		 global $wp_meta_boxes;

		 // Remove the quickpress widget
		 unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);

		 //actualité
		 unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);

		 // Remove the incoming links widget
		 unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);

		 //comment
		 unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);

		 //plugins
		 unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);

		 //blog
		 unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);

	}

	function minify($array_file, $type){
    $file = 'veolia';
    $minified = get_template_directory_uri() . '/' . $type .'/' . $file . '.min.' . $type ;
    $minifiedpath = $this->get_path($minified);

    if (is_file($minifiedpath)){
      return $minified;
    }else{
      $result = '';
      foreach($array_file as $key => $value) {
        if ($type == 'css'){
          if($value){
            $result .= CssMin::minify(file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$type));
          }
          else {
            $result .= file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$key);
          }
        }else{
          if($value){
            $result .= JSMin::minify(file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$type));
          }else{
            $result .= file_get_contents(get_template_directory_uri(). '/' . $type. '/'.$key. '.'.$type);
          }
        }
      }

      if ($type == 'css'){
       	    $result=str_replace('@charset "utf-8";', '',$result);
       	    $result = '@charset "utf-8";'.$result;
      }
      $this->wpu_create_file($minifiedpath,$result);
      return $minified;
    }
	}

	/**
	 * retourne le chemin relatif d'un fichier
	 *
	 * @param string $_zFileUrl
	 * @return string
	 */
	function get_path($_zFileUrl) {
	   $siteurl = get_option('siteurl').'/';
	   if ($_zFileUrl !='') {
		$zRealPath   = str_replace($siteurl, '', $_zFileUrl);
		return $zRealPath;
	   }
	   return false;
	}

	function wpu_the_content($html){
		//charger en lazy load les images wp-image
		$html = preg_replace_callback(
			'!<img(.+?)>!',
			array($this,'wpu_do_lazyload_wpimage'),
			$html
		);
		return $html;
	}
	function wpu_do_lazyload_wpimage($matches){
		$lazyimage = get_option('siteurl') .'/wp-content/mu-plugins/images/trans.gif';
		//recherche l'attribut src
		$src1 = preg_replace('!(src=)(")(.+?)(")!','$1$2'.$lazyimage.'$4 data-original="$3"',$matches[1]);
		if (preg_match('#(class=)(")(.*?)(")#', $src1)){
			$src1 = preg_replace('#(class=)(")(.*?)(")#', '$1$2$3 lazy$4', $src1);
		}else{
			$src1.= ' class="lazy"';
		}
		return '<img'.$src1 . '>';
	}

	function wpu_jcache(){
		switch ($_REQUEST['method']) {
			case 'set':
				$key  = $_REQUEST['key'];
				$value  = $_REQUEST['value'];
				$expired  = $_REQUEST['expired'];
				$this->cache($key, $value,$expired);
				die('1');
				break;
			case 'get':
				$key  = $_REQUEST['key'];
				echo json_encode($this->getcache($key));
				die('');
				break;
		}
	}

    /**
     * redimenssioner l'image, retourne l'url (ne pas redimensiooner si existe)
     *
     * @param string $_zImagePath
     * @param int $_iMaxWidth
     * @param int $_iMaxHeight
     * @param bool $crop
     * @param bool $center
     * @return string
     */
    function image_resize ($_zImagePath, $_zDestName, $_iMaxWidth, $_iMaxHeight, $crop=false, $center=true)
    {
        //if(is_file($_zImagePath)){
        //@ini_set ("memory_limit", -1) ;

        //first check, no need to load image info
        $zUpload_dir = wp_upload_dir();
        $dir = $zUpload_dir['basedir'] ;
        $url = $zUpload_dir['baseurl'] ;
        $suffix = "{$_iMaxWidth}x{$_iMaxHeight}";
        $tmp_destfilename = "{$dir}/{$_zDestName}-{$suffix}.jpg";
        $tmp_desturl = "{$url}/{$_zDestName}-{$suffix}.jpg";
        if (is_file($tmp_destfilename)){
            return $tmp_desturl;
        }

        $tzImageInfos = getimagesize ($_zImagePath) ;
        list($iWidth,$iHeight) = $tzImageInfos ;
        $iOrigWidth = $iWidth ;
        $iOrigHeight = $iHeight ;

        $info = pathinfo($_zImagePath);
        $ext = $info['extension'];
        if(is_null($ext)){
            $ext = 'jpg';
        }

        $name = wp_basename($_zImagePath, ".$ext");
        $destfilename = "{$dir}/{$_zDestName}-{$suffix}.{$ext}";
        $desturl = "{$url}/{$_zDestName}-{$suffix}.{$ext}";
        if (is_file($destfilename)){
            return $desturl;
        }

        if($iOrigWidth <= $_iMaxWidth && $iOrigHeight <= $_iMaxHeight){
            return $_zImagePath;
        }
        $zImageMimeType = $tzImageInfos["mime"] ;
        $tzTokens = explode ("/", $zImageMimeType) ;
        $zImageType = strtoupper (trim ($tzTokens[1])) ;

        $createImageFromXXX = "imagecreatefrom" . $zImageType ;
        $imageXXX = "image" . $zImageType ;

        $oImgSrc = $createImageFromXXX ($_zImagePath) ;
        $oNewImg = imagecreatetruecolor ($_iMaxWidth, $_iMaxHeight) ;

        $dims = image_resize_dimensions($iOrigWidth, $iOrigHeight, $_iMaxWidth, $_iMaxHeight, $crop);
        list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;
        if (!$center){
            $src_x = 0;
            $src_y = 0;
        }

        imagecopyresampled ($oNewImg, $oImgSrc, $dst_x, $dst_y, $src_x , $src_y, $dst_w, $dst_h, $src_w, $src_h) ;

        $imageXXX ($oNewImg, $destfilename) ;

        @chmod ($destfilename, 0666) ;

        return $desturl;
        /*}else{
            return false;
        }*/

    }
}
global $wp_utils;
$wp_utils = new Wordpress_Utils();

if(!function_exists('mp')){
    /**
     * Fonction pour debugger.
     *
     * @param type $var
     * @param type $t
     */
    function mp($var, $t = true) {
        print('<pre style="text-align: left;">');
        print_r($var);
        print('</pre>');
        if($t == true)
            die();
    }
}
if(!function_exists('testNonVide')){
  /**
   * Fonction qui test si la valeur entrée en parematre n'est pas vide
   */
  function testNonVide($value) {
    if(isset($value) && !empty($value)){
      return true;
    }else{
      return false;
    }
  }
}

if(!function_exists('wp_limite_text')){
  /**
   * Fonction qui sert a tronqué un texte par nombre de caractere.
   */
  function wp_limite_text($string, $char_limit = NULL, $plus = '...') {
    if( $string && $char_limit ) {
      if( strlen( $string) > $char_limit ) {
        $words = substr($string, 0, $char_limit);
        $words = explode(' ', $words);
        array_pop($words);
        return implode(' ', $words) . $plus;
      } else {
        return $string;
      }
    } else {
      return $string;
    }
  }
}

if(!function_exists('wp_limite_word')){
    /**
     * Fonction qui sert a tronqué un texte par nombre de mot.
     */
    function wp_limite_word($string, $word_limit =NULL) {
        if($string && $word_limit){
            $words = preg_split("/[\s,-:]+/", $string, -1 ,PREG_SPLIT_OFFSET_CAPTURE);
            if (isset($words[$word_limit-1])){
                $the_word = $words[$word_limit-1][0];
                $offset = intval($words[$word_limit-1][1]);
                $string = substr($string,0, $offset +strlen($the_word));
                if (isset($words[$word_limit])){
                  $string.='...';
                }
            }
            return $string;
        }else{
            return $string;
        }
    }
}


/**
 * fonction qui retourne l'ID du post_meta par meta_value
 */
function wp_get_post_by_template($meta_value){
  $args = array(
    'post_type' => 'page',
    'meta_key' => '_wp_page_template',
    'meta_value' => $meta_value,
    'suppress_filters' => FALSE,
    'numberposts' => 1,
    'fields' => 'ids'
  );
  $posts = get_posts($args);
  if(isset($posts) && !empty($posts)){
    return $posts[0];
  }else{
    global $post;
    return $post->ID;
  }
}

class Browser {
    public static function detect() {
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if ((substr($_SERVER['HTTP_USER_AGENT'],0,6)=="Opera/") || (strpos($userAgent,'opera')) != false ){
            $name = 'opera';
        }
        elseif ((strpos($userAgent,'chrome')) != false) {
            $name = 'chrome';
        }
        elseif ((strpos($userAgent,'safari')) != false && (strpos($userAgent,'chrome')) == false && (strpos($userAgent,'chrome')) == false){
            $name = 'safari';
        }
        elseif (preg_match('/msie/', $userAgent)) {
            $name = 'msie';
        }
        elseif ((strpos($userAgent,'firefox')) != false) {
            $name = 'firefox';
        }
        else {
            $name = 'unrecognized';
        }
        if (preg_match('/.+(?:me|ox|it|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches) && $browser['name']=='safari' ) {
            $version = $matches[1];
        }
        if (preg_match('/.+(?:me|ox|it|on|ra|ie)[\/: ]([\d.]+)/', $userAgent, $matches) && $browser['name']!='safari' ) {
            $version = $matches[1];
        }
        else {
            $version = 'unknown';
        }

        return array(
            'name'      => $name,
            'version'   => $version,
        );
    }
}
?>