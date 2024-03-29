<?php 

//Include admin
include dirname( __FILE__ ) .'/instagram-feed-admin.php';

// Add shortcodes
add_shortcode('instagram-feed', 'display_sb_instagram_feed');
function display_sb_instagram_feed($atts, $content = null) {

    /******************* SHORTCODE OPTIONS ********************/

    $options = get_option('sb_instagram_settings');

    //Create the includes string to set as shortcode default
    $hover_include_string = '';
    if( isset($options[ 'sbi_hover_inc_username' ]) ){
        ($options[ 'sbi_hover_inc_username' ] && $options[ 'sbi_hover_inc_username' ] !== '') ? $hover_include_string .= 'username,' : $hover_include_string .= '';
    }
    //If the username option doesn't exist in the database yet (eg: on plugin update) then set it to be displayed
    if ( !array_key_exists( 'sbi_hover_inc_username', $options ) ) $hover_include_string .= 'username,';

    if( isset($options[ 'sbi_hover_inc_icon' ]) ){
        ($options[ 'sbi_hover_inc_icon' ] && $options[ 'sbi_hover_inc_icon' ] !== '') ? $hover_include_string .= 'icon,' : $hover_include_string .= '';
    }
    if ( !array_key_exists( 'sbi_hover_inc_icon', $options ) ) $hover_include_string .= 'icon,';

    if( isset($options[ 'sbi_hover_inc_date' ]) ){
        ($options[ 'sbi_hover_inc_date' ] && $options[ 'sbi_hover_inc_date' ] !== '') ? $hover_include_string .= 'date,' : $hover_include_string .= '';
    }
    if ( !array_key_exists( 'sbi_hover_inc_date', $options ) ) $hover_include_string .= 'date,';

    if( isset($options[ 'sbi_hover_inc_instagram' ]) ){
        ($options[ 'sbi_hover_inc_instagram' ] && $options[ 'sbi_hover_inc_instagram' ] !== '') ? $hover_include_string .= 'instagram,' : $hover_include_string .= '';
    }
    if ( !array_key_exists( 'sbi_hover_inc_instagram', $options ) ) $hover_include_string .= 'instagram,';

    if( isset($options[ 'sbi_hover_inc_location' ]) ){
        ($options[ 'sbi_hover_inc_location' ] && $options[ 'sbi_hover_inc_location' ] !== '') ? $hover_include_string .= 'location,' : $hover_include_string .= '';
    }
    if( isset($options[ 'sbi_hover_inc_caption' ]) ){
        ($options[ 'sbi_hover_inc_caption' ] && $options[ 'sbi_hover_inc_caption' ] !== '') ? $hover_include_string .= 'caption,' : $hover_include_string .= '';
    }
    if( isset($options[ 'sbi_hover_inc_likes' ]) ){
        ($options[ 'sbi_hover_inc_likes' ] && $options[ 'sbi_hover_inc_likes' ] !== '') ? $hover_include_string .= 'likes,' : $hover_include_string .= '';
    }
    
    //Pass in shortcode attrbutes
    $atts = shortcode_atts(
    array(
        'type' => isset($options[ 'sb_instagram_type' ]) ? $options[ 'sb_instagram_type' ] : '',
        'id' => isset($options[ 'sb_instagram_user_id' ]) ? $options[ 'sb_instagram_user_id' ] : '',
        'hashtag' => isset($options[ 'sb_instagram_hashtag' ]) ? $options[ 'sb_instagram_hashtag' ] : '',
        'location' => isset($options[ 'sb_instagram_location' ]) ? $options[ 'sb_instagram_location' ] : '',
        'coordinates' => isset($options[ 'sb_instagram_coordinates' ]) ? $options[ 'sb_instagram_coordinates' ] : '',
        'width' => isset($options[ 'sb_instagram_width' ]) ? $options[ 'sb_instagram_width' ] : '',
        'widthunit' => isset($options[ 'sb_instagram_width_unit' ]) ? $options[ 'sb_instagram_width_unit' ] : '',
        'widthresp' => isset($options[ 'sb_instagram_feed_width_resp' ]) ? $options[ 'sb_instagram_feed_width_resp' ] : '',
        'height' => isset($options[ 'sb_instagram_height' ]) ? $options[ 'sb_instagram_height' ] : '',
        'heightunit' => isset($options[ 'sb_instagram_height_unit' ]) ? $options[ 'sb_instagram_height_unit' ] : '',
        'sortby' => isset($options[ 'sb_instagram_sort' ]) ? $options[ 'sb_instagram_sort' ] : '',
        'disablelightbox' => isset($options[ 'sb_instagram_disable_lightbox' ]) ? $options[ 'sb_instagram_disable_lightbox' ] : '',
        'num' => isset($options[ 'sb_instagram_num' ]) ? $options[ 'sb_instagram_num' ] : '',
        'cols' => isset($options[ 'sb_instagram_cols' ]) ? $options[ 'sb_instagram_cols' ] : '',
        'disablemobile' => isset($options[ 'sb_instagram_disable_mobile' ]) ? $options[ 'sb_instagram_disable_mobile' ] : '',
        'imagepadding' => isset($options[ 'sb_instagram_image_padding' ]) ? $options[ 'sb_instagram_image_padding' ] : '',
        'imagepaddingunit' => isset($options[ 'sb_instagram_image_padding_unit' ]) ? $options[ 'sb_instagram_image_padding_unit' ] : '',

        //Photo hover styles
        'hovereffect' => isset($options[ 'sb_instagram_hover_effect' ]) ? $options[ 'sb_instagram_hover_effect' ] : '',
        'hovercolor' => isset($options[ 'sb_hover_background' ]) ? $options[ 'sb_hover_background' ] : '',
        'hovertextcolor' => isset($options[ 'sb_hover_text' ]) ? $options[ 'sb_hover_text' ] : '',
        'hoverdisplay' => $hover_include_string,

        'background' => isset($options[ 'sb_instagram_background' ]) ? $options[ 'sb_instagram_background' ] : '',
        'showbutton' => isset($options[ 'sb_instagram_show_btn' ]) ? $options[ 'sb_instagram_show_btn' ] : '',
        'buttoncolor' => isset($options[ 'sb_instagram_btn_background' ]) ? $options[ 'sb_instagram_btn_background' ] : '',
        'buttontextcolor' => isset($options[ 'sb_instagram_btn_text_color' ]) ? $options[ 'sb_instagram_btn_text_color' ] : '',
        'buttontext' => isset($options[ 'sb_instagram_btn_text' ]) ? stripslashes( esc_attr( $options[ 'sb_instagram_btn_text' ] ) ) : '',
        'imageres' => isset($options[ 'sb_instagram_image_res' ]) ? $options[ 'sb_instagram_image_res' ] : '',
        'media' => isset($options[ 'sb_instagram_media_type' ]) ? $options[ 'sb_instagram_media_type' ] : '',
        'showcaption' => isset($options[ 'sb_instagram_show_caption' ]) ? $options[ 'sb_instagram_show_caption' ] : '',
        'captionlength' => isset($options[ 'sb_instagram_caption_length' ]) ? $options[ 'sb_instagram_caption_length' ] : '',
        'captioncolor' => isset($options[ 'sb_instagram_caption_color' ]) ? $options[ 'sb_instagram_caption_color' ] : '',
        'captionsize' => isset($options[ 'sb_instagram_caption_size' ]) ? $options[ 'sb_instagram_caption_size' ] : '',
        'showlikes' => isset($options[ 'sb_instagram_show_meta' ]) ? $options[ 'sb_instagram_show_meta' ] : '',
        'likescolor' => isset($options[ 'sb_instagram_meta_color' ]) ? $options[ 'sb_instagram_meta_color' ] : '',
        'likessize' => isset($options[ 'sb_instagram_meta_size' ]) ? $options[ 'sb_instagram_meta_size' ] : '',
        'hidephotos' => isset($options[ 'sb_instagram_hide_photos' ]) ? $options[ 'sb_instagram_hide_photos' ] : '',

        'showfollow' => isset($options[ 'sb_instagram_show_follow_btn' ]) ? $options[ 'sb_instagram_show_follow_btn' ] : '',
        'followcolor' => isset($options[ 'sb_instagram_folow_btn_background' ]) ? $options[ 'sb_instagram_folow_btn_background' ] : '',
        'followtextcolor' => isset($options[ 'sb_instagram_follow_btn_text_color' ]) ? $options[ 'sb_instagram_follow_btn_text_color' ] : '',
        'followtext' => isset($options[ 'sb_instagram_follow_btn_text' ]) ? stripslashes( esc_attr( $options[ 'sb_instagram_follow_btn_text' ] ) ) : '',
        //Header
        'showheader' => isset($options[ 'sb_instagram_show_header' ]) ? $options[ 'sb_instagram_show_header' ] : '',
        'headercolor' => isset($options[ 'sb_instagram_header_color' ]) ? $options[ 'sb_instagram_header_color' ] : '',
        'headerstyle' => isset($options[ 'sb_instagram_header_style' ]) ? $options[ 'sb_instagram_header_style' ] : '',
        'showfollowers' => isset($options[ 'sb_instagram_show_followers' ]) ? $options[ 'sb_instagram_show_followers' ] : '',
        'showbio' => isset($options[ 'sb_instagram_show_bio' ]) ? $options[ 'sb_instagram_show_bio' ] : '',
        'headerprimarycolor' => isset($options[ 'sb_instagram_header_primary_color' ]) ? $options[ 'sb_instagram_header_primary_color' ] : '',
        'headersecondarycolor' => isset($options[ 'sb_instagram_header_secondary_color' ]) ? $options[ 'sb_instagram_header_secondary_color' ] : '',

        'class' => '',
        'ajaxtheme' => isset($options[ 'sb_instagram_ajax_theme' ]) ? $options[ 'sb_instagram_ajax_theme' ] : '',
        'cachetime' => isset($options[ 'sb_instagram_cache_time' ]) ? $options[ 'sb_instagram_cache_time' ] : '',
        'blockusers' => isset($options[ 'sb_instagram_block_users' ]) ? $options[ 'sb_instagram_block_users' ] : '',
        'excludewords' => isset($options[ 'sb_instagram_exclude_words' ]) ? $options[ 'sb_instagram_exclude_words' ] : '',
        'includewords' => isset($options[ 'sb_instagram_include_words' ]) ? $options[ 'sb_instagram_include_words' ] : '',
        'maxrequests' => isset($options[ 'sb_instagram_requests_max' ]) ? $options[ 'sb_instagram_requests_max' ] : '',

        //Carousel
        'carousel' => isset($options[ 'sb_instagram_carousel' ]) ? $options[ 'sb_instagram_carousel' ] : '',
        'carouselarrows' => isset($options[ 'sb_instagram_carousel_arrows' ]) ? $options[ 'sb_instagram_carousel_arrows' ] : '',
        'carouselpag' => isset($options[ 'sb_instagram_carousel_pag' ]) ? $options[ 'sb_instagram_carousel_pag' ] : '',
        'carouselautoplay' => isset($options[ 'sb_instagram_carousel_autoplay' ]) ? $options[ 'sb_instagram_carousel_autoplay' ] : '',
        'carouseltime' => isset($options[ 'sb_instagram_carousel_interval' ]) ? $options[ 'sb_instagram_carousel_interval' ] : ''

    ), $atts);

    /******************* VARS ********************/

    //Config
    $sb_instagram_type = trim($atts['type']);
    $sb_instagram_user_id = trim($atts['id'], " ,");
    $sb_instagram_hashtag = trim(str_replace( '#', '', trim($atts['hashtag']) ), " ,"); //Remove hashtags and trailing commas
    $sb_instagram_location = trim($atts['location'], " ,");
    $sb_instagram_coordinates = trim($atts['coordinates'], " ,");

    //Container styles
    $sb_instagram_width = $atts['width'];
    $sb_instagram_width_unit = $atts['widthunit'];
    $sb_instagram_height = $atts['height'];
    $sb_instagram_height_unit = $atts['heightunit'];
    $sb_instagram_image_padding = $atts['imagepadding'];
    $sb_instagram_image_padding_unit = $atts['imagepaddingunit'];
    $sb_instagram_background = str_replace('#', '', $atts['background']);
    $sb_hover_background = $atts['hovercolor'];
    $sb_hover_text = str_replace('#', '', $atts['hovertextcolor']);

    //Set to be 100% width on mobile?
    $sb_instagram_width_resp = $atts[ 'widthresp' ];
    ( $sb_instagram_width_resp == 'on' || $sb_instagram_width_resp == 'true' || $sb_instagram_width_resp == true ) ? $sb_instagram_width_resp = true : $sb_instagram_width_resp = false;
    if( $atts[ 'widthresp' ] == 'false' ) $sb_instagram_width_resp = false;

    //Layout options
    $sb_instagram_cols = $atts['cols'];

    $sb_instagram_styles = 'style="';
    if($sb_instagram_cols == 1) $sb_instagram_styles .= 'max-width: 640px; ';
    if ( !empty($sb_instagram_width) ) $sb_instagram_styles .= 'width:' . $sb_instagram_width . $sb_instagram_width_unit .'; ';
    if ( !empty($sb_instagram_height) && $sb_instagram_height != '0' ) $sb_instagram_styles .= 'height:' . $sb_instagram_height . $sb_instagram_height_unit .'; ';
    if ( !empty($sb_instagram_background) ) $sb_instagram_styles .= 'background-color: #' . $sb_instagram_background . '; ';
    if ( !empty($sb_instagram_image_padding) ) $sb_instagram_styles .= 'padding-bottom: ' . (2*intval($sb_instagram_image_padding)).$sb_instagram_image_padding_unit . '; ';
    $sb_instagram_styles .= '"';

    //Header
    $sb_instagram_show_header = $atts['showheader'];
    ( $sb_instagram_show_header == 'on' || $sb_instagram_show_header == 'true' || $sb_instagram_show_header == true ) ? $sb_instagram_show_header = true : $sb_instagram_show_header = false;
    if( $atts[ 'showheader' ] === 'false' ) $sb_instagram_show_header = false;

    $sb_instagram_header_style = $atts['headerstyle'];

    $sb_instagram_show_followers = $atts['showfollowers'];
    ( $sb_instagram_show_followers == 'on' || $sb_instagram_show_followers == 'true' || $sb_instagram_show_followers ) ? $sb_instagram_show_followers = 'true' : $sb_instagram_show_followers = 'false';
    if( $atts[ 'showfollowers' ] === 'false' ) $sb_instagram_show_followers = false;
    //As this is a new option in the update then set it to be true if it doesn't exist yet
    if ( !array_key_exists( 'sb_instagram_show_followers', $options ) ) $sb_instagram_show_followers = 'true';

    $sb_instagram_show_bio = $atts['showbio'];
    ( $sb_instagram_show_bio == 'on' || $sb_instagram_show_bio == 'true' || $sb_instagram_show_bio ) ? $sb_instagram_show_bio = 'true' : $sb_instagram_show_bio = 'false';
    if( $atts[ 'showbio' ] === 'false' ) $sb_instagram_show_bio = false;
    //As this is a new option in the update then set it to be true if it doesn't exist yet
    if ( !array_key_exists( 'sb_instagram_show_bio', $options ) ) $sb_instagram_show_bio = 'true';

    $sb_instagram_header_color = str_replace('#', '', $atts['headercolor']);

    $sb_instagram_header_primary_color = str_replace('#', '', $atts['headerprimarycolor']);
    $sb_instagram_header_secondary_color = str_replace('#', '', $atts['headersecondarycolor']);

    //Load more button
    $sb_instagram_show_btn = $atts['showbutton'];
    ( $sb_instagram_show_btn == 'on' || $sb_instagram_show_btn == 'true' || $sb_instagram_show_btn == true ) ? $sb_instagram_show_btn = true : $sb_instagram_show_btn = false;
    if( $atts[ 'showbutton' ] === 'false' ) $sb_instagram_show_btn = false;

    $sb_instagram_btn_background = str_replace('#', '', $atts['buttoncolor']);
    $sb_instagram_btn_text_color = str_replace('#', '', $atts['buttontextcolor']);
    //Load more button styles
    $sb_instagram_button_styles = 'style="';
    if ( !empty($sb_instagram_btn_background) ) $sb_instagram_button_styles .= 'background: #'.$sb_instagram_btn_background.'; ';
    if ( !empty($sb_instagram_btn_text_color) ) $sb_instagram_button_styles .= 'color: #'.$sb_instagram_btn_text_color.';';
    $sb_instagram_button_styles .= '"';

    //Follow button vars
    $sb_instagram_show_follow_btn = $atts['showfollow'];
    ( $sb_instagram_show_follow_btn == 'on' || $sb_instagram_show_follow_btn == 'true' || $sb_instagram_show_follow_btn == true ) ? $sb_instagram_show_follow_btn = true : $sb_instagram_show_follow_btn = false;
    if( $atts[ 'showfollow' ] === 'false' ) $sb_instagram_show_follow_btn = false;

    $sb_instagram_follow_btn_background = str_replace('#', '', $atts['followcolor']);
    $sb_instagram_follow_btn_text_color = str_replace('#', '', $atts['followtextcolor']);
    $sb_instagram_follow_btn_text = $atts['followtext'];
    //Follow button styles
    $sb_instagram_follow_btn_styles = 'style="';
    if ( !empty($sb_instagram_follow_btn_background) ) $sb_instagram_follow_btn_styles .= 'background: #'.$sb_instagram_follow_btn_background.'; ';
    if ( !empty($sb_instagram_follow_btn_text_color) ) $sb_instagram_follow_btn_styles .= 'color: #'.$sb_instagram_follow_btn_text_color.';';
    $sb_instagram_follow_btn_styles .= '"';
    //Follow button HTML
    $sb_instagram_follow_btn_html = '<div class="sbi_follow_btn"><a href="http://instagram.com/" '.$sb_instagram_follow_btn_styles.' target="_blank"><i class="fa fa-instagram"></i>'.stripslashes($sb_instagram_follow_btn_text).'</a></div>';

    //Text styles
    $sb_instagram_show_caption = $atts['showcaption'];
    $sb_instagram_caption_length = $atts['captionlength'];
    $sb_instagram_caption_color = str_replace('#', '', $atts['captioncolor']);
    $sb_instagram_caption_size = $atts['captionsize'];

    //Meta styles
    $sb_instagram_show_meta = $atts['showlikes'];
    $sb_instagram_meta_color = str_replace('#', '', $atts['likescolor']);
    $sb_instagram_meta_size = $atts['likessize'];

    //Lighbox
    $sb_instagram_disable_lightbox = $atts['disablelightbox'];
    ( $sb_instagram_disable_lightbox == 'on' || $sb_instagram_disable_lightbox == 'true' || $sb_instagram_disable_lightbox == true ) ? $sb_instagram_disable_lightbox = 'true' : $sb_instagram_disable_lightbox = 'false';
    if( $atts[ 'disablelightbox' ] === 'false' ) $sb_instagram_disable_lightbox = 'false';


    //Mobile
    $sb_instagram_disable_mobile = $atts['disablemobile'];
    ( $sb_instagram_disable_mobile == 'on' || $sb_instagram_disable_mobile == 'true' || $sb_instagram_disable_mobile == true ) ? $sb_instagram_disable_mobile = ' sbi_disable_mobile' : $sb_instagram_disable_mobile = '';
    if( $atts[ 'disablemobile' ] === 'false' ) $sb_instagram_disable_mobile = '';

    //Class
    !empty( $atts['class'] ) ? $sbi_class = ' ' . trim($atts['class']) : $sbi_class = '';

    //Media type
    $sb_instagram_media_type = $atts['media'];
    if( !isset($sb_instagram_media_type) || empty($sb_instagram_media_type) ) $sb_instagram_media_type = 'all';

    //Ajax theme
    $sb_instagram_ajax_theme = $atts['ajaxtheme'];
    ( $sb_instagram_ajax_theme == 'on' || $sb_instagram_ajax_theme == 'true' || $sb_instagram_ajax_theme == true ) ? $sb_instagram_ajax_theme = true : $sb_instagram_ajax_theme = false;
    if( $atts[ 'ajaxtheme' ] === 'false' ) $sb_instagram_ajax_theme = false;

    //Caching
    $sb_instagram_cache_time = trim($atts['cachetime']);
    if ( !array_key_exists( 'sb_instagram_cache_time', $options ) || $sb_instagram_cache_time == '' ) $sb_instagram_cache_time = '1';
    ($sb_instagram_cache_time == 0 || $sb_instagram_cache_time == '0') ? $sb_instagram_disable_cache = 'true' : $sb_instagram_disable_cache = 'false';

    //API requests
    $sb_instagram_requests_max = trim($atts['maxrequests']);
    if( $sb_instagram_requests_max == '0' ) $sb_instagram_requests_max = 1;
    if( empty($sb_instagram_requests_max) ) $sb_instagram_requests_max = 5;
    $sb_instagram_requests_max = min($sb_instagram_requests_max, 10);

    //Carousel
    $sbi_carousel = $atts['carousel'];
    ( $sbi_carousel == 'true' || $sbi_carousel == 'on' || $sbi_carousel == true || $sbi_carousel == 1 || $sbi_carousel == '1' ) ? $sbi_carousel = 'true' : $sbi_carousel = 'false';
    if( $atts[ 'carousel' ] === 'false' ) $sbi_carousel = 'false';

    $sbi_carousel_class = '';
    $sbi_carousel_options = '';
    $sb_instagram_cols_class = $sb_instagram_cols;
    if($sbi_carousel == 'true'){
        $sbi_carousel_class = 'class="sbi_carousel" ';
        $sb_instagram_show_btn = false;
        $sb_instagram_cols_class = '1';
    }
    $sb_instagram_carousel_arrows = $atts['carouselarrows'];
    ( $sb_instagram_carousel_arrows == 'true' || $sb_instagram_carousel_arrows == 'on' || $sb_instagram_carousel_arrows == 1 || $sb_instagram_carousel_arrows == '1' ) ? $sb_instagram_carousel_arrows = 'true' : $sb_instagram_carousel_arrows = 'false';
    if( $atts[ 'carouselarrows' ] === false ) $sb_instagram_carousel_arrows = 'false';

    $sb_instagram_carousel_pag = $atts['carouselpag'];
    ( $sb_instagram_carousel_pag == 'true' || $sb_instagram_carousel_pag == 'on' || $sb_instagram_carousel_pag == 1 || $sb_instagram_carousel_pag == '1' ) ? $sb_instagram_carousel_pag = 'true' : $sb_instagram_carousel_pag = 'false';
    if( $atts[ 'carouselpag' ] === false ) $sb_instagram_carousel_pag = 'false';

    $sb_instagram_carousel_autoplay = $atts['carouselautoplay'];
    ( $sb_instagram_carousel_autoplay == 'true' || $sb_instagram_carousel_autoplay == 'on' || $sb_instagram_carousel_autoplay == 1 || $sb_instagram_carousel_autoplay == '1' ) ? $sb_instagram_carousel_autoplay = 'true' : $sb_instagram_carousel_autoplay = 'false';
    if( $atts[ 'carouselautoplay' ] === false ) $sb_instagram_carousel_autoplay = 'false';

    $sb_instagram_carousel_interval = intval($atts['carouseltime']);


    //Filters
    //Exclude words
    isset($atts[ 'excludewords' ]) ? $sb_instagram_exclude_words = trim($atts['excludewords']) : $sb_instagram_exclude_words = '';

    //Explode string by commas
    // $sb_instagram_exclude_words = explode(",", trim( $sb_instagram_exclude_words ) );

    //Include words
    isset($atts[ 'includewords' ]) ? $sb_instagram_include_words = trim($atts['includewords']) : $sb_instagram_include_words = '';

    //Explode string by commas
    // $sb_instagram_include_words = explode(",", trim( $sb_instagram_include_words ) );

    //Access token
    isset($sb_instagram_settings[ 'sb_instagram_at' ]) ? $sb_instagram_at = trim($sb_instagram_settings['sb_instagram_at']) : $sb_instagram_at = '';


    /* CACHING */
    //Create the transient name from the plugin settings
    $sb_instagram_include_words = $atts['includewords'];
    $sb_instagram_exclude_words = $atts['excludewords'];
    $sbi_cache_string_include = '';
    $sbi_cache_string_exclude = '';

    //Convert include words array into a string consisting of 3 chars each
    if( !empty($sb_instagram_include_words) ){
        $sb_instagram_include_words_arr = explode(',', $sb_instagram_include_words);

        foreach($sb_instagram_include_words_arr as $sbi_word){
            $sbi_include_word = str_replace(str_split(' #'), '', $sbi_word);
            $sbi_cache_string_include .= substr($sbi_include_word, 0, 3);
        }
    }

    //Convert exclude words array into a string consisting of 3 chars each
    if( !empty($sb_instagram_exclude_words) ){
        $sb_instagram_exclude_words_arr = explode(',', $sb_instagram_exclude_words);

        foreach($sb_instagram_exclude_words_arr as $sbi_word){
            $sbi_exclude_word = str_replace(str_split(' #'), '', $sbi_word);
            $sbi_cache_string_exclude .= substr($sbi_exclude_word, 0, 3);
        }
    }

    //Figure out how long the first part of the caching string should be
    $sbi_cache_string_include_length = strlen($sbi_cache_string_include);
    $sbi_cache_string_exclude_length = strlen($sbi_cache_string_exclude);
    $sbi_cache_string_length = 40 - min($sbi_cache_string_include_length + $sbi_cache_string_exclude_length, 20);

    //Create the first part of the caching string
    $sbi_transient_name = 'sbi_';
    if( $sb_instagram_type == 'user' ) $sbi_transient_name .= substr( str_replace(str_split(', '), '', $sb_instagram_user_id), 0, $sbi_cache_string_length); //Remove commas and spaces and limit chars
    if( $sb_instagram_type == 'hashtag' ) $sbi_transient_name .= substr( str_replace(str_split(', #'), '', $sb_instagram_hashtag), 0, $sbi_cache_string_length);
    if( $sb_instagram_type == 'location' ) $sbi_transient_name .= substr( str_replace(str_split(', -.()'), '', $sb_instagram_location), 0, $sbi_cache_string_length);
    if( $sb_instagram_type == 'liked' ) $sbi_transient_name .= 'liked';

    //Find the length of the string so far, and then however many chars are left we can use this for filters
    $sbi_cache_string_length = strlen($sbi_transient_name);
    $sbi_cache_string_length = 44 - intval($sbi_cache_string_length);

    //Set the length of each filter string
    if( $sbi_cache_string_exclude_length < $sbi_cache_string_length/2 ){
        $sbi_cache_string_include = substr($sbi_cache_string_include, 0, $sbi_cache_string_length - $sbi_cache_string_exclude_length);
    } else {
        //Exclude string
        if( strlen($sbi_cache_string_exclude) == 0 ){
            $sbi_cache_string_include = substr($sbi_cache_string_include, 0, $sbi_cache_string_length );
        } else {
            $sbi_cache_string_include = substr($sbi_cache_string_include, 0, ($sbi_cache_string_length/2) );
        }
        //Include string
        if( strlen($sbi_cache_string_include) == 0 ){
            $sbi_cache_string_exclude = substr($sbi_cache_string_exclude, 0, $sbi_cache_string_length );
        } else {
            $sbi_cache_string_exclude = substr($sbi_cache_string_exclude, 0, ($sbi_cache_string_length/2) );
        }
    }

    //Add both parts of the caching string together and make sure it doesn't exceed 45
    $sbi_transient_name .= $sbi_cache_string_include . $sbi_cache_string_exclude;
    $sbi_transient_name = substr($sbi_transient_name, 0, 45);

    // delete_transient($sbi_transient_name);

    //Check whether the cache transient exists in the database
    ( false === ( $sbi_cache_exists = get_transient( $sbi_transient_name ) ) ) ? $sbi_cache_exists = false : $sbi_cache_exists = true;
    ($sbi_cache_exists) ? $sbi_cache_exists = 'true' : $sbi_cache_exists = 'false';

    $sbiHeaderCache = 'false';
    if( $sb_instagram_type == 'user' ){
        //If it's a user then add the header cache check to the feed
        $sb_instagram_user_id_arr = explode(',', $sb_instagram_user_id);
        $sbi_header_transient_name = 'sbi_header_' . trim($sb_instagram_user_id_arr[0]);
        $sbi_header_transient_name = substr($sbi_header_transient_name, 0, 45);

        //Check for the header cache
        ( false === ( $sbi_header_cache_exists = get_transient( $sbi_header_transient_name ) ) ) ? $sbi_header_cache_exists = false : $sbi_header_cache_exists = true;

        ($sbi_header_cache_exists) ? $sbiHeaderCache = 'true' : $sbiHeaderCache = 'false';
    }
    /* END CACHING */


    /******************* CONTENT ********************/
    $sb_instagram_content = '<div id="sb_instagram" class="sbi' . $sbi_class . $sb_instagram_disable_mobile;
    if ( !empty($sb_instagram_height) ) $sb_instagram_content .= ' sbi_fixed_height ';
    $sb_instagram_content .= ' sbi_col_' . trim($sb_instagram_cols_class);
    if ( $sb_instagram_width_resp ) $sb_instagram_content .= ' sbi_width_resp';
    $sb_instagram_content .= '" '.$sb_instagram_styles .' data-id="' . $sb_instagram_user_id . '" data-num="' . trim($atts['num']) . '" data-res="' . trim($atts['imageres']) . '" data-cols="' . trim($sb_instagram_cols) . '" data-options=\'{&quot;showcaption&quot;: &quot;'.$sb_instagram_show_caption.'&quot;, &quot;captionlength&quot;: &quot;'.$sb_instagram_caption_length.'&quot;, &quot;captioncolor&quot;: &quot;'.$sb_instagram_caption_color.'&quot;, &quot;captionsize&quot;: &quot;'.$sb_instagram_caption_size.'&quot;, &quot;showlikes&quot;: &quot;'.$sb_instagram_show_meta.'&quot;, &quot;likescolor&quot;: &quot;'.$sb_instagram_meta_color.'&quot;, &quot;likessize&quot;: &quot;'.$sb_instagram_meta_size.'&quot;, &quot;sortby&quot;: &quot;'.$atts['sortby'].'&quot;, &quot;hashtag&quot;: &quot;'.$sb_instagram_hashtag.'&quot;, &quot;type&quot;: &quot;'.$sb_instagram_type.'&quot;, &quot;hovercolor&quot;: &quot;'.sbi_hextorgb($sb_hover_background).'&quot;, &quot;hovertextcolor&quot;: &quot;'.sbi_hextorgb($sb_hover_text).'&quot;, &quot;hoverdisplay&quot;: &quot;'.$atts['hoverdisplay'].'&quot;, &quot;hovereffect&quot;: &quot;'.$atts['hovereffect'].'&quot;, &quot;headercolor&quot;: &quot;'.$sb_instagram_header_color.'&quot;, &quot;headerprimarycolor&quot;: &quot;'.$sb_instagram_header_primary_color.'&quot;, &quot;headersecondarycolor&quot;: &quot;'.$sb_instagram_header_secondary_color.'&quot;, &quot;disablelightbox&quot;: &quot;'.$sb_instagram_disable_lightbox.'&quot;, &quot;disablecache&quot;: &quot;'.$sb_instagram_disable_cache.'&quot;, &quot;location&quot;: &quot;'.$sb_instagram_location.'&quot;, &quot;coordinates&quot;: &quot;'.$sb_instagram_coordinates.'&quot;, &quot;maxrequests&quot;: &quot;'.$sb_instagram_requests_max.'&quot;, &quot;headerstyle&quot;: &quot;'.$sb_instagram_header_style.'&quot;, &quot;showfollowers&quot;: &quot;'.$sb_instagram_show_followers.'&quot;, &quot;showbio&quot;: &quot;'.$sb_instagram_show_bio.'&quot;, &quot;carousel&quot;: &quot;['.$sbi_carousel.', '.$sb_instagram_carousel_arrows.', '.$sb_instagram_carousel_pag.', '.$sb_instagram_carousel_autoplay.', '.$sb_instagram_carousel_interval.']&quot;, &quot;imagepadding&quot;: &quot;'.$sb_instagram_image_padding.'&quot;, &quot;imagepaddingunit&quot;: &quot;'.$sb_instagram_image_padding_unit.'&quot;, &quot;media&quot;: &quot;'.$sb_instagram_media_type.'&quot;, &quot;includewords&quot;: &quot;'.$sb_instagram_include_words.'&quot;, &quot;excludewords&quot;: &quot;'.$sb_instagram_exclude_words.'&quot;, &quot;sbiCacheExists&quot;: &quot;'.$sbi_cache_exists.'&quot;, &quot;sbiHeaderCache&quot;: &quot;'.$sbiHeaderCache.'&quot;}\'>';

    //Header
    if( $sb_instagram_show_header ){
        $sb_instagram_content .= '<div class="sb_instagram_header sbi_feed_type_' . $sb_instagram_type;
        if($sb_instagram_type !== 'user') $sb_instagram_content .= ' sbi_header_type_generic';
        if( $sb_instagram_header_style == 'boxed' ) $sb_instagram_content .= ' sbi_header_style_boxed';
        $sb_instagram_content .= '"';
        if( $sb_instagram_header_style == 'boxed' ) $sb_instagram_content .= ' data-follow-text="' . $sb_instagram_follow_btn_text . '"';
        $sb_instagram_content .= 'style="';
        if( $sb_instagram_header_style !== 'boxed' ) $sb_instagram_content .= 'padding: '.(intval($sb_instagram_image_padding)).$sb_instagram_image_padding_unit.' '.(2*intval($sb_instagram_image_padding)).$sb_instagram_image_padding_unit . ';';
        if( intval($sb_instagram_image_padding) < 10 && $sb_instagram_header_style !== 'boxed' ) $sb_instagram_content .= ' margin-bottom: 10px;';
        if( $sb_instagram_header_style == 'boxed' ) $sb_instagram_content .= ' background: #'.$sb_instagram_header_primary_color.';';
        $sb_instagram_content .= '"></div>';
    }

    //Images container
    $sb_instagram_content .= '<div id="sbi_images" '.$sbi_carousel_class.'style="padding: '.$sb_instagram_image_padding . $sb_instagram_image_padding_unit .';">';

    //Loader
    $sb_instagram_content .= '<div class="sbi_loader fa-spin"></div>';

    //Error messages
    if( $sb_instagram_type == 'user' && ( empty($sb_instagram_user_id) || !isset($sb_instagram_user_id) ) ) $sb_instagram_content .= '<p>Please enter a User ID on the Instagram plugin Settings page</p>';

    if( $sb_instagram_type == 'hashtag' && (empty($sb_instagram_hashtag) || !isset($sb_instagram_hashtag) ) ) $sb_instagram_content .= '<p>Please enter a Hashtag on the Instagram plugin Settings page</p>';

    if( empty($options[ 'sb_instagram_at' ]) || !isset($options[ 'sb_instagram_at' ]) ) $sb_instagram_content .= '<p>Please enter an Access Token on the Instagram Feed plugin Settings page</p>';

    $sb_instagram_content .= '</div><div id="sbi_load"';
    if($sb_instagram_image_padding == 0 || !isset($sb_instagram_image_padding)) $sb_instagram_content .= ' style="padding-top: 5px"';
    $sb_instagram_content .= '>';

    //Load More button
    if( $sb_instagram_show_btn ) $sb_instagram_content .= '<a class="sbi_load_btn" href="javascript:void(0);" '.$sb_instagram_button_styles.'><span class="sbi_btn_text">'.$atts['buttontext'].'</span><span class="fa fa-spinner fa-pulse"></span></a>';

    //Follow button
    if( $sb_instagram_show_follow_btn && $sb_instagram_type == 'user' ) $sb_instagram_content .= $sb_instagram_follow_btn_html;

    $sb_instagram_content .= '</div>'; //End #sbi_load
    
    $sb_instagram_content .= '</div>'; //End #sb_instagram

    //If using an ajax theme then add the JS to the bottom of the feed
    if($sb_instagram_ajax_theme){

        //Hide photos
        (isset($atts[ 'hidephotos' ]) && !empty($atts[ 'hidephotos' ])) ? $sb_instagram_hide_photos = trim($atts['hidephotos']) : $sb_instagram_hide_photos = '';

        //Block users
        (isset($atts[ 'blockusers' ]) && !empty($atts[ 'blockusers' ])) ? $sb_instagram_block_users = trim($atts['blockusers']) : $sb_instagram_block_users = '';

        $sb_instagram_content .= '<script type="text/javascript">var sb_instagram_js_options = {"sb_instagram_at":"'.trim($options['sb_instagram_at']).'", "sb_instagram_hide_photos":"'.$sb_instagram_hide_photos.'", "sb_instagram_block_users":"'.$sb_instagram_block_users.'"};</script>';
        $sb_instagram_content .= "<script type='text/javascript' src='".plugins_url( '/js/sb-instagram.js?ver='.SBIVER , __FILE__ )."'></script>";
    }
 
    //Return our feed HTML to display
    return $sb_instagram_content;

}


#############################

//Convert Hex to RGB
function sbi_hextorgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return implode(",", $rgb); // returns the rgb values separated by commas
   //return $rgb; // returns an array with the rgb values
}

//Allows shortcodes in theme
add_filter('widget_text', 'do_shortcode');

function sbi_cache_photos() {
    global $wpdb;

    $sb_instagram_settings = get_option('sb_instagram_settings');
    //If the caching time doesn't exist in the database then set it to be 1 hour
    ( !array_key_exists( 'sb_instagram_cache_time', $sb_instagram_settings ) ) ? $sb_instagram_cache_time = 1 : $sb_instagram_cache_time = $sb_instagram_settings['sb_instagram_cache_time'];
    ( !array_key_exists( 'sb_instagram_cache_time_unit', $sb_instagram_settings ) ) ? $sb_instagram_cache_time_unit = 'minutes' : $sb_instagram_cache_time_unit = $sb_instagram_settings['sb_instagram_cache_time_unit'];

    //Calculate the cache time in seconds
    if($sb_instagram_cache_time_unit == 'minutes') $sb_instagram_cache_time_unit = 60;
    if($sb_instagram_cache_time_unit == 'hours') $sb_instagram_cache_time_unit = 60*60;
    if($sb_instagram_cache_time_unit == 'days') $sb_instagram_cache_time_unit = 60*60*24;
    $cache_seconds = intval($sb_instagram_cache_time) * intval($sb_instagram_cache_time_unit);

    $transient_name = $_POST['transientName'];
    $photos_data = $_POST['photos'];

    set_transient( $transient_name, $photos_data, $cache_seconds );
}
add_action('wp_ajax_cache_photos', 'sbi_cache_photos');
add_action('wp_ajax_nopriv_cache_photos', 'sbi_cache_photos');



function sbi_get_cache() {
    global $wpdb;

    $cached_data = get_transient( $_POST['transientName'] );

    print $cached_data;
    die();
}
add_action('wp_ajax_get_cache', 'sbi_get_cache');
add_action('wp_ajax_nopriv_get_cache', 'sbi_get_cache');


//Enqueue stylesheet
add_action( 'wp_enqueue_scripts', 'sb_instagram_styles_enqueue' );
function sb_instagram_styles_enqueue() {
    wp_register_style( 'sb_instagram_styles', plugins_url('css/sb-instagram.css', __FILE__), array(), SBIVER );
    wp_enqueue_style( 'sb_instagram_styles' );
    wp_enqueue_style( 'sbi-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css', array(), '4.6.3' );
}

//Enqueue scripts
add_action( 'wp_enqueue_scripts', 'sb_instagram_scripts_enqueue' );
function sb_instagram_scripts_enqueue() {
    //Register the script to make it available
    wp_register_script( 'sb_instagram_scripts', plugins_url( '/js/sb-instagram.js' , __FILE__ ), array('jquery'), SBIVER, true );

    //Options to pass to JS file
    $sb_instagram_settings = get_option('sb_instagram_settings');

    //Hide photos
    isset($sb_instagram_settings[ 'sb_instagram_hide_photos' ]) ? $sb_instagram_hide_photos = trim($sb_instagram_settings['sb_instagram_hide_photos']) : $sb_instagram_hide_photos = '';

    //Block users
    isset($sb_instagram_settings[ 'sb_instagram_block_users' ]) ? $sb_instagram_block_users = trim($sb_instagram_settings['sb_instagram_block_users']) : $sb_instagram_block_users = '';

    //Access token
    isset($sb_instagram_settings[ 'sb_instagram_at' ]) ? $sb_instagram_at = trim($sb_instagram_settings['sb_instagram_at']) : $sb_instagram_at = '';

    $data = array(
        'sb_instagram_at' => $sb_instagram_at,
        'sb_instagram_hide_photos' => $sb_instagram_hide_photos,
        'sb_instagram_block_users' => $sb_instagram_block_users
    );

    isset($sb_instagram_settings[ 'sb_instagram_ajax_theme' ]) ? $sb_instagram_ajax_theme = trim($sb_instagram_settings['sb_instagram_ajax_theme']) : $sb_instagram_ajax_theme = '';
    ( $sb_instagram_ajax_theme == 'on' || $sb_instagram_ajax_theme == 'true' || $sb_instagram_ajax_theme == true ) ? $sb_instagram_ajax_theme = true : $sb_instagram_ajax_theme = false;

    //Enqueue it to load it onto the page
    if( !$sb_instagram_ajax_theme ) wp_enqueue_script('sb_instagram_scripts');

    //Pass option to JS file
    wp_localize_script('sb_instagram_scripts', 'sb_instagram_js_options', $data);
}

//Custom CSS
add_action( 'wp_head', 'sb_instagram_custom_css' );
function sb_instagram_custom_css() {
    $options = get_option('sb_instagram_settings');

    isset($options[ 'sb_instagram_custom_css' ]) ? $sb_instagram_custom_css = trim($options['sb_instagram_custom_css']) : $sb_instagram_custom_css = '';

    //Show CSS if an admin (so can see Hide Photos link), if including Custom CSS or if hiding some photos
    ( current_user_can( 'edit_posts' ) || !empty($sb_instagram_custom_css) || !empty($sb_instagram_hide_photos) ) ? $sbi_show_css = true : $sbi_show_css = false;

    if( $sbi_show_css ) echo '<!-- Instagram Feed CSS -->';
    if( $sbi_show_css ) echo "\r\n";
    if( $sbi_show_css ) echo '<style type="text/css">';

    if( !empty($sb_instagram_custom_css) ){
        echo "\r\n";
        echo stripslashes($sb_instagram_custom_css);
    }

    if( current_user_can( 'edit_posts' ) ){
        echo "\r\n";
        echo "#sbi_mod_link, #sbi_mod_error{ display: block; }";
    }

    if( $sbi_show_css ) echo "\r\n";
    if( $sbi_show_css ) echo '</style>';
    if( $sbi_show_css ) echo "\r\n";
}

//Custom JS
add_action( 'wp_footer', 'sb_instagram_custom_js' );
function sb_instagram_custom_js() {
    $options = get_option('sb_instagram_settings');
    isset($options[ 'sb_instagram_custom_js' ]) ? $sb_instagram_custom_js = trim($options['sb_instagram_custom_js']) : $sb_instagram_custom_js = '';

    echo '<!-- Instagram Feed JS -->';
    echo "\r\n";
    echo '<script type="text/javascript">';
    echo "\r\n";
    echo 'var sbiajaxurl = "' . admin_url('admin-ajax.php') . '";';

    if( !empty($sb_instagram_custom_js) ) echo "\r\n";
    if( !empty($sb_instagram_custom_js) ) echo "jQuery( document ).ready(function($) {";
    if( !empty($sb_instagram_custom_js) ) echo "\r\n";
    if( !empty($sb_instagram_custom_js) ) echo "window.sbi_custom_js = function(){";
    if( !empty($sb_instagram_custom_js) ) echo "\r\n";
    if( !empty($sb_instagram_custom_js) ) echo stripslashes($sb_instagram_custom_js);
    if( !empty($sb_instagram_custom_js) ) echo "\r\n";
    if( !empty($sb_instagram_custom_js) ) echo "}";
    if( !empty($sb_instagram_custom_js) ) echo "\r\n";
    if( !empty($sb_instagram_custom_js) ) echo "});";

    echo "\r\n";
    echo '</script>';
    echo "\r\n";

}

?>