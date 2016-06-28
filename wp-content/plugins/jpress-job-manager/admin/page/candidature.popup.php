<?php
require_once( realpath( dirname(__FILE__) . '/../../../../../') . DIRECTORY_SEPARATOR . 'wp-load.php' );
global $is_popup_candidature;
$is_popup_candidature = true;

$post_id = $_REQUEST['cid'];
$post = get_post( $post_id );
?>
<html>
    <head>
        <link type="text/css" rel='stylesheet' href="<?php echo admin_url(); ?>/load-styles.php?c=1&dir=ltr&load=wp-admin,buttons"/>
        <style>
            body{
                padding : 20px;
            }
            .form-table td,
            .form-table th {
                margin: 0;
                padding: 5px 0;
            }
        </style>
    </head>
    <body>
        <div id = "wpbody">
             <div id="wpbody-content">
                 <div class="wrap">
                     <h2>Informations candidature : <?php echo $post->post_title;?></h2>
                     <?php
                     jpress_jm_candidature_fields( $post );
                     jpress_jm_candidature_relations ( $post );
                     ?>
                 </div>
             </div>
        </div>
    </body>
</html>


