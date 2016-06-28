<?php
class JM_Offre {

    private static $_elements;

    public function __construct() {

    }

    /**
     * fonction qui prend les informations son Id.
     *
     * @param type $pid
     */
    public static function getById($pid) {
        $pid = intval($pid);

        //On essaye de charger l'element
        if(!isset(self::$_elements[$pid])) {
            self::_load($pid);
        }
        //Si on a pas réussi à chargé l'article (pas publiée?)
        if(!isset(self::$_elements[$pid])) {
            return FALSE;
        }

        return self::$_elements[$pid];
    }

    /**
     * fonction qui charge toutes les informations dans le variable statique $_elements.
     *
     * @param type $pid
     */
    private static function _load($pid) {
        global $jpress_jm_offre_fields;
        $pid = intval($pid);
        $p = get_post($pid);

        if ($p->post_type == JM_POSTTYPE_OFFRE) {
            $element = new stdClass();

            //champs basiques
            $element->id            = $p->ID;
            $element->titre         = $p->post_title;
            $element->slug          = $p->post_name;
            $element->description   = apply_filters('the_content', $p->post_content);
            $element->extrait       = empty($p->post_excerpt) ? jpress_jm_limite_text( strip_shortcodes( strip_tags( $p->post_content ) ), 200 ) : $p->post_excerpt ;
            $element->date          = $p->post_date;
            $element->auteur        = $p->post_author;

            //champs supplementaire
            foreach ( $jpress_jm_offre_fields as $field) {
                $element->$field['attribut'] = get_post_meta( $p->ID, $field['metakey'], true );
            }

            //relation
            $element->societe_associe = get_post_meta( $p->ID, JM_META_SOCIETE_OFFRE_RELATION, true );

            //term associé
            $element->localisation  = wp_get_post_terms($p->ID, JM_TAXONOMIE_LOCALISATION);
            $element->service       = wp_get_post_terms($p->ID, JM_TAXONOMIE_DEPARTEMENT);
            $element->type_contrat  = wp_get_post_terms($p->ID, JM_TAXONOMIE_TYPE_CONTRAT);
            $element->categorie     = wp_get_post_terms($p->ID, JM_TAXONOMIE_CATEGORIE);

            //stocker dans le tableau statique
            self::$_elements[$pid] = $element;
        }
    }

    /**
     * fonction qui retourne une liste filtrée
     */
    public static function getBy($tag = null, $exclude = null, $number = NULL, $return_ids = false, $meta = null) {
        $args = array(
            'post_type' => JM_POSTTYPE_OFFRE,
            'post_status' => 'publish',
            'numberposts' => -1,
            'offset' => 0,
            'order' => 'DESC',
            'orderby' => 'date',
            'fields' => 'ids',
            'suppress_filters' => false
        );

        if ($number != NULL) {
            $args['numberposts'] = $number;
        }
        if (!is_null($tag)) {
            $args['tax_query'][] = array(
                'taxonomy' => $tag['taxonomy'],
                'field' => 'ids',
                'terms' => $tag['id'],
                'operator' => 'IN'
            );
        }

        if (!is_null($meta)) {
            $args['meta_query'][] = array(
                'key' => $meta['key'],
                'value' => $meta['value'],
                'compare' => '='
            );
        }

        if (!is_null($exclude)) {
            $args['post__not_in'] = $exclude;
        }

        $elements = get_posts($args);
        $elts = array();
        foreach ($elements as $id) {
            $elt = self::getById(intval($id));
            if ($return_ids){
                $elts[] = $id;
            }else{
                $elts[] = $elt;
            }
        }
        return $elts;
    }

    /**
     * Callback offres voir plus
     * @param $offset
     * @param $limit
     * @param $filters
     * @param $sorting
     * @param $extra_args
     * @return array() de offre
     */
    public static function getItemsCallback( $offset, $limit, $filters, $sorting, $extra_args ) {
        $paged =  intval( $offset / $limit + 1 );
        $args = array (
            'post_type' => JM_POSTTYPE_OFFRE,
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'paged' => $paged,
            'order' => 'DESC',
            'orderby' => 'post_date',
            'fields' => 'ids',
            'suppress_filters' => (ICL_LANGUAGE_CODE == "fr") ? true : false,
        );

        $active_filters = jpress_jm_is_in_options( JM_META_LIST_OFFRE_FILTRE, 'template-list' );
        if ( !empty($active_filters) ){
            foreach ($active_filters as $tax) {
                if ( isset($filters['filter-' . $tax]) && !empty($filters['filter-' . $tax])) {
                    $args['tax_query'][] =  array(
                            'taxonomy' => $tax,
                            'field' => 'id',
                            'terms' =>  $filters['filter-' . $tax],
                            'include_children' => true
                        );
                }
            }
        }

        if (!empty ($sorting) && isset($sorting['order']) && isset($sorting['orderby']) ){
            $args['order'] = $sorting['order'];
            $args['orderby'] = 'meta_value';
            $args['meta_key'] = $sorting['orderby'];
        }


        $args = wp_parse_args( $args, $extra_args );
        $elements = query_posts($args);
        return $elements;
    }

    /**
     * Créer les li de la liste des offres
     * @param $id
     * @return html
     */
    public static function renderItemCallback($id){
        $offre = self::getById($id);
        $html = '
                    <p><a href="' . get_permalink($offre->id) .  '">' . $offre->titre . '</a></p>
                    <p>' . $offre->extrait . '</p>
                    <p>' . $offre->salaire . '</p>
                    <p>' . jpress_jm_implode_array_of_object( $offre->service, 'name') . '</p>
                    <p>' . jpress_jm_implode_array_of_object( $offre->localisation, 'name') . '</p>
                    <p>' . jpress_jm_implode_array_of_object( $offre->type_contrat, 'name') . '</p>
                    <p>' . jpress_jm_implode_array_of_object( $offre->categorie, 'name') . '</p>
                    <p>' . __('Diffusé le ', 'jpress-job-manager') . date('d/m/Y', strtotime($offre->date)) . '</p>
                ';
        return $html;
    }

    public static function get_nombre_candidature($oid){
       global $wpdb;
       return $wpdb->get_var( "SELECT COUNT(*) FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '" . JM_META_OFFRE_CANDIDATURE_RELATION . "' AND meta_value = " . $oid);
    }
}
?>
