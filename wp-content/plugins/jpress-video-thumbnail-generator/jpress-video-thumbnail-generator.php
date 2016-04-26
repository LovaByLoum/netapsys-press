<?php
/*
Plugin Name: jpress-video-thumbnail-generator
Description: Génère des vignettes pour les media de type  video. Utilise la librairie ffmpeg
Author: Johary
*/
require_once("ffmpeg_convert.php");

//outils
add_action( 'admin_menu',  'jvtg_add_admin_menu' );
function jvtg_add_admin_menu() {
    add_management_page( 'Video thumbnail generator', 'Video thumbnail generator', 'delete_pages', 'video-thumbnail-generator', 'video_thumbnail_generator');

    add_submenu_page('options-general.php', 'Video thumbnail generator options', 'Video thumbnail generator options', 'activate_plugins', 'jvtg-options-page', 'jvtg_options_page');
}

function jvtg_get_options(){
    $options = get_option('jvtg_options');
    if ($options){
        $options = is_array($options)?$options:unserialize($options);
    }else{
        $options = array();
    }

    if (!isset($options['root']))
        $options['root'] = "/usr/bin/";
    if (!isset($options['seconde']))
        $options['seconde'] = "10";
    if (!isset($options['width']))
        $options['width'] = "300";
    if (!isset($options['height']))
        $options['height'] = "300";

    return $options;
}

function jvtg_options_page(){
    $options = jvtg_get_options();
    if (isset($_POST["jvtg_option"])){

        $new_option = array();
        $new_option['root'] = $_POST["jvtg_root"];
        $new_option['seconde'] = $_POST["jvtg_seconde"];
        $new_option['width'] = $_POST["jvtg_width"];
        $new_option['height'] = $_POST["jvtg_height"];

        if ($options && !empty($options)){
            update_option('jvtg_options', serialize($new_option));
        }else{
            add_option('jvtg_options', serialize($new_option));
        }
        $options = $new_option;
    }
    ?>
    <div id="wpbody">
        <div id="wpbody-content">
            <div class="icon32" id="icon-options-general"><br></div>
            <h2 class="nav-tab-wrapper">
                <a href="#list-env-tab" title="Video thumbnail generator options" class="nav-tab nav-tab-active" id="list-env">Video thumbnail generator options</a>
            </h2>

            <h3>ffmpeg options</h3>
            <form name="form1" method="post">
                <input type="hidden" name="jvtg_option" value="1"/>
                <table class="form-table">
                    <tbody>

                    <tr valign="top">
                        <th scope="row"><label for="jvtg_root">ffmpeg root</label></th>
                        <td><input name="jvtg_root" type="text" id="jvtg_root" value="<?php if (isset($options['root'])):echo $options['root'];else:echo '/usr/bin/';endif;?>" class="medium-text"></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="jvtg_seconde">La position du capture en seconde</label></th>
                        <td><input name="jvtg_seconde" type="text" id="jvtg_seconde" value="<?php if (isset($options['seconde'])):echo $options['seconde'];else:echo '10';endif;?>" class="medium-text"></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="jvtg_width">Largeur</label></th>
                        <td><input name="jvtg_width" type="text" id="jvtg_width" value="<?php if (isset($options['width'])):echo $options['width'];else:echo '300';endif;?>" class="medium-text"></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="jvtg_height">La position du capture en seconde</label></th>
                        <td><input name="jvtg_height" type="text" id="jvtg_height" value="<?php if (isset($options['height'])):echo $options['height'];else:echo '300';endif;?>" class="medium-text"></td>
                    </tr>

                    </tbody></table>


                <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Enregistrer les modifications"></p></form>

        </div><!-- wpbody-content -->
        <div class="clear"></div>
    </div>
<?php
}

add_action( 'admin_enqueue_scripts', 'jvtg_admin_enqueues'  );
function jvtg_admin_enqueues( $hook_suffix ) {
    if ( $hook_suffix != 'tools_page_video-thumbnail-generator' )
        return;

    // WordPress 3.1 vs older version compatibility
    if ( wp_script_is( 'jquery-ui-widget', 'registered' ) )
        wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'jquery-ui/jquery.ui.progressbar.min.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.8.6' );
    else
        wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'jquery-ui/jquery.ui.progressbar.min.1.7.2.js', __FILE__ ), array( 'jquery-ui-core' ), '1.7.2' );

    wp_enqueue_style( 'jquery-ui-convert-video-image', plugins_url( 'jquery-ui/redmond/jquery-ui-1.7.2.custom.css', __FILE__ ), array(), '1.7.2' );
}

add_filter( 'media_row_actions', 'jvtg_add_media_row_action' , 10, 2 );
function jvtg_add_media_row_action( $actions, $post ) {
    if ( strpos($post->post_mime_type, 'video/') ===false  || ! current_user_can( 'delete_pages' ) )
        return $actions;

    $url = wp_nonce_url( admin_url( 'tools.php?page=video-thumbnail-generator&ids=' . $post->ID ), 'video-thumbnail-generator' );
    $actions['video-thumbnail-generator'] = '<a href="' . esc_url( $url ) . '" title="' . esc_attr( "Générer une vignette" ) . '">' . "Générer une vignette" . '</a>';

    return $actions;
}

// Add "Convert pdf" to the Bulk Actions media dropdown
add_action( 'admin_head-upload.php', 'jvtg_add_bulk_actions_via_javascript'  );
function jvtg_add_bulk_actions_via_javascript() {
    if ( ! current_user_can( 'delete_pages' ) )
        return;
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('select[name^="action"] option:last-child').before('<option value="bulk_generate_video_thumbnail"><?php echo esc_attr( "Générer les vignettes" ); ?></option>');
        });
    </script>
<?php
}

add_action( 'admin_action_bulk_generate_video_thumbnail', 'jvtg_bulk_action_handler'  ); // Top drowndown
add_action( 'admin_action_-1', 'jvtg_bulk_action_handler'  );
// Handles the bulk actions POST
function jvtg_bulk_action_handler() {
    if ( empty( $_REQUEST['action'] ) || ( 'bulk_generate_video_thumbnail' != $_REQUEST['action'] && 'bulk_generate_video_thumbnail' != $_REQUEST['action2'] ) )
        return;

    if ( empty( $_REQUEST['media'] ) || ! is_array( $_REQUEST['media'] ) )
        return;

    check_admin_referer( 'bulk-media' );

    $ids = implode( ',', array_map( 'intval', $_REQUEST['media'] ) );

    // Can't use wp_nonce_url() as it escapes HTML entities
    wp_redirect( add_query_arg( '_wpnonce', wp_create_nonce( 'video-thumbnail-generator' ), admin_url( 'tools.php?page=video-thumbnail-generator&ids=' . $ids ) ) );
    exit();
}


add_action( 'wp_ajax_generate_thumbnail_video',  'jvtg_generate_thumbnail_video' );
function jvtg_generate_thumbnail_video(){
    @error_reporting( 0 ); // Don't break the JSON result

    header( 'Content-type: application/json' );

    $id = (int) $_REQUEST['id'];
    $sec = (int) $_REQUEST['sec'];
    $video = get_post( $id );

    if ( ! $video || 'attachment' != $video->post_type || strpos($video->post_mime_type, 'video/') ===false )
        die( json_encode( array( 'error' => sprintf( 'Echec conversion: %s est un identifiant video invalide.',  $_REQUEST['id']  ) ) ) );

    if ( ! current_user_can( 'delete_pages' ) )
        jvtg_die_json_error_msg( $video->ID, "Vous n'avez pas la permission de générer des vignettes à partir d'un video" );

    $fullsizepath = get_attached_file( $video->ID );

    if ( false === $fullsizepath || ! file_exists( $fullsizepath ) )
        jvtg_die_json_error_msg( $video->ID, sprintf('Le video spécifié par le chemin %s est introuvable', '<code>' .  $fullsizepath  . '</code>' ) );

    @set_time_limit( 900 ); // 5 minutes per image should be PLENTY
    //image magik operations
    $videourl = $video->guid;
    $videomagik = new FFMPEG_CONVERT($videourl);
    $videomagik->removeVignette();
    $videomagik->convertVideo($sec);

    die( json_encode( array( 'success' => sprintf( '&quot;%1$s&quot; (ID %2$s) convertit avec succès en %3$s secondes.',  get_the_title( $video->ID ) , $video->ID, timer_stop() ) ) ) );
}

add_filter('wp_get_attachment_image_attributes', 'jvtg_wp_get_attachment_image_attributes',10,2);
function jvtg_wp_get_attachment_image_attributes($attr, $attachment){
    if( strpos($attachment->post_mime_type,'video/')!==false){
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'numberposts' => 1,
            'post_parent' => $attachment->ID,
            'fields' => 'ids'
        );
       $posts = get_posts($args);
        if(isset($posts[0]) && $posts[0]>0 ){
            list($src, $width, $height) = wp_get_attachment_image_src(intval($posts[0]), 'thumbnail', false);
            $attr['src'] = $src;
        }
        return $attr;
    }else{
        return $attr;
    }
}

/**
 * function pour retourner la vignette video
 * @param $video_id
 */
function jvtg_get_vignette($video_id, $size = 'thumbnail'){
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'numberposts' => 1,
        'post_parent' => $video_id,
        'fields' => 'ids'
    );
    $posts = get_posts($args);
    if(isset($posts[0]) && $posts[0]>0 ){
        list($src, $width, $height) = wp_get_attachment_image_src(intval($posts[0]), $size, false);
        return  $src;
    }
    return false;
}

// Helper to make a JSON error message
function jvtg_die_json_error_msg( $id, $message ) {
    die( json_encode( array( 'error' => sprintf( '&quot;%1$s&quot; (ID %2$s) echec lors de la conversion. Le message d\'erreur est: %3$s',  get_the_title( $id ) , $id, $message ) ) ) );
}

function video_thumbnail_generator(){
    global $wpdb;

    ?>

    <div id="message" class="updated fade" style="display:none"></div>

    <div class="wrap ConvertVideoImages">
        <h2>Générer les vignettes video</h2>
        <?php

        // If the button was clicked
        if ( ! empty( $_POST['convert-video-to-image'] ) || ! empty( $_REQUEST['ids'] ) ) {
            // Capability check
            if ( ! current_user_can( 'delete_pages' ) )
                wp_die( __( 'Cheatin&#8217; uh?' ) );

            // Form nonce check
            check_admin_referer( 'video-thumbnail-generator' );

            // Create the list of image IDs
            if ( ! empty( $_REQUEST['ids'] ) ) {
                $videos = array_map( 'intval', explode( ',', trim( $_REQUEST['ids'], ',' ) ) );
                $ids = implode( ',', $videos );
            } else {
                // Directly querying the database is normally frowned upon, but all
                // of the API functions will return the full post objects which will
                // suck up lots of memory. This is best, just not as future proof.
                if ( ! $videos = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'video/%' ORDER BY ID DESC" ) ) {
                    echo '	<p>Aucun video trouvé.</p></div>';
                    return;
                }

                // Generate the list of IDs
                $ids = array();
                foreach ( $videos as $video )
                    $ids[] = $video->ID;
                $ids = implode( ',', $ids );
            }

            echo "	<p>Veuillez patienter pendant que les vignettes video sont générés. Cela peut prendre un certain temps si votre serveur est lent ou si vous avez beaucoup de video à convertir. Ne quittez pas cette page jusqu'à ce que ce script soit fini où les images ne seront pas générées. Vous serez averti par cette page lorsque l'operation est terminée.</p>";

            $count = count( $videos );

            $text_failures = sprintf( 'Tout est fait! %1$s vignette(s) ont été généré avec succès en %2$s secondes et il y a %3$s echec(s).', "' + cpi_successes + '", "' + cpi_totaltime + '", "' + cpi_errors + '");
            $text_nofailures = sprintf('Tout est fait! %1$s vignette(s) ont été généré avec succès en %2$s secondes et il y a 0 echec.', "' + cpi_successes + '", "' + cpi_totaltime + '");
            ?>


            <noscript><p><em><?php echo 'Vous devez activer Javascript avant de procéder!';?></em></p></noscript>

            <div id="convert-video-image-bar" style="position:relative;height:25px;">
                <div id="convert-video-image-bar-percent" style="position:absolute;left:50%;top:50%;width:300px;margin-left:-150px;height:25px;margin-top:-9px;font-weight:bold;text-align:center;"></div>
            </div>
			<div id="video-preview">
				<table class="compat-attachment-fields">
					<tbody>
						<tr class="compat-field-thumbtime">
							<th valign="top" class="label" scope="row">
								<label for="video-vignette-time">
									<span class="alignleft">Temps vignette</span><br class="clear">
								</label>
							</th>
							<td class="field">
								<input type="text" value="" name="video-vignette-time" id="video-vignette-time" class="text">
								<p class="help"><small>Optionnel: générer une vignette au temps spécifié (en seconde).</small></p>
							</td>
						</tr>
					</tbody>
				</table>
				<div id="video-preview-container">
					
				</div>
				
			</div>
            <p>
            	<input type="button" class="button-primary hide-if-no-js" name="convert-video-image-start" id="convert-video-image-start" value="<?php echo 'Demarrer la conversion'; ?>" />
            	<input type="button" class="button hide-if-no-js" name="convert-video-image-stop" id="convert-video-image-stop" value="<?php echo 'Annuler la conversion'; ?>" />
            </p>

            <h3 class="title"><?php echo 'Informations'; ?></h3>

            <p>
                <?php printf( 'Total videos: %s', $count ); ?><br />
                <?php printf( 'convertis avec succès : %s', '<span id="convert-video-image-debug-successcount">0</span>' ); ?><br />
                <?php printf( 'conversions echouées : %s', '<span id="convert-video-image-debug-failurecount">0</span>' ); ?>
            </p>

            <ol id="convert-video-image-debuglist">
                <li style="display:none"></li>
            </ol>

            <script type="text/javascript">
                // <![CDATA[
                jQuery(document).ready(function($){
                    var i;
                    var cpi_videos = [<?php echo $ids; ?>];
                    var cpi_total = cpi_videos.length;
                    var cpi_count = 1;
                    var cpi_percent = 0;
                    var cpi_successes = 0;
                    var cpi_errors = 0;
                    var cpi_failedlist = '';
                    var cpi_resulttext = '';
                    var cpi_timestart = new Date().getTime();
                    var cpi_timeend = 0;
                    var cpi_totaltime = 0;
                    var cpi_continue = true;

                    // Create the progress bar
                    $("#convert-video-image-bar").progressbar();
                    $("#convert-video-image-bar-percent").html( "0%" );

                    // Stop button
                    $("#convert-video-image-stop").click(function() {
                        cpi_continue = false;
                        $('#convert-video-image-stop').val("<?php echo 'Annulation...'; ?>");
                    });

                    // Clear out the empty list element that's there for HTML validation purposes
                    $("#convert-video-image-debuglist li").remove();

                    // Called after each resize. Updates debug information and the progress bar.
                    function ConvertVideoImageUpdateStatus( id, success, response ) {
                        $("#convert-video-image-bar").progressbar( "value", ( cpi_count / cpi_total ) * 100 );
                        $("#convert-video-image-bar-percent").html( Math.round( ( cpi_count / cpi_total ) * 1000 ) / 10 + "%" );
                        cpi_count = cpi_count + 1;

                        if ( success ) {
                            cpi_successes = cpi_successes + 1;
                            $("#convert-video-image-debug-successcount").html(cpi_successes);
                            $("#convert-video-image-debuglist").append("<li>" + response.success + "</li>");
                        }
                        else {
                            cpi_errors = cpi_errors + 1;
                            cpi_failedlist = cpi_failedlist + ',' + id;
                            $("#convert-video-image-debug-failurecount").html(cpi_errors);
                            $("#convert-video-image-debuglist").append("<li>" + response.error + "</li>");
                        }
                    }

                    // Called when all images have been processed. Shows the results and cleans up.
                    function ConvertVideoImageFinishUp() {
                        cpi_timeend = new Date().getTime();
                        cpi_totaltime = Math.round( ( cpi_timeend - cpi_timestart ) / 1000 );

                        $('#convert-video-image-stop').hide();
                        $('#convert-video-image-start').hide();

                        if ( cpi_errors > 0 ) {
                            cpi_resulttext = '<?php echo $text_failures; ?>';
                        } else {
                            cpi_resulttext = '<?php echo $text_nofailures; ?>';
                        }

                        $("#message").html("<p><strong>" + cpi_resulttext + "</strong></p>");
                        $("#message").show();
                    }

                    // Regenerate a specified image via AJAX
                    function ConvertVideoImages( id , sec) {
                        $.ajax({
                            type: 'POST',
                            url: ajaxurl,
                            data: { action: "generate_thumbnail_video", id: id, sec : sec },
                            success: function( response ) {
                                if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {
                                    response = new Object;
                                    response.success = false;
                                    response.error = "<?php printf( esc_js( __( 'The conversion request was abnormally terminated (ID %s). This is likely due to the image exceeding available memory or some other type of fatal error.', 'jvtg' ) ), '" + id + "' ); ?>";
                                }

                                if ( response.success ) {
                                    ConvertVideoImageUpdateStatus( id, true, response );
                                }
                                else {
                                    ConvertVideoImageUpdateStatus( id, false, response );
                                }

                                if ( cpi_videos.length && cpi_continue ) {
                                    ConvertVideoImages( cpi_videos.shift(),sec);
                                }
                                else {
                                    ConvertVideoImageFinishUp();
                                }
                            },
                            error: function( response ) {
                                ConvertVideoImageUpdateStatus( id, false, response );

                                if ( cpi_videos.length && cpi_continue ) {
                                    ConvertVideoImages( cpi_videos.shift() ,sec);
                                }
                                else {
                                    ConvertVideoImageFinishUp();
                                }
                            }
                        });
                    }
					
                    jQuery("#convert-video-image-start").click(function(){
                    	var sec = -1;
                    	if(jQuery("#video-vignette-time").length>0){
                    		sec = jQuery("#video-vignette-time").val();
                    		if(parseInt(sec)<=0) sec = 10;
                    	}
                    	ConvertVideoImages( cpi_videos.shift() ,sec);
                    });
                });
                // ]]>
            </script>
        <?php
        }

        // No button click? Display the form.
        else {
            ?>
            <form method="post" action="">
                <?php wp_nonce_field('video-thumbnail-generator') ?>

                <p><?php echo  "Utilisez cet outil pour générer des vignettes à partir de fichier video."; ?></p>

                <p><?php echo "Pour commencer, il suffit d'appuyer sur le bouton ci-dessous."; ?></p>

                <p><input type="submit" class="button hide-if-no-js" name="convert-video-to-image" id="convert-video-to-image" value="<?php echo 'Générer les vignettes video';?>" /></p>

                <noscript><p><em><?php echo 'Vous devez activer Javascript avant de procéder!'; ?></em></p></noscript>

            </form>
        <?php
        } // End if button
        ?>
    </div>

<?php
}
?>
