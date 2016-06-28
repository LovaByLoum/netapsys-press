<?php
class JM_Candidature {

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
        global $jpress_jm_candidature_fields;
        $pid = intval($pid);
        $p = get_post($pid);

        if($p->post_type == JM_POSTTYPE_CANDIDATURE){
            $element = new stdClass();

            //champs basiques
            $element->id            = $p->ID;
            $element->titre         = $p->post_title;
            $element->slug          = $p->post_name;
            $element->date          = $p->post_date;
            $element->auteur        = $p->post_author;

            //champs supplementaires
            foreach ( $jpress_jm_candidature_fields as $field) {
                $element->$field['attribut'] = get_post_meta( $p->ID, $field['metakey'], true );
            }

            //relation
            $element->offre_associe = get_post_meta( $p->ID, JM_META_OFFRE_CANDIDATURE_RELATION, true );

            //stocker dans le tableau statique
            self::$_elements[$pid] = $element;
        }
    }

    /**
     * fonction qui retourne une liste filtrée
     *
     */
    public static function getBy($tax = null) {
        $args = array (
            'post_type' => JM_POSTTYPE_CANDIDATURE,
            'post_status' => 'publish',
            'numberposts' => -1,
            'offset' => 0,
            'order' => 'DESC',
            'orderby' => 'date',
            'fields' => 'ids'
        );

        if(!is_null($tax)) {
            $args['tax_query'][] = array (
                'taxonomy' => 'TAXONOMY',
                'field' => 'id',
                'terms' => $tax
            );
        }

        $elements = get_posts($args);
        $elts = array();
        foreach ($elements as $id) {
            $elt = self::getById(intval($id));
            $elts[]=$elt;
        }
        return $elts;

    }
}
?>