<?php

/**
 * Plugin Name: Image Classes select
 * Author: Johary Ranarimanana
 * Description: Remplace le champ Class CSS de l'image en BO par une liste de classes prédéfinies
 * Version: 1.0
 */

/**
 * USE CASE

Pour personnaliser la liste des classes, ajouter un filtre "ics_image_classes_list" dans votre thème et retourner un tableau (clé=>valeur) contenant vos classes :

add_filter("ics_image_classes_list" , "my_image_classes_list" );
function my_image_classes_list($classes){
    return array(
        "foo" => "bar",
        "foo1" => "bar1",
        "foo2" => "bar2",
    );
}
Mettre dans les clés le nom de votre classe et dans les valeurs le libelé à afficher dans le BO.

Vous n'avez plus qu'à ajouter les styles correspondants à vos classes dans un fichier CSS à appeler à la fois en Front et en Back
**/


class Image_Classes_Select {

	const VERSION = '1.0';

	public static function init() {
		add_action( 'wp_enqueue_editor', array( __CLASS__, 'enqueue' ), 10, 1 );
	}

	public static function enqueue( $options ) {
		if ( $options['tinymce'] ) {
            wp_enqueue_script( 'ics-livequery', plugins_url( 'js/jquery.livequery.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
            wp_enqueue_script( 'ics-js', plugins_url( 'js/image-classes-select.js', __FILE__ ), array( 'jquery' ), self::VERSION, true );
            wp_localize_script('ics-js', 'ics_image_classes_list', apply_filters("ics_image_classes_list", array(
                "" => "Aucun",
                "noborder" => "Pas de bordure",
                "bordurefine" => "Bordure fine",
                "bordurelarge" => "Bordure large",
            )));
		}
	}

}

add_action( 'init', array( 'Image_Classes_Select', 'init' ) );