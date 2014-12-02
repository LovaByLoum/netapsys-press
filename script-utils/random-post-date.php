<?php
global $wpdb;
$posts = get_posts(array(
	'post_type' => "astuce",
	'post_status' => 'publish',
	'numberposts' => -1,
	'fields' => 'ids'
));
foreach ($posts as $id) {
	$date = get_post_field('post_date',intval($id));
	$array = explode(':',$date);
	
	$min = rand(0,59);
	$min = ($min<10)?'0'.$min:$min;
	
	$second = rand(0,59);
	$second = ($second<10)?'0'.$second:$second;
	
	$new_date = $array[0] .':'. $min .':'. $second;
	$sql = "UPDATE {$wpdb->prefix}posts SET post_date='" . $new_date . "',post_date_gmt='" . $new_date . "' WHERE ID=".intval($id);
	$wpdb->query($sql);
	echo get_post_field('post_title',intval($id)) . ' => ' .  $new_date . '<br>';
}