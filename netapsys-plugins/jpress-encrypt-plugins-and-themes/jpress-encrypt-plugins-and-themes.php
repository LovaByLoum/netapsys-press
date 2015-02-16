<?php
/**
Plugin Name: jPress Encrypt Plugins and Themes
Description: Encrypte vos plugins pour protéger le code source PHP de l'observation facile, le vol et le modification
Version: 1.0.0
Author: Johary Ranarimanana
*/

add_action('admin_menu', 'jpressep_admin_menu');
function jpressep_admin_menu(){
    add_options_page('Encrypt files', 'Encrypt files','manage_options','encrypt-files','jpressep_admin_page');

}

function jpressep_admin_page(){
    ?>
    <div class="wrap">
        <h2>Encrypt files</h2>
        <?php $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'plugins';?>
        <h2 class="nav-tab-wrapper">
            <a href="options-general.php?page=encrypt-files&tab=plugins" class="nav-tab <?php if($current_tab == 'plugins'):?>nav-tab-active<?php endif;?>" id="plugins-tab">Plugins</a>
            <a href="options-general.php?page=encrypt-files&tab=themes" class="nav-tab <?php if($current_tab == 'themes'):?>nav-tab-active<?php endif;?>" id="themes-tab">Themes</a>
        </h2>
        <div id="tab-content">
            <?php
            switch($current_tab){
                case 'plugins':
                    include 'tabs/tab-plugins.php';
                    break;
                case 'themes':
                    include 'tabs/tab-themes.php';
                    break;
                default:
                    include 'tabs/tab-plugins.php';
            }
            ?>
        </div>
    </div>
    <?php

}

/*fonction d'encrypt plugins */
function jpressep_encrypt_plugins(){
    if(!isset($_POST['encrypt-plugins'])) return;

    $pluginstoencrypt = $_POST['plugs'];
    if(empty($pluginstoencrypt)) return;
    $messages = '';
    foreach ($pluginstoencrypt as $plug) {
        if(strpos($plug,'/')===false) continue;

        $messages .= jpressep_encrypt_plugin($plug);
    }
    return $messages;
}
/*encrypt plugin one to one*/
function jpressep_encrypt_plugin($path){

    //get plugin info
    $mainfilepath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
    $pluginheader = get_plugin_header($mainfilepath);

    $extradata = array($mainfilepath=>$pluginheader);

    $message = '<strong>- ' . $path .' : </strong><br>';
    $fullpath = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path;
    $pathinfo = pathinfo($fullpath);
    $fullpath = $pathinfo['dirname'];

    if(!file_exists($fullpath)){
        $message.='&nbsp;&nbsp;&nbsp;Folder not exists<br>';
    }

    $message .= scan_dir($fullpath, '',$extradata);
    return $message;
}

/*fonction d'encrypt themes */
function jpressep_encrypt_themes(){
    if(!isset($_POST['encrypt-themes'])) return;

    $themestoencrypt = $_POST['themes'];
    if(empty($themestoencrypt)) return;

    $messages = '';
    foreach ($themestoencrypt as $theme) {
        $messages .= jpressep_encrypt_theme($theme);
    }
    return $messages;
}
/*encrypt theme one to one*/
function jpressep_encrypt_theme($path){
    $message = '<strong>- ' . $path .' : </strong><br>';

    $fullpath = get_theme_root() . DIRECTORY_SEPARATOR . $path;

    if(!file_exists($fullpath)){
        $message.='&nbsp;&nbsp;&nbsp;Folder not exists<br>';
    }

    $message .= scan_dir($fullpath);
    return $message;
}

/*scan directory*/
function scan_dir($path, $message='',  $extradata=array()){
    $nodes = glob($path . '/*');
    foreach ($nodes as $node) {
        if(is_dir($node)){
            $message = scan_dir($node,$message,$extradata);
        }elseif (is_file($node))  {
            $pathinfo = pathinfo($node) ;
            if($pathinfo['extension'] == 'php'){
                $headers = '';
                if(isset($extradata[$node])){
                    $headers = $extradata[$node];
                }
                $message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. $node. ' : ';
                $message .= encrypt_file($node,$headers);
                $message .='<br>';
            }
        }
    }
    return $message;
}
/*function d'encryptage*/
function encrypt_file($path, $headers=''){
    if(!is_file($path)) return '<span style="color:red;">failed : not an existing file.</span>';

    $filecontent = file_get_contents($path);

    //remove comments
    //$filecontent = remove_comments($filecontent);

    if(strpos($filecontent,'(encrypted by jpress)')!==false) return '<span style="color:red;">failed : already encrypted.</span>';

    //encrypt file content
    $content = encrypt_text($filecontent);

    //execute code
    $varrand =  rand();
    $temprand =  rand();
    $execute_code =
    'chmod(dirname(__FILE__), 0777);
    $temp' .$temprand. ' = tempnam(dirname(__FILE__), "jpress");
    $handle = fopen($temp' .$temprand. ', "w");
    fwrite($handle, $var' . $varrand. ');
    fclose($handle);
    require_once($temp' .$temprand. ');
    unlink($temp' .$temprand. ');
    chmod(dirname(__FILE__), 0755);';
    $execute_code = encrypt_text($execute_code);

    //fonction decode gzuncompress(gzinflate(base64_decode($str)));
    $encrypted_code = "<?php\n{$headers}\n//BEGIN CODE\n\$var{$varrand}=gzuncompress(gzinflate(base64_decode('{$content}')));\neval(gzuncompress(gzinflate(base64_decode('{$execute_code}'))));\n//END CODE (encrypted by jpress)\n?>";
    //echo '<pre>'.$encrypted_code.'</pre>';die;

    if (!$handle = @fopen($path, 'w')) {
        return '<span style="color:red;">failed : can\'t open file.</span>';
    }
    if (@fwrite($handle, $encrypted_code) === FALSE) {
        return '<span style="color:red;">failed : can\'t write file.</span>';
    }
    @fclose($handle);

   return '<span style="color:green;">success</span>';
}

/*fonction d'encryptage*/
function encrypt_text($str){
    //gzcompress()
    $str = gzcompress($str);
    // gzdeflate()
    $str = gzdeflate($str);
    //base64_encode()
    $str = base64_encode($str);
    return $str;
}

/*function de decryptage*/
function decrypt_file($str){
    //base64_encode()
    $str = base64_encode($str);
    // gzdeflate()
    $str = gzdeflate($str);
    //gzcompress()
    $str = gzcompress($str);
    return $str;
}

//remove accent
function remove_comments($fileStr){
    $newStr  = '';
    $commentTokens = array(T_COMMENT);

    if (defined('T_DOC_COMMENT'))
        $commentTokens[] = T_DOC_COMMENT; // PHP 5
    if (defined('T_ML_COMMENT'))
        $commentTokens[] = T_ML_COMMENT;  // PHP 4

    $tokens = token_get_all($fileStr);

    foreach ($tokens as $token) {
        if (is_array($token)) {
            if (in_array($token[0], $commentTokens))
                continue;

            $token = $token[1];
        }

        $newStr .= $token;
    }
    return $newStr;
}

/*plugin header*/
function get_plugin_header($mainfilepath){
    $default_headers = array(
        'Plugin Name' => 'Plugin Name',
        'Plugin URI' => 'Plugin URI',
        'Version' => 'Version',
        'Description' => 'Description',
        'Author' => 'Author',
        'Author URI' => 'Author URI',
        'Text Domain' => 'Text Domain',
        'Domain Path' => 'Domain Path',
        'Network' => 'Network',
    );
    $plugin_data = get_file_data( $mainfilepath, $default_headers, 'plugin' );
    $pluginheader = "/*\n";
    foreach ( $plugin_data as $key => $value) {
        $pluginheader.=$key.": ".$value."\n";
    }
    $pluginheader .= "*/\n";
    return $pluginheader;
}