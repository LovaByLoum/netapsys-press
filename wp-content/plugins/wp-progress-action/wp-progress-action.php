<?php
/*
Plugin Name: WP Progress Action
Description: Classe utilitaire pour créer une interface de traitement de données par ajax avec un progress bar
Author: Johary (Netapsys)
Version: 0.1
*/

//////////////Exemple d'instanciation//////////////////
/*
//hook
$element = 'image';
//ajouter le hook pour specifier les ids à traiter
add_filter('progress_action_data_' . $element, 'my_callback');
function my_callback($data){
  return array(1,2,3,4,5,6);
}
//ajouter le hook pour faire le traitement des elements
add_filter('progress_action_callback_'.$element, 'my_action_callback');
function my_action_callback($id){
    //do some action with $id

    //return a string if error
    return 'error message for ' . $id;

    //return true if success
    return true;
}

//diplay form
$element = 'image';
$progress_action = new WP_Progress_Action($element, 'Image',array('process_title' => 'Conversion des images'));
$progress_action->display();
*/

class WP_Progress_Action{
     public $element_slug;
     public $element_label;
     public $labels;
     public function __construct($element_slug, $element_label = "Elément", $labels =array(
        'process_title' => 'Traitement'

     )){
       $this->element_slug = $element_slug;
       $this->element_label = $element_label;
       $this->labels = $labels;

     }
    public static function  admin_enqueues( ) {

      // WordPress 3.1 vs older version compatibility
      if ( wp_script_is( 'jquery-ui-widget', 'registered' ) )
        wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'jquery-ui/jquery.ui.progressbar.min.js', __FILE__ ), array( 'jquery-ui-core', 'jquery-ui-widget' ), '1.8.6' );
      else
        wp_enqueue_script( 'jquery-ui-progressbar', plugins_url( 'jquery-ui/jquery.ui.progressbar.min.1.7.2.js', __FILE__ ), array( 'jquery-ui-core' ), '1.7.2' );

      wp_enqueue_style( 'jquery-ui-progress-bar-processus', plugins_url( 'jquery-ui/redmond/jquery-ui-1.7.2.custom.css', __FILE__ ), array(), '1.7.2' );
    }

  public static function progress_action_callback(){
    @error_reporting( 0 ); // Don't break the JSON result

    header( 'Content-type: application/json' );

    $id = (int) $_REQUEST['id'];
    $element = $_REQUEST['element'];
    $return = apply_filters('progress_action_callback_' .$element, $id);

    if ($return !== true){
      self::die_json_error_msg($id, $return) ;
    }

    @set_time_limit( 900 ); // 5 minutes per process should be PLENTY

    die( json_encode( array( 'success' => sprintf( 'Succès lors du traitement du ID : %1$s. Temps de traitement en %2$s secondes.',  $id, timer_stop() ) ) ) );
  }

  // Helper to make a JSON error message
  public static function die_json_error_msg( $id, $message ) {
    die( json_encode( array( 'error' => sprintf( 'Echec lors du traitement du ID : %1$s . Le message d\'erreur est: %2$s',  $id, $message ) ) ) );
  }

  public function display(){
    ?>

    <div id="message" class="updated fade" style="display:none"></div>

    <div class="wrap ProgressActionProcessus">
    <h2><?php echo $this->labels['process_title'];?></h2>
    <?php

      $elements = apply_filters('progress_action_data_'.$this->element_slug,array());
      if ( empty($elements) || !$elements  ) {
        echo '	<p>Aucun ' . $this->element_label. ' trouvé.</p></div>';
        return;
      }

      if(is_array($elements)){
        // Generate the list of IDs
        $ids = implode( ',', $elements );
      }


      echo "	<p>Veuillez patienter pendant le traitement des ".$this->element_label ."s. Cela peut prendre un certain temps si votre serveur est lent ou si vous avez beaucoup de ".$this->element_label ."s à traiter. Ne quittez pas cette page jusqu'à ce que ce script soit fini. Vous serez averti par cette page lorsque l'operation est terminée.</p>";

      $count = count( $elements );

      $text_failures = sprintf( 'Tout est fait! %1$s '.$this->element_label .'(s) ont été traité avec succès en %2$s secondes et il y a %3$s echec(s).', "' + cpi_successes + '", "' + cpi_totaltime + '", "' + cpi_errors + '");
      $text_nofailures = sprintf('Tout est fait! %1$s '.$this->element_label .'(s) ont été généré avec succès en %2$s secondes et il y a 0 echec.', "' + cpi_successes + '", "' + cpi_totaltime + '");
      ?>


      <noscript><p><em><?php echo 'Vous devez activer Javascript avant de procéder!';?></em></p></noscript>

      <div id="progress-bar-processus-bar" style="position:relative;height:25px;">
        <div id="progress-bar-processus-bar-percent" style="position:absolute;left:50%;top:50%;width:300px;margin-left:-150px;height:25px;margin-top:-9px;font-weight:bold;text-align:center;"></div>
      </div>
      <p>
        <input type="button" class="button-primary hide-if-no-js" name="progress-bar-processus-start" id="progress-bar-processus-start" value="<?php echo 'Demarrer le traitement'; ?>" />
        <input type="button" class="button hide-if-no-js" name="progress-bar-processus-stop" id="progress-bar-processus-stop" value="<?php echo 'Annuler le traitement'; ?>" />
      </p>

      <h3 class="title"><?php echo 'Informations'; ?></h3>

      <p>
        <?php printf( 'Total '.$this->element_label .'s: %s', $count ); ?><br />
        <?php printf( 'convertis avec succès : %s', '<span id="progress-bar-processus-debug-successcount">0</span>' ); ?><br />
        <?php printf( 'conversions echouées : %s', '<span id="progress-bar-processus-debug-failurecount">0</span>' ); ?>
      </p>

      <ol id="progress-bar-processus-debuglist">
        <li style="display:none"></li>
      </ol>

      <script type="text/javascript">
        // <![CDATA[
        jQuery(document).ready(function($){
          var i;
          var cpi_elements = [<?php echo $ids; ?>];
          var cpi_total = cpi_elements.length;
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
          $("#progress-bar-processus-bar").progressbar();
          $("#progress-bar-processus-bar-percent").html( "0%" );

          // Stop button
          $("#progress-bar-processus-stop").click(function() {
            cpi_continue = false;
            $('#progress-bar-processus-stop').val("<?php echo 'Annulation...'; ?>");
          });

          // Clear out the empty list element that's there for HTML validation purposes
          $("#progress-bar-processus-debuglist li").remove();

          // Called after each resize. Updates debug information and the progress bar.
          function ProgressActionUpdateStatus( id, success, response ) {
            $("#progress-bar-processus-bar").progressbar( "value", ( cpi_count / cpi_total ) * 100 );
            $("#progress-bar-processus-bar-percent").html( Math.round( ( cpi_count / cpi_total ) * 1000 ) / 10 + "%" );
            cpi_count = cpi_count + 1;

            if ( success ) {
              cpi_successes = cpi_successes + 1;
              $("#progress-bar-processus-debug-successcount").html(cpi_successes);
              $("#progress-bar-processus-debuglist").append("<li>" + response.success + "</li>");
            }
            else {
              cpi_errors = cpi_errors + 1;
              cpi_failedlist = cpi_failedlist + ',' + id;
              $("#progress-bar-processus-debug-failurecount").html(cpi_errors);
              $("#progress-bar-processus-debuglist").append("<li>" + response.error + "</li>");
            }
          }

          // Called when all images have been processed. Shows the results and cleans up.
          function ProgressActionFinishUp() {
            cpi_timeend = new Date().getTime();
            cpi_totaltime = Math.round( ( cpi_timeend - cpi_timestart ) / 1000 );

            $('#progress-bar-processus-stop').hide();
            $('#progress-bar-processus-start').hide();

            if ( cpi_errors > 0 ) {
              cpi_resulttext = '<?php echo $text_failures; ?>';
            } else {
              cpi_resulttext = '<?php echo $text_nofailures; ?>';
            }

            $("#message").html("<p><strong>" + cpi_resulttext + "</strong></p>");
            $("#message").show();
          }

          // processus via AJAX
          function ProgressActionProcessus( id ) {
            $.ajax({
              type: 'POST',
              url: '<?php echo admin_url('/admin-ajax.php');?>',
              data: { action: "progress_action", id: id, element:'<?php echo $this->element_slug; ;?>' },
              success: function( response ) {
                if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {
                  response = new Object;
                  response.success = false;
                  response.error = "<?php printf( esc_js(  'Le traitement s\'est terminé anormalement (ID %s). Fatal error.' ), '" + id + "' ); ?>";
                }

                if ( response.success ) {
                  ProgressActionUpdateStatus( id, true, response );
                }
                else {
                  ProgressActionUpdateStatus( id, false, response );
                }

                if ( cpi_elements.length && cpi_continue ) {
                  ProgressActionProcessus( cpi_elements.shift());
                }
                else {
                  ProgressActionFinishUp();
                }
              },
              error: function( response ) {
                ProgressActionUpdateStatus( id, false, response );

                if ( cpi_elements.length && cpi_continue ) {
                  ProgressActionProcessus( cpi_elements.shift());
                }
                else {
                  ProgressActionFinishUp();
                }
              }
            });
          }

          jQuery("#progress-bar-processus-start").click(function(){
            ProgressActionProcessus( cpi_elements.shift());
          });
        });
        // ]]>
      </script>
    </div>

  <?php
  }
}
add_action( 'wp_ajax_progress_action', array('WP_Progress_Action','progress_action_callback'));
add_action( 'admin_enqueue_scripts', array('WP_Progress_Action','admin_enqueues')  );