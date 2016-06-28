<?php
class JM_Societe {

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
        global $jpress_jm_societe_fields;
        $pid = intval($pid);
        $p = get_post($pid);

        if ( $p->post_type == JM_POSTTYPE_SOCIETE ) {
            $element = new stdClass();

            //champs basiques
            $element->id            = $p->ID;
            $element->titre         = $p->post_title;
            $element->slug          = $p->post_name;
            $element->description   = apply_filters('the_content', $p->post_content);
            $element->extrait       = empty($p->post_excerpt) ? jpress_jm_limite_text( strip_shortcodes( strip_tags( $p->post_content ) ), 200 ) : $p->post_excerpt ;
            $element->date          = $p->post_date;
            $element->auteur        = $p->post_author;
            list($element->logo)    = wp_get_attachment_image_src( get_post_thumbnail_id($p->ID), 'thumbnail' ) ;

            //champs supplementaire
            foreach ( $jpress_jm_societe_fields as $field) {
                $element->$field['attribut'] = get_post_meta( $p->ID, $field['metakey'], true );
            }
            //term associé
            $element->domaine  = wp_get_post_terms($p->ID, JM_TAXONOMIE_DOMAINE);

            //stocker dans le tableau statique
            self::$_elements[$pid] = $element;
        }
    }

    /**
     * fonction qui retourne une liste filtrée
     */
    public static function getBy($tag = null, $exclude = null, $number = NULL) {
        $args = array(
            'post_type' => JM_POSTTYPE_SOCIETE,
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
                'taxonomy' => 'theme',
                'field' => 'ids',
                'terms' => $tag,
                'operator' => 'IN'
            );
        }

        if (!is_null($exclude)) {
            $args['post__not_in'] = $exclude;
        }

        $elements = get_posts($args);
        $elts = array();
        foreach ($elements as $id) {
            $elt = self::getById(intval($id));
            $elts[] = $elt;
        }
        return $elts;
    }

}
?>
