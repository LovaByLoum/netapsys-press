=== Image Classes Select ===
Contributors: Johary Ranarimanana
Tags: image, editor, photo, TinyMCE
Tested up to: 3.9
Requires at least: 3.9

== Description ==
Remplace le champ Class CSS de l'image en BO par une liste de classes prédéfinies.
(cf screenshot.png)

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

== Changelog ==

= 1.0 =
* Initial Version.


