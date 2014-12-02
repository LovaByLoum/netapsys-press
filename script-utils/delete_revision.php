<?php
/**
 * supprime les revisions
 */

global $wpdb;

$sql = "DELETE 		a,b,c
FROM 		{$wpdb->prefix}posts a
LEFT JOIN 	{$wpdb->prefix}term_relationships b
ON 			(a.ID = b.object_id)
LEFT JOIN 	{$wpdb->prefix}postmeta c
ON 			(a.ID = c.post_id)
WHERE        a.post_type = 'revision'";

$wpdb->query($sql);

echo 'done';

