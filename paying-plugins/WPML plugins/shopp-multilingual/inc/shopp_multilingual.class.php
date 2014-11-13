<?php


class Shopp_multilingual{


    function __construct($ext = false){
        add_action('init', array($this,'init'));
    }

    function __destruct(){
        return;
    }

    function init(){
        add_filter('WPML_get_translatable_types', array($this,'get_translatable_types'));
        add_filter('WPML_get_translatable_items', array($this,'get_translatable_items'), 10, 3);
        add_filter('WPML_get_translatable_item', array($this,'get_translatable_item'), 10, 2);
        add_filter('WPML_get_link', array($this,'get_link'), 10, 4);
        add_filter('WPML_filter_link', array($this, 'filter_link'), 10, 2);

        add_filter('shopp_ml_t', array($this, 'shopp_t'), 10, 4);
        add_filter('shopp_ml_pages', array($this, 'shopp_pages'), 10, 1);
		add_action('shopp_cart_request',array($this,'shopp_cart_redirect')); // @added JD, Ingenesis Limited
        
        add_action('shopp_process_order', array(&$this,'process_shopp_order'), 12);
        add_action('shopp_order_success', array(&$this,'shopp_order_success'));

        add_filter('home_url', array($this, 'home_url'), 1, 4) ;

        if (is_admin()) {

            add_action('admin_notices', array($this, '_translation_warning'));

            wp_cache_flush();
        }

    }

    function _translation_warning(){

        // See if translations exist for the Shopp pages.

        $message = '';
        global $Shopp, $sitepress;
        if (isset($Shopp) && isset($sitepress) && defined('WPML_TM_FOLDER')) {
            $default_lang = $sitepress->get_default_language();
            $languages = $sitepress->get_active_languages();
            if ($default_lang != false) {
                $pages = $Shopp->Settings->get('pages');

                foreach($pages as $type => $data) {

                    $details = $sitepress->get_element_language_details($data['id'], 'post_page');
                    if ($details == null) {
                        $sitepress->set_element_language_details($data['id'], 'post_page', false, $default_lang);
                        $details = $sitepress->get_element_language_details($data['id'], 'post_page');
                    }
                    $trid = $details->trid;
                    $translations = $sitepress->get_element_translations($trid, 'post_page');

                    $missing_translations = array();

                    foreach ($languages as $lang) {
                        if ($lang['code'] != $default_lang && !array_key_exists($lang['code'], $translations)) {
                            // translation doesn't exist

                            $missing_translations[] = $lang['code'];
                        }

                    }

                    if (sizeof($missing_translations) > 0) {
                        if ($message == '') {
                            $message = '<p>' . sprintf(__('Some of your <strong>Shopp</strong> pages have not been translated. Go to the %stranslation dashboard%s to translate them.',
                                          'shopp_multilingual'),
                                          '<a href="admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php">', '</a>') . '</p>';
                        }
                    }

                }
            }
            if (!function_exists('icl_st_is_registered_string')) {
                $message .= '<p>' . sprintf(__('Translation of <b>Shopp</b> products require the <b>WPML String Translation</b> plugin to be activated.')) . '</p>';

            }
        }

        if ($message != '') {
            ?>
            <div class="message error"><p><?php printf($message); ?></p></div>
            <?php
        }
    }

    // filter for WP home_url function
    function home_url($url, $path, $orig_scheme, $blog_id){

        global $sitepress, $Shopp;
        if ($_POST['checkout'] == "process" && $Shopp->Order->confirm == 1) {
            $url = $sitepress->convert_url($url);
        }
        return $url;
    }
    
    function process_shopp_order() {
        $test = 1;
    }

    function shopp_order_success($Purchase) {
        global $Shopp;
        $test = 1;
    }

    function shopp_pages($pages) {
        global $sitepress;
        $current_lang = $sitepress->get_current_language();
        foreach ($pages as $index => $page) {
            $details = $sitepress->get_element_language_details($page['id'], 'post_page');
            $trid = $details->trid;
            $translations = $sitepress->get_element_translations($trid, 'post_page');
            if (isset($translations[$current_lang])) {
                $pages[$index]['id'] = $translations[$current_lang]->element_id;
                $pages[$index]['uri'] = get_page_uri($pages[$index]['id']);
                $pages[$index]['title'] = get_the_title($pages[$index]['id']);
            }
        }
        return $pages;
    }


    function shopp_t($original, $options, $property, $Object) {
        if (function_exists('icl_t')) {

            if ($property == 'url') {
                global $Shopp, $sitepress;
                if (get_class($Object) == 'Cart') {
            		if (!isset($Object->_item_loop)) {
                        $pages = $Shopp->Settings->get('pages');
                        $data = $pages['cart'];
                        $details = $sitepress->get_element_language_details($data['id'], 'post_page');
                        $trid = $details->trid;
                        $translations = $sitepress->get_element_translations($trid, 'post_page');
                        $current_lang = $sitepress->get_current_language();
                        if (isset($translations[$current_lang])) {

                            return get_permalink($translations[$current_lang]->element_id);
                        }
                    }

                }
                if (get_class($Object) == 'Order') {
                    $pages = $Shopp->Settings->get('pages');
                    $data = $pages['checkout'];
                    $details = $sitepress->get_element_language_details($data['id'], 'post_page');
                    $trid = $details->trid;
                    $translations = $sitepress->get_element_translations($trid, 'post_page');

                    $current_lang = $sitepress->get_current_language();
                    if (isset($translations[$current_lang])) {

                        return get_permalink($translations[$current_lang]->element_id);
                    }
                }
            }
            if (get_class($Object) == 'Product') {
                switch($property) {
                    case 'name':
                    case 'description':
                    case 'summary':
                        return icl_t('shopp_product', $Object->id . '_' . $property, $original);
                }
            }
            if (get_class($Object) == 'Cart') {
        		if (isset($Object->_item_loop)) {
                    $Item = current($Object->contents);
                    switch($property) {
                        case 'name':
                        case 'desctiption':
                        case 'summary':
                            return icl_t('shopp_product', (string)$Item->product . '_' . $property, $original);
                    }
                }
            }
        }

        return $original;
    }

	/**
	 * Adds WPML URL filtering during Shopp cart redirects
	 *
	 * Uses WPML convert_url method to add ML-specific query_vars
	 * during the Shopp cart redirect process (shopp_cart_redirect hook)
	 * so the ML query_vars are preserved.
	 *
	 * @author Jonathan Davis, Ingenesis Limited
	 *
	 * @return void
	 **/
	function shopp_cart_redirect () {
		global $sitepress; // if needed
		add_filter('home_url', array($sitepress, 'convert_url'));
	}

    function get_translatable_types($types) {
        // Tell WPML that we want shopp_product translated
        $types['shopp_product'] = 'Shopp product';
        return $types;
    }

    function get_translatable_items($items, $type, $filter) {

        if (function_exists('icl_st_is_registered_string')) {
            // Only return items if string translation is available.

            global $sitepress, $wpdb;

            if ($type == 'shopp_product') {

                $default_lang = $sitepress->get_default_language();
                $languages = $sitepress->get_active_languages();

                global $wpdb;
                $products = $wpdb->get_results($wpdb->prepare("
                    SELECT *
                    FROM {$wpdb->prefix}shopp_product
                "));
                foreach($products as $k=>$v){
                    $new_item = new stdClass();

                    $new_item->external_type = true;
                    $new_item->type = 'shopp_product';
                    $new_item->id = $v->id;
                    $new_item->post_type = 'shopp_product';
                    $new_item->post_id = 'external_' . $new_item->post_type . '_' . $v->id;
                    $new_item->post_date = $v->modified;
                    $new_item->post_status = $v->status;
                    $new_item->post_title = $v->name;
                    $new_item->string_data = array();
                    $new_item->string_data['name'] = $v->name;
                    $new_item->string_data['summary'] = $v->summary;
                    $new_item->string_data['description'] = $v->description;

                    // add to the translation table if required
                    $post_trid = $sitepress->get_element_trid($new_item->id, 'post_' . $new_item->post_type);
                    if (!$post_trid) {
                        $sitepress->set_element_language_details($new_item->id, 'post_' . $new_item->post_type, false, $default_lang, null, false);
                    }

                    // register the strings with WPML

                    if (function_exists('icl_st_is_registered_string')) {
                        foreach ($new_item->string_data as $key => $value) {
                            if (!icl_st_is_registered_string('shopp_product', $new_item->id . '_' . $key)) {
                                icl_register_string('shopp_product', $new_item->id . '_' . $key, $value);
                            }
                        }
                    }

                    $post_trid = $sitepress->get_element_trid($new_item->id, 'post_' . $new_item->post_type);
                    $post_translations = $sitepress->get_element_translations($post_trid, 'post_' . $new_item->post_type);

                    global $iclTranslationManagement;

                    $md5 = $iclTranslationManagement->post_md5($new_item);

                    foreach ($post_translations as $lang => $translation) {
                        $res = $wpdb->get_row("SELECT status, needs_update, md5 FROM {$wpdb->prefix}icl_translation_status WHERE translation_id={$translation->translation_id}");
                        if ($res) {
                            if (!$res->needs_update) {
                                // see if the md5 has changed.
                                if ($md5 != $res->md5) {
                                    $res->needs_update = 1;
                                    $wpdb->update($wpdb->prefix.'icl_translation_status', array('needs_update'=>1), array('translation_id'=>$translation->translation_id));
                                }
                            }
                            $_suffix = str_replace('-','_',$lang);
                            $index = 'status_' . $_suffix;
                            $new_item->$index = $res->status;
                            $index = 'needs_update_' . $_suffix;
                            $new_item->$index = $res->needs_update;
                        }
                    }

                    $items[] = $new_item;

                }

            }
        }
        return $items;
    }

    function get_translatable_item($item, $id) {
        if ($item == null) {
            $parts = explode('_', $id);
            if ($parts[0] == 'external') {
                $id = array_pop($parts);

                unset($parts[0]);

                $type = implode('_', $parts);

                if ($type == 'shopp_product') {
                    // this is ours.

                    global $wpdb;
                    $product = $wpdb->get_row($wpdb->prepare("
                        SELECT *
                        FROM {$wpdb->prefix}shopp_product
                        WHERE id = %d
                        ", (int)$id));

                    $item = new stdClass();

                    $item->external_type = true;
                    $item->type = 'shopp_product';
                    $item->id = $product->id;
                    $item->ID = $product->id;
                    $item->post_type = 'shopp_product';
                    $item->post_id = 'external_' . $item->post_type . '_' . $product->id;
                    $item->post_date = $product->modified;
                    $item->post_status = $product->status;
                    $item->post_title = $product->name;
                    $item->string_data = array();
                    $item->string_data['name'] = $product->name;
                    $item->string_data['summary'] = $product->summary;
                    $item->string_data['description'] = $product->description;

                }
            }
        }

        return $item;

    }

    function get_link($item, $id, $anchor, $hide_empty) {
        if ($item == "") {
            $parts = explode('_', $id);
            if ($parts[0] == 'external') {
                $id = array_pop($parts);

                unset($parts[0]);

                $type = implode('_', $parts);

                if ($type == 'shopp_product') {
                    // this is ours.

                    global $wpdb;
                    $product = $wpdb->get_row($wpdb->prepare("
                        SELECT *
                        FROM {$wpdb->prefix}shopp_product
                        WHERE id = %d
                        ", (int)$id));

                    $item = sprintf('<a href="%s">%s</a>', 'admin.php?page=shopp-products&id=' . $id, $product->name);
                }
            }
        }
        return $item;
    }

    /**
     *Add a filter to fix the links in the language switcher so
     *they point to the corresponding pages in different languages.
     */
    
    function filter_link($url, $lang_info) {
        global $Shopp;
        if (isset($Shopp->Product->slug) && $Shopp->Product->slug != "") {
            // we are showing a shop product
            // We need to add the path to the product as WPML path is
            // only to the shop root.

            $product_url = shopp('Product', 'url', array('return' => true));

            $url = $this->_add_language_to_shop_url($product_url, $url, $lang_info);

        } else if (isset($Shopp->Category->loaded) && $Shopp->Category->loaded == 1) {
            // we are showing a shop category
            // We need to add the path to the category as WPML path is
            // only to the shop root.

            $category_url = shopp('Category', 'url', array('return' => true));
            
            $url = $this->_add_language_to_shop_url($category_url, $url, $lang_info);
        }

        return $url;
    }
    
    /**
     *Add the language url to a shop url
     */
    
    function _add_language_to_shop_url($shop_url, $wpml_url, $lang_info) {
        global $sitepress;
        
        // Find the page id
        $count = preg_match('/page_id=([0-9]+)/', $shop_url, $matches);
        if ($count == 1) {
            $page_id = $matches[1];
            $count = preg_match('/page_id=([0-9]+)/', $wpml_url, $matches);
            if ($count == 1) {
                $translated_page_id = $matches[1];
                $shop_url = str_replace('page_id=' . $page_id, 'page_id='. $translated_page_id, $shop_url);
            }
        }

        return $sitepress->convert_url($shop_url, $lang_info['language_code']);
        
    }

}
