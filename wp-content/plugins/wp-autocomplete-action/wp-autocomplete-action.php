<?php
/*
Plugin Name: WP Autocomplete Action
Description: Classe utilitaire pour les autocompletions
Author: Fitiavana (Netapsys)
Version: 0.1
*/
/*

//////////////Exemple d'instanciation//////////////////
/*
$autocomplete_input = new WP_Autocomplete_Action('nom-element');
// Affichage du input pour la recherche par auto completion
$autocomplete_input->display();

//hook
add_filter('autocomplete_action_callback','autocomplete_action_my_callback');
cette filtre sert à controler les données recherchées

add_action('autocomplete_select_callback','autocomplete_select_my_callback');
cet action sert à  implementer le script après selection du texte rechercher
 */
class WP_Autocomplete_Action{
  public $id_elt;
  public $width;
  public $placeholder;
  public function __construct($id, $width = 250, $placeholder ='Entrer votre texte'){
    $this->id_elt = $id;
    $this->width = $width;
    $this->placeholder = $placeholder;
  }

  public function display(){
    ?>
    <input type="text" id="<?php echo $this->id_elt;?>" name="<?php echo $this->id_elt;?>" class="autocomplete-action initialize-url-text" placeholder="<?php echo $this->placeholder; ?>" title="<?php echo $this->placeholder; ?>" style="width: <?php echo $this->width; ?>px;"/>
    <?php
  }

  public static function autocomplete_action_callback(){
    $term = sanitize_text_field($_GET['term']);
    $id = $_GET['id'];
    $id = str_replace('-','_',$id);
    $results = apply_filters('autocomplete_action_callback_'.$id , $term);

    echo json_encode(array('results' => array_slice($results, 0, 100)));
    die();
  }

  public static function admin_enqueues(){
    $localVars = array(
      'ajaxurl' => admin_url( 'admin-ajax.php')
    );
    wp_enqueue_style('SearchAutocomplete-theme', plugins_url( 'css/redmond/jquery-ui-1.9.2.custom.css' , __FILE__ ), array(), '1.9.2');
    wp_enqueue_script('jquery-ui-autocomplete', plugins_url('js/jquery-ui-1.9.2.custom.min.js', __FILE__ ), array('jquery-ui'), '1.9.2', true);
    wp_localize_script('jquery-ui-autocomplete', 'autocompleteaction', $localVars);
  }

  public static function autocomplete_script(){
    ?>
    <script type="text/javascript">
      (function($) {
        $(function() {
          $('.autocomplete-action').autocomplete({
            source: function( request, response ) {
              _id = this.element[0].id;
              $.ajax({
                url: autocompleteaction.ajaxurl,
                dataType: "json",
                data: {
                  action: 'autocomplete_action',
                  id: _id,
                  term: this.term
                },
                success: function( data ) {
                  response( $.map( data.results, function( item ) {
                    return {
                      label: item.label,
                      value: item.value,
                      id: item.id
                    }
                  }));
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  console.log(jqXHR, textStatus, errorThrown);
                }
              });
            },
            minLength: 2,
            search: function(event, ui) {
              $(event.currentTarget).addClass('sa_searching');
            },
            create: function() {
            },
            select: function( event, ui ) {
              _id = event.target.id;
              _id =  _id.replace('-','_');
              var _function = "autocomplete_select_callback_"+_id;
              eval(_function+"(event,ui);");
            },
            open: function(event, ui) {
              $(event.target).removeClass('sa_searching');
            },
            close: function() {
            }
          });
        });
      })(jQuery);
    </script>
    <?php
  }
}
add_action('in_admin_footer', array('WP_Autocomplete_Action','autocomplete_script'));
add_action( 'wp_ajax_autocomplete_action', array('WP_Autocomplete_Action','autocomplete_action_callback'));
add_action( 'admin_enqueue_scripts', array('WP_Autocomplete_Action','admin_enqueues')  );