<?php
$path = realpath(dirname(__FILE__));
$strend = strpos($path,'wp-content');
$path = substr($path,0,$strend);
require_once($path . "wp-load.php");

?>
<style>
  thead{
    text-align: left;
  }
  tbody{
    display: table-row-group;
    vertical-align: middle;
    border-color: inherit;
  }
  table{
    border-color: #dfdfdf;
    background-color: #f9f9f9;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    border-width: 1px;
    border-style: solid;
  }
  th{
    border-top-left-radius: 3px;
    background: #f1f1f1;
    background-image: -webkit-gradient(linear,left bottom,left top,from(#ececec),to(#f9f9f9));
    background-image: -webkit-linear-gradient(bottom,#ececec,#f9f9f9);
    background-image: -moz-linear-gradient(bottom,#ececec,#f9f9f9);
    background-image: -o-linear-gradient(bottom,#ececec,#f9f9f9);
    background-image: linear-gradient(to top,#ececec,#f9f9f9)
  }
  tr{
    display: table-row;
    vertical-align: inherit;
    border-color: inherit;
  }

</style>
<h2>Listes des taxonomies du site</h2>
<?php
global $wp_taxonomies;
$taxonomies_to_display = apply_filters('jpress_ie_taxonomies_helper',$wp_taxonomies);
if(sizeof($taxonomies_to_display)>0){
  foreach ($taxonomies_to_display as $tax) {
    echo "<h3>{$tax->labels->name}</h3>";
    $terms = get_terms($tax->name,array('hide_empty'=>false));
    echo "<table><thead><tr><th>Terme</th><th>Identifiant</th></tr></thead><tbody>";
    foreach ( $terms as $t) {
      echo "<tr><td>{$t->name}</td><td>{$t->term_id}</td></tr>";
    }
    echo "</tbody></table>";
  }
}

$post_to_display = apply_filters('jpress_ie_posts_helper',array());
if(sizeof($post_to_display)>0){
  echo "<h2>Listes des types de publication du site</h2>";
  foreach ($post_to_display as $slug) {
    $postt = get_post_type_object($slug);
    echo "<h3>{$postt->labels->name}</h3>";
    $posts = get_posts(array(
      'post_type'=>$slug,
      'post_status'=>'publish',
      'orderby' => 'name',
      'order' => "ASC",
      'numberposts' => -1
    ));
    echo "<table><thead><tr><th>Titre</th><th>Identifiant</th></tr></thead><tbody>";
    foreach ( $posts as $p) {
      echo "<tr><td>{$p->post_title}</td><td>{$p->ID}</td></tr>";
    }
    echo "</tbody></table>";
  }
}

//another stuff
do_action('jpress_ie_other_helper');

?>