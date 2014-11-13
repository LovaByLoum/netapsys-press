<?php
/*
Plugin Name: WP Ajax Upload
Plugin URI:
Description: Front-end, Backend ajax image upload. Add sohrtcode [AAIU] any where in post,page or in your custom form. For theme insert the code ' echo do_shortcode('[AAIU theme="true"]'); ' in your theme.
Version:  1.1
Author: Ranarimanana Johary
License: GPL2
*/

define('WAU_BASENAME', trailingslashit(basename(dirname(__FILE__))));
define('WAU_DIR', WP_CONTENT_DIR . '/plugins/' . WAU_BASENAME);
define('WAU_URL', WP_CONTENT_URL . '/plugins/' . WAU_BASENAME);

class WP_Ajax_Image_Upload
{
    public $option = 'wpau-options';
    public $options = null;

    public function register()
    {
        register_setting('wpau_plugin_option', $this->option, array($this, 'validate_options'));
    }

    public function initialize_default_options()
    {
        $default_options = array(
            "max_upload_size" => "100 ",
            "max_upload_no" => "2",
            "allow_ext" => "jpg,gif,png"
        );
        update_option($this->option, $default_options);

    }

    public function display($atts = null)
    {
        $atts = $atts;
        include (WAU_DIR . '/html.php');
    }

    public function validate_options($input)
    {
        return $input;
    }

    public function enquee()
    {
        $this->options = get_option('wpau-options');

        wp_enqueue_script('jquery');
        wp_enqueue_script('plupload-handlers');

        $max_file_size = intval($this->options['max_upload_size']) * 1000 * 1000;
        $max_upload_no = intval($this->options['max_upload_no']);
        $allow_ext = $this->options['allow_ext'];

        wp_enqueue_script('wpau_upload', WAU_URL . 'js/wpau_upload.js', array('jquery'));
        wp_enqueue_script('livequery', WAU_URL . 'js/jquery.livequery.js', array('jquery'));
        wp_enqueue_style('wpau_upload', WAU_URL . 'css/wpau_upload.css');

        wp_localize_script('wpau_upload', 'wpau_upload', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wpau_upload'),
            'remove' => wp_create_nonce('wpau_remove'),
            'number' => $max_upload_no,
            'upload_enabled' => true,
            'confirmMsg' => __('Are you sure you want to delete this?'),
            'plupload' => array(
                'runtimes' => 'html5,flash,html4',
                'browse_button' => 'wpau-uploader',
                'container' => 'wpau-upload-container',
                'file_data_name' => 'wpau_upload_file',
                'max_file_size' => $max_file_size . 'b',
                'url' => admin_url('admin-ajax.php') . '?action=wpau_upload&nonce=' . wp_create_nonce('wpau_allow'),
                'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
                'filters' => array(array('title' => __('Allowed Files'), 'extensions' => $allow_ext)),
                'multipart' => true,
                'urlstream_upload' => true,
            )
        ));

    }

    public function upload()
    {
        check_ajax_referer('wpau_allow', 'nonce');

        $file = array(
            'name' => $_FILES['wpau_upload_file']['name'],
            'type' => $_FILES['wpau_upload_file']['type'],
            'tmp_name' => $_FILES['wpau_upload_file']['tmp_name'],
            'error' => $_FILES['wpau_upload_file']['error'],
            'size' => $_FILES['wpau_upload_file']['size']
        );
        $file = $this->fileupload_process($file);


    }

    public function fileupload_process($file)
    {
        $attachment = $this->handle_file($file);

        if (is_array($attachment)) {
            $attach_id = $attachment['id'];
            $file = explode('/', $attachment['data']['file']);
            $file = array_slice($file, 0, count($file) - 1);
            $path = implode('/', $file);
            $image = $attachment['data']['sizes']['thumbnail']['file'];
            $post = get_post($attach_id);
            $dir = wp_upload_dir();
            $path = $dir['baseurl'] . '/' . $path;
        
            $html = $this->getHTML($attachment);
            
            
            //modif netapsys 2014 02 07, ne pas utiliser du html json encode
            $response = array(
                'success' => true,
                'attach_id' => $attach_id,
                'file' => $file,
                'path' => $path,
                'image'=>$image,
                'postname' => $post->post_title
                //'html' => $html,
            );

            echo json_encode($response);
            exit;
        }

        $response = array('success' => false);
        echo json_encode($response);
        exit;
    }

    function handle_file($upload_data)
    {

        $return = false;
        $uploaded_file = wp_handle_upload($upload_data, array('test_form' => false));

        if (isset($uploaded_file['file'])) {
            $file_loc = $uploaded_file['file'];
            $file_name = basename($upload_data['name']);
            $file_type = wp_check_filetype($file_name);

            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attach_id = wp_insert_attachment($attachment, $file_loc);
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_loc);
            wp_update_attachment_metadata($attach_id, $attach_data);

            $return = array('data' => $attach_data, 'id' => $attach_id);

            return $return;
        }

        return $return;
    }

    function getHTML($attachment)
    {

        $attach_id = $attachment['id'];
        $file = explode('/', $attachment['data']['file']);
        $file = array_slice($file, 0, count($file) - 1);
        $path = implode('/', $file);
        $image = $attachment['data']['sizes']['thumbnail']['file'];
        $post = get_post($attach_id);
        $dir = wp_upload_dir();
        $path = $dir['baseurl'] . '/' . $path;

        $html = '';
        $html .= '<li class="wpau-uploaded-files">';
        $html .= sprintf('<img src="%s" name="' . $post->post_title . '" />', $path . '/' . $image);
        $html .= sprintf('<br /><a href="#" class="action-delete" data-upload_id="%d">%s</a></span>', $attach_id, __('Delete'));
        $html .= sprintf('<input type="hidden" name="wpau_image_id[]" value="%d" />', $attach_id);
        $html .= sprintf('<br /><a href="javascript:;" class="action-validate" data-upload_id="%d">%s</a></span>', $attach_id, __('Ok'));
        $html .= '</li>';

        return $html;
    }


    function has_shortcode($shortcode = '', $post_id = false)
    {
        global $post;

        if (!$post) {
            return false;
        }

        $post_to_check = ($post_id == false) ? get_post(get_the_ID()) : get_post($post_id);

        if (!$post_to_check) {
            return false;
        }
        $return = false;

        if (!$shortcode) {
            return $return;
        }

        if (stripos($post_to_check->post_content, '[' . $shortcode) !== false) {
            $return = true;
        }

        return $return;
    }

    public function delete_file()
    {
        $attach_id = $_POST['attach_id'];
        wp_delete_attachment($attach_id, true);
        exit;
    }

}

function register_wp_menu_page()
{
    $menuSlug = 'wp_ajax_upload.php';
    add_options_page('Waau', 'WP Ajax Upload', 'manage_options', $menuSlug, 'wpau_settings');

}

function wpau_settings()
{
    ?>
<div class="wrap">
    <h2>WP Ajax Upload Settings</h2>

    <form method="post" name="wpau-form" action="<?php echo 'options.php'; ?>">
        <?php settings_fields('wpau_plugin_option'); ?>
        <?php $options = get_option('wpau-options');?>
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row"><label for="max_upload_size">Max Upload Size</label></th>
                <td><input type="text" value="<?php echo $options['max_upload_size'];?>"
                           name="wpau-options[max_upload_size]" size="10">

                    <p class="description">Size in MB.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="max_upload_no">Max Number of Image</label></th>
                <td><input type="text" value="<?php echo $options['max_upload_no'];?>"
                           name="wpau-options[max_upload_no]" size="10">

                    <p class="description">Maximun number of Images user can upload.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="allow_ext">Allowed Extension</label></th>
                <td><input type="text" value="<?php echo $options['allow_ext'];?>"
                           name="wpau-options[allow_ext]" size="20">

                    <p class="description">Eg: jpge,gif,png</p>
                </td>
            </tr>

            <tr valign="top">
                <td colspan="2"><?php submit_button(); ?></td>
            </tr>

            </tbody>
        </table>
    </form>
</div>
<?php
}

$wpaufile = WP_CONTENT_DIR . '/plugins/' . basename(dirname(__FILE__)) . '/' . basename(__FILE__);
global $wpau;
$wpau = new WP_Ajax_Image_Upload();
add_action('admin_init', array($wpau, 'register'));
add_action('admin_menu', 'register_wp_menu_page');
register_activation_hook($wpaufile, array($wpau, 'initialize_default_options'));
add_action('admin_init', array($wpau, 'enquee'));
add_action('init', array($wpau, 'enquee'));
add_shortcode('AAIU', array($wpau, 'display'));
add_action('wp_ajax_wpau_upload', array($wpau, 'upload'));
add_action('wp_ajax_wpau_delete', array($wpau, 'delete_file'));

/* For non logged-in user */
add_action('wp_ajax_nopriv_wpau_upload', array($wpau, 'upload'));
add_action('wp_ajax_nopriv_wpau_delete', array($wpau, 'delete_file'));

