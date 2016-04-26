<?php
/**
 * generation de constante de taxonomie
 * @author Johary
 */

global $the_taxonomy, $tax_prefix;
//changer le nom de votre taxonomie ainsi que le prefix des constates ici
$the_taxonomy = 'menu_template';
$tax_prefix = 'MT_'; // pour menu_template

function wp_get_term_hierarchy($taxonomy) {
	if ( !is_taxonomy_hierarchical($taxonomy) )
		return array();

	$children = array();
	$terms = get_terms($taxonomy, array('get' => 'all', 'orderby' => 'name', 'fields' => 'id=>parent'));
	foreach ( $terms as $term_id => $parent ) {
			$children[$parent][] = $term_id;
	}

	return $children;
}
function render_const($child, $hierarchy, $parent_const_name = '',$level=0){
	global $the_taxonomy,$tax_prefix;
	foreach ($child as $id) {
		$current_term = get_term($id, $the_taxonomy);
		$nom_const = str_replace('-','_',strtoupper($current_term->slug));
		if(!empty($parent_const_name)){
			$nom_const = $parent_const_name .'_' .$nom_const;
		}
		echo "DEFINE('". $tax_prefix.$nom_const . "', '" . $current_term->slug ."');<br>";
		if(isset($hierarchy[$id])){
			render_const($hierarchy[$id], $hierarchy, $nom_const,$level+1);
		}
		if($level==0){
			echo '<br>';
		}
	}	
}

$tax_hierarchy= wp_get_term_hierarchy($the_taxonomy);
echo 'Copie le code suivant dans votre fichier de constante :<br><br><br>';

echo '//------------------------CONSTANTE ' . strtoupper($the_taxonomy).'-----generé le ' .date('d-m-Y H:i:s').'------------------<br>';
render_const($tax_hierarchy[0], $tax_hierarchy);
echo '//------------------------FIN CONSTANTE ' . strtoupper($the_taxonomy).'------------------------------------------------<br>';