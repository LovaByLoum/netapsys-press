<?php
class WP_e_Commerce_Multilingual{
    public $settings;
    private $version = '0.1';
    
    const WPSC_POST_TYPE        = 'wpsc-product';
    const WPSC_CATEGORY         = 'wpsc_product_category';
    const WPSC_TAG              = 'product_tag';
    
    function __construct(){
        global $sitepress, $sitepress_settings;
        
        add_action('init', array($this,'plugin_localization'), 0);
        
        $this->settings = get_option('wpec_wpml_glue');
        $this->install();
        
        if(!is_admin()){       
            if($sitepress->get_current_language() != $sitepress->get_default_language()){
                add_filter('pre_option_shopping_cart_url', array($this, 'filter_pre_option_shopping_cart_url'));
                add_filter('pre_option_product_list_url', array($this, 'filter_pre_option_product_list_url'));
                add_filter('pre_option_checkout_url', array($this, 'filter_pre_option_shopping_cart_url'));
                add_filter('pre_option_transact_url', array($this, 'filter_pre_option_transact_url'));
                add_filter('pre_option_user_account_url', array($this, 'filter_pre_option_user_account_url'));
            }
            add_action('init', array($this, 'init'));
            add_action('template_redirect', array($this, 'set_user_language'));
        }
        
        if($sitepress_settings['language_negotiation_type'] != 3){
            add_filter('wpsc_product_permalink', array($this, 'product_permalink'), 10, 2);            
        }
        
        add_action('init', array($this, 'consistency_check'));
        
        if(is_admin()){
            add_action('admin_print_scripts', array($this, '_force_load_color_picker'));
            // allow downloads to be set for all languages
            add_filter('wpsc_downloads_metabox', array($this, 'filter_wpsc_downloads_metabox'));        
        }    
        
        // retrieve file upload for th eoriginal post when downloads are synced
        add_filter('pre_get_posts', array($this, 'filter_pre_get_posts'));
        
    }
    
    function init(){        
        global $sitepress;
        
        if($sitepress->get_current_language() != $sitepress->get_default_language()){
            // add products in the default language only
            add_filter( 'wpsc_add_to_cart_product_id' , array($this, 'filter_wpsc_add_to_cart_product_id') );
            // add products in the current language in cart - NO NEED FOR THIS. WE CAN STORE IN ANY LANGUAGE. WE'LL TRANSLATE AT RENDER
            // add_filter( 'wpsc_cart_product_title', array($this, 'filter_wpsc_cart_product_title'), 10, 2);
        }
        
        // display product name in the current language
        add_filter('wpsc_cart_item_name', array($this, 'cart_item_name'), 10, 2);     
        add_filter('wpsc_cart_item_url', array($this, 'cart_item_url'), 10, 2);     
        
        // identify special posts by language too. get the current language version
        add_filter('wpec_get_the_post_id_by_shortcode', array($this, 'get_the_post_id_by_shortcode'));
        
        // displayed product names in the correct language in the Purchase History
        add_filter('wpsc_cart_log_product_name', array($this, 'cart_log_product_name'), 10, 2);
        
        // actions when the user submits the checkout        
        add_action('wpsc_submit_checkout', array($this, 'submit_checkout'), 10, 1);
        
    }
    
    function install(){
        
        if(empty($this->settings) || !isset($this->settings['version'])){
            $defaults = array(
                            'version'   => $this->version
                            );
            add_option('wpec_wpml_glue', $defaults, null, 1);
        } 
        
    }
    
    function plugin_localization(){
        load_plugin_textdomain( 'wp_e_commerce_multilingual', false, basename(dirname(__FILE__)) . '/locale');
    }
    
    function consistency_check(){
        global $sitepress;
        if(empty($this->settings['operations']['cats_language'])){
            remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));
            $cats = get_terms(self::WPSC_CATEGORY, array('hide_empty'=>0));
            add_filter('terms_clauses', array($sitepress, 'terms_clauses'));
            foreach($cats as $c){
                $trid = $sitepress->get_element_trid($c->term_taxonomy_id, 'tax_' . self::WPSC_CATEGORY);
                if(empty($trid)){
                    $sitepress->set_element_language_details($c->term_taxonomy_id, 'tax_' . self::WPSC_CATEGORY, null, $sitepress->get_default_language());
                    $trid = $sitepress->get_element_trid($c->term_taxonomy_id, 'tax_' . self::WPSC_CATEGORY);
                }
                // attempt to add translations
                $active_languages = $sitepress->get_active_languages();
                foreach($active_languages as $code => $lang){
                    if($code != $sitepress->get_default_language()){
                        if($c->name == 'Product Category'){
                            $this->_switch_locale($code);
                            $translated_name = __('Product Category', 'wp_e_commerce_multilingual');
                            $this->_switch_locale(); // restore
                        }else{
                            $translated_name = $c->name . ' @' . $code;    
                        }
                        
                        $newterm = wp_insert_term($translated_name, self::WPSC_CATEGORY);
                        if(!is_wp_error($newterm)){
                            $sitepress->set_element_language_details($newterm['term_taxonomy_id'], 'tax_' . self::WPSC_CATEGORY, $trid, $code, $sitepress->get_default_language());    
                        }
                    }
                }
            }
            $this->settings['operations']['cats_language'] = 1;            
            $this->save_settings();
        } 
        
        if(empty($this->settings['operations']['tags_language'])){
            remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));
            $tags = get_terms(self::WPSC_TAG, array('hide_empty'=>0));
            add_filter('terms_clauses', array($sitepress, 'terms_clauses'));
            foreach($tags as $t){
                $trid = $sitepress->get_element_trid($t->term_taxonomy_id, 'tax_' . self::WPSC_TAG);
                if(empty($trid)){
                    $sitepress->set_element_language_details($t->term_taxonomy_id, 'tax_' . self::WPSC_TAG, null, $sitepress->get_default_language());
                }
            }
            $this->settings['operations']['tags_language'] = 1;            
            $this->save_settings();
        }
        
        foreach($sitepress->get_active_languages() as $code=>$lang){
            if($code == $sitepress->get_default_language()) continue;
            if(empty($this->settings['operations']['page_productspage'][$code])){
                $this->_switch_locale($code);
                $this->filter_pre_option_product_list_url($code);    
                $this->_switch_locale();
                $this->settings['operations']['page_productspage'][$code] = 1;                            
                $this->save_settings();
            }
            if(empty($this->settings['operations']['page_shoppingcart'][$code])){
                $this->_switch_locale($code);
                $this->filter_pre_option_shopping_cart_url($code);    
                $this->_switch_locale();
                $this->settings['operations']['page_shoppingcart'][$code] = 1;            
                $this->save_settings();
            }
            if(empty($this->settings['operations']['page_transactionresults'][$code])){
                $this->_switch_locale($code);
                $this->filter_pre_option_transact_url($code);    
                $this->_switch_locale();
                $this->settings['operations']['page_transactionresults'][$code] = 1;            
                $this->save_settings();
            }
            if(empty($this->settings['operations']['page_userlog'][$code])){
                $this->_switch_locale($code);
                $this->filter_pre_option_user_account_url($code);    
                $this->_switch_locale();
                $this->settings['operations']['page_userlog'][$code] = 1;            
                $this->save_settings();
            }
        }
    }
    
    function save_settings(){
        update_option('wpec_wpml_glue', $this->settings);
    }
    
    function set_user_language(){
        global $sitepress;
        
        // send returning user to the right language according to his history
        $icl_cookie_language = $sitepress->get_language_cookie();
        if(empty($icl_cookie_language)){
            if($user_id = get_current_user_id()){
                $wpsc_language = get_user_meta($user_id, '_wpshpcrt_usr_profile_language', true);    
            }else{
                if(isset($_COOKIE['wpsc_multilingual'])){
                    $wpsc_cookie = unserialize(urldecode($_COOKIE['wpsc_multilingual']));
                    $wpsc_language = isset($wpsc_cookie['user_language']) ? $wpsc_cookie['user_language'] : '';    
                }
            }
            
            if($wpsc_language != $sitepress->get_current_language() && in_array($wpsc_language, array_keys($sitepress->get_active_languages()))){
                $args['skip_missing'] = 1;
                $langs = $sitepress->get_ls_languages($args);
                if(isset($langs[$wpsc_language])){
                    $translated = $langs[$wpsc_language]['url'];    
                }
                wp_redirect($translated);
                exit;
            }
            
        }
    }
    
    function filter_wpsc_add_to_cart_product_id($product_id){
        global $sitepress;
        // will return empty if the product does not exist in the default language
        $product_id = icl_object_id($product_id, self::WPSC_POST_TYPE, false, $sitepress->get_default_language());
        return $product_id;
    }
    
    function filter_wpsc_cart_product_title($product_post_title, $product_id){
        // will return default language if the product does not exist in the current language
        $product_id = icl_object_id($product_id, self::WPSC_POST_TYPE, true);        
        $product_post_title = get_the_title($product_id);
        return $product_post_title;
    }
    
    function filter_pre_option_product_list_url($lang = null){
        return $this->_get_url_for_page_by_shortcode('[productspage]', __('Products Page', 'wp_e_commerce_multilingual'), $lang);
    }    
        
    function filter_pre_option_shopping_cart_url($lang = null){
        return $this->_get_url_for_page_by_shortcode('[shoppingcart]', __('Checkout', 'wp_e_commerce_multilingual'), $lang);
    }
    
    function filter_pre_option_transact_url($lang = null){
        return $this->_get_url_for_page_by_shortcode('[transactionresults]', __('Transaction Results', 'wp_e_commerce_multilingual'), $lang);
    }

    function filter_pre_option_user_account_url($lang = null){
        return $this->_get_url_for_page_by_shortcode('[userlog]', __('Your Account', 'wp_e_commerce_multilingual'), $lang);
    }
    
    function _get_url_for_page_by_shortcode($shortcode, $page_title = '', $lang = null){        
        global $wpdb, $sitepress;        
        $current_language = !empty($lang) ? $lang : $sitepress->get_current_language();
        $id = $wpdb->get_var( "
            SELECT p.ID FROM {$wpdb->posts} p 
            JOIN {$wpdb->prefix}icl_translations t  ON t.element_id = p.ID AND t.element_type = 'post_page'
            WHERE p.post_content LIKE '%".$wpdb->escape($shortcode)."%' AND t.language_code='".$current_language."' 
            LIMIT 1");
        if(empty($id)){
            $postarr['post_title'] = $page_title;
            $postarr['post_content'] = $shortcode;
            $postarr['post_type'] = 'page';
            $postarr['post_status'] = 'publish';
            $_POST['icl_post_language'] = $current_language; 
            $original = $wpdb->get_row( "
                SELECT p.ID, p.post_parent FROM {$wpdb->posts} p 
                JOIN {$wpdb->prefix}icl_translations t  ON t.element_id = p.ID AND t.element_type = 'post_page'
                WHERE p.post_content LIKE '%".$wpdb->escape($shortcode)."%' AND t.language_code='".$sitepress->get_default_language()."' 
                LIMIT 1");
            $_POST['icl_translation_of'] = $original->ID; 
            $postarr['post_parent'] = intval(icl_object_id($original->post_parent, 'page', false, $current_language, false));            
            $id = wp_insert_post($postarr);            
            if(is_wp_error($id)) $id = 0;
        }
        $url = get_permalink($id);
        return $url ? $url : false;
    }    
    
    function cart_item_name($name, $id){
        /*
        global $sitepress;
        $trid = $sitepress->get_element_trid($id, 'post_' . self::WPSC_POST_TYPE);
        $tranlations = $sitepress->get_element_translations($trid, 'post_' . self::WPSC_POST_TYPE);
        if(isset($tranlations[$sitepress->get_current_language()]->post_title)){
            $name = $tranlations[$sitepress->get_current_language()]->post_title;
        }
        */        
        $id = icl_object_id($id, self::WPSC_POST_TYPE, false);
        if($id){
            $post = get_post($id);
            $name = $post->post_title;
        }
        
        return $name;
    }
    
    function cart_item_url($url, $post_id){
        $post_id = icl_object_id($post_id, self::WPSC_POST_TYPE, true);
        return get_permalink($post_id);
    }
    
    function product_permalink($url, $post_id){
        global $sitepress;
        
        // deal with the case of adding a product in the default language in the cart when we are on a non-default language
        // make sure we convert to the default language and not to the current language
        // so  -let's determine the language by post id
        $lang_details = $sitepress->get_element_language_details($post_id, 'post_' . self::WPSC_POST_TYPE);
        
        return $sitepress->convert_url($url, $lang_details->language_code);
    }
    
    function get_the_post_id_by_shortcode($post_id){
        return icl_object_id($post_id, 'page', true);
    }
    
    function cart_log_product_name($name, $id){        
        $id = icl_object_id($id, self::WPSC_POST_TYPE, false);
        if($id){
            $post = get_post($id);
            $name = $post->post_title;
        }
        return $name;
    }
    
    function submit_checkout($info){
        global $sitepress;
        if(empty($info['our_user_id'])){        
            $cookie = array(
                'user_language'     => $sitepress->get_current_language(),
                'purchase_log_id'   => $info['purchase_log_id']
            );
            setcookie('wpsc_multilingual', serialize($cookie), time() + 31536000, COOKIEPATH, COOKIE_DOMAIN);
        }else{
            update_user_meta($info['our_user_id'], '_wpshpcrt_usr_profile_language', $sitepress->get_current_language());
        }
        
    }
    
    function _force_load_color_picker(){
        ?><script type="text/javascript" src="<?php site_url() ?>/wp-includes/js/colorpicker.js"></script><?php
    }
    
    function _switch_locale($lang_code = false){
        global $sitepress, $l10n;
        static $original_l10n;
        if(!empty($lang_code)){
            $original_l10n = $l10n['wp_e_commerce_multilingual'];
            load_textdomain('wp_e_commerce_multilingual', dirname(__FILE__) . '/locale/wp_e_commerce_multilingual-' . $sitepress->get_locale($lang_code).'.mo');            
        }else{ // switch back
            $l10n['wp_e_commerce_multilingual'] = $original_l10n;    
        }        
    }
    
    function filter_wpsc_downloads_metabox($out){
        global $post_id, $sitepress;
        
        if(is_null($post_id) && ($sitepress->get_current_language() != $sitepress->get_default_language())){
            if(isset($_GET['trid'])){
                $translations = $sitepress->get_element_translations($_GET['trid'], 'post_wpsc-product');
                $original_post_id = $translations[$sitepress->get_default_language()]->element_id;
                $is_original = false;
            }
        }elseif($sitepress->get_current_language() != $sitepress->get_default_language()){
            $original_post_id = icl_object_id($post_id, 'wpsc-product', false, $sitepress->get_default_language());
            $is_original = false;
        }else{
            $original_post_id = $post_id;
            $is_original = true;
        }
        $downloads_sync = get_post_meta($original_post_id, '_wpscml_copy_downloads', true);
        
        if($is_original){
            $checked =  $downloads_sync ? 'checked="checked"' : '';
            echo '<input type="hidden" name="meta[_wpscml_copy_downloads]" value="0" />';
            echo '<label><input type="checkbox" name="meta[_wpscml_copy_downloads]" ' . $checked . ' value="1" />&nbsp;';
            _e('Downloads are available to all languages.', 'wp_e_commerce_multilingual');
        }else{
            if($downloads_sync){
                echo '<div class="icl_cyan_box">';
                _e('The downloads for this product are synced with the product in the original language. Edit the original product in order to edit the product downloads.', 'wp_e_commerce_multilingual');
                echo '</div>';
            }        
        }
        
        return $out;
    }
    
    function filter_pre_get_posts($q){
        global $sitepress;
        if($q->query_vars['post_type'] == 'wpsc-product-file'){
            $product_id = $q->query_vars['post_parent'];
            if($sitepress->get_current_language() != $sitepress->get_default_language()){
                $original_product_id = icl_object_id($product_id, 'wpsc-product', false, $sitepress->get_default_language());
                if(get_post_meta($original_product_id, '_wpscml_copy_downloads', true)){
                    $q->query_vars['post_parent']   = $original_product_id;
                    $q->query['post_parent']        = $original_product_id;
                }
            }
        }
        return $q;
        
    }
        
}    
