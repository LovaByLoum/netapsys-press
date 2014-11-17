<?php
//hook sur les requettes
add_filter('query', 'jcpt_query');
function jcpt_query($q){
	global $wpdb,$jcpt_options,$pagenow;
    if(is_null($jcpt_options)){
	    $jcpt_options = get_option('jcpt_options');
        //return $q;
    }
	if(empty($jcpt_options['enable'])) return $q;
  //ignore requeste
  if(strpos($q, $wpdb->prefix.'options')){
    return $q;
  }

	$traces = array_map("jcpt_get_function_in_array", debug_backtrace());

	//get_post request
	if(
		in_array('get_post',$traces)&&
		preg_match("/^SELECT \* FROM {$wpdb->prefix}posts WHERE ID = ([0-9]+) LIMIT 1$/",$q,$matches)
		){
			$table = jcpt_whereis($matches[1]);
			$q = "SELECT * FROM $table WHERE ID = ". $matches[1] ." LIMIT 1";
	//vider corbeille
	}elseif(
		in_array('get_col',$traces) &&
		preg_match("/^SELECT ID FROM {$wpdb->prefix}posts WHERE post_type='(.*?)' AND post_status = '(.*?)'$/",$q,$matches)
		){
			$post_type = $matches[1];
      if(in_array($post_type,$jcpt_options['enable']))
			  $q = str_replace("{$wpdb->prefix}posts",$wpdb->prefix . $post_type . "s",$q);
	//get_posts request on post type
	}elseif(
			(in_array('get_posts',$traces) ||
			in_array('wp_count_posts',$traces)
			)&&
			preg_match("/post_type = '(.*?)'/",$q,$matches)
		){
			if(in_array($matches[1],$jcpt_options['enable'])){
        $q = str_replace($wpdb->prefix.'posts',$wpdb->prefix.$matches[1].'s',$q);
        $q = str_replace('AND 0 = 1','',$q);
        $regex = "/SELECT (.*?) FROM (.*?)  (INNER JOIN) (.*?) (ON) (.*?) WHERE (.*?) AND \((.*?)\) AND (.*?) GROUP BY (.*?) ORDER BY (.*?) (LIMIT) (.*?)$/";
        preg_match($regex,$q,$orderby);
        if((isset($_REQUEST['orderby'])  && !empty($_REQUEST['orderby'])) && (isset($orderby[11]) && !empty($orderby[11]))){
          $meta_key = jcpt_get_column_by_metaname($matches[1],$_REQUEST['orderby']);
            $q = preg_replace($regex,"SELECT $1 FROM $2 WHERE $7 AND ($8) GROUP BY $10 ORDER BY " . $meta_key . " " . ((isset($_REQUEST['order']) && !empty($_REQUEST['order'])) ? $_REQUEST['order'] : "ASC"). " $12 $13",$q);
        }
      }
	//admin list request
	}elseif(
			in_array('get_posts',$traces) &&
			preg_match("/{$wpdb->prefix}posts/",$q) &&
			$pagenow == 'edit.php'
		){
			$post_type = isset($_REQUEST["post_type"])?$_REQUEST["post_type"]:'post';
			if(in_array($post_type,$jcpt_options['enable'])){
				$posttable = $wpdb->prefix.$post_type."s";
				$q = str_replace($wpdb->prefix.'posts',$posttable,$q);
			}
	//wp_update_post
	}elseif(
		 (
		  in_array('wp_update_post',$traces)||
		  in_array('wp_insert_post',$traces)||
		  in_array('wp_unique_post_slug',$traces)
		 )&&
		 (
		  preg_match("/post_type = '(.*?)'/",$q,$matches) ||
		  preg_match("/`post_type` = '(.*?)'/",$q,$matches)
		 )
		){
		$post_type = $matches[1];
		if(in_array($post_type,$jcpt_options['enable'])){
			$posttable = $wpdb->prefix.$post_type."s";
			$q = str_replace($wpdb->prefix.'posts',$posttable,$q);
		}
	//other admin request
	}elseif(
			(
			in_array('wp_update_term_count_now',$traces) ||
			in_array('is_multi_author',$traces)
			) &&
			preg_match("/{$wpdb->prefix}posts/",$q) &&
			$pagenow == 'edit.php'
		){
			$post_type = isset($_REQUEST["post_type"])?$_REQUEST["post_type"]:(isset($_POST["post_type"])?$_POST["post_type"]:'post');
			if(in_array($post_type,$jcpt_options['enable'])){
				$posttable = $wpdb->prefix.$post_type."s";
				$q = str_replace($wpdb->prefix.'posts',$posttable,$q);
				$q = str_replace("'post'","'$post_type'",$q);
			}
	//update post count
	}elseif(
			in_array('_update_post_term_count',$traces)
		){
			$sql = "SELECT (" . $q . ")";
			foreach ($jcpt_options['enable'] as $posttype) {
				$posttable = $wpdb->prefix.$posttype.'s';
				$tmp_q = str_replace($wpdb->prefix.'posts',$posttable,$q);
				$sql.= "+(" .$tmp_q.")";
			}
			$q = $sql;
	//insert post meta
	}elseif(
			in_array('add_post_meta',$traces)&&
            preg_match("/VALUES \((.*?),'(.*?)',(.*?)\)/",$q,$matches)
		){
			$ID = $matches[1];
			$table = jcpt_whereis($ID);
			$post_type = jcpt_whois($ID);
			$metakey = $matches[2];
			$metavalue = $matches[3];
			if(in_array($post_type,$jcpt_options['enable']) && in_array($metakey,jcpt_get_metanames($post_type))){
				$columnname = jcpt_get_column_by_metaname($post_type,$metakey);
				$q = "UPDATE {$table} SET {$columnname} = {$metavalue} WHERE ID={$ID}";
			}
    //publish post
    }elseif(
        in_array('wp_publish_post',$traces)&&
        in_array('update',$traces)&&
        preg_match("/ WHERE `ID` = (.*?)$/",$q,$matches)
    ){
        $ID = $matches[1];
        $table = jcpt_whereis($ID);
        $post_type = jcpt_whois($ID);
        if(in_array($post_type,$jcpt_options['enable'])){
            $q = str_replace('posts',$post_type.'s',$q);
        }
    //update_post_meta
    }elseif(
        in_array('update_metadata',$traces)&&
        in_array('update',$traces)&&
        preg_match("/`post_id` = (.*?) AND/",$q,$matches_id)&&
        preg_match("/`meta_key` = '(.*?)'/",$q,$matches_metakey)&&
        preg_match("/SET `meta_value` = (.*?) WHERE/",$q,$matches_metavalue)
    ){
        $ID = $matches_id[1];
        $table = jcpt_whereis($ID);
        $post_type = jcpt_whois($ID);
        $metakey = $matches_metakey[1];
        $metavalue = $matches_metavalue[1];
        if(in_array($post_type,$jcpt_options['enable']) && in_array($metakey,jcpt_get_metanames($post_type))){
            $columnname = jcpt_get_column_by_metaname($post_type,$metakey);
            $q = "UPDATE {$table} SET {$columnname} = {$metavalue} WHERE ID={$ID}";
        }
  //update_post_meta insert
  }elseif(
    in_array('update_metadata',$traces)&&
    in_array('insert',$traces)&&
    preg_match("/INSERT INTO `ffcv_postmeta`/",$q)&&
    preg_match("/VALUES \((.*?),'(.*?)',(.*?)\)/",$q,$matches)
  ){
    $ID = $matches[1];
    $table = jcpt_whereis($ID);
    $post_type = jcpt_whois($ID);
    $metakey = $matches[2];
    $metavalue = $matches[3];
    if(in_array($post_type,$jcpt_options['enable']) && in_array($metakey,jcpt_get_metanames($post_type))){
      $columnname = jcpt_get_column_by_metaname($post_type,$metakey);
      $q = "UPDATE {$table} SET {$columnname} = {$metavalue} WHERE ID={$ID}";
    }
	//get meta
	}elseif(
			in_array('_get_meta',$traces)&&
            preg_match("/post_id=(.*?)\n/",$q,$match_postid) &&
            preg_match("/meta_key='(.*?)'/",$q,$match_metakey)
		){
			$ID = $match_postid[1];
			$table = jcpt_whereis($ID);
			$post_type = jcpt_whois($ID);
			$metakey = $match_metakey[1];
			if(in_array($post_type,$jcpt_options['enable']) && in_array($metakey,jcpt_get_metanames($post_type))){
				$columnname = jcpt_get_column_by_metaname($post_type,$metakey);
				$q = "SELECT NULL AS meta_id, NULL AS post_id, NULL AS meta_key, {$columnname} AS meta_value FROM {$table} WHERE ID = ".$ID;
			}
	//wp_insert_post autodraft
	}elseif(
			in_array('wp_insert_post',$traces)&&
            preg_match("/VALUES \((.*?),'(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','auto-draft','(.*?)'/",$q,$matches)
		){
			$post_type = $matches[8];
			if(in_array($post_type,$jcpt_options['enable'])){
				$ID = jcpt_getnewid();
				$q = str_replace('`'.$wpdb->prefix.'posts` (',$wpdb->prefix.$post_type.'s'.' (ID,',$q);
				$q = str_replace('VALUES (','VALUES ('.$ID.',',$q);
			}else{
                $ID = jcpt_getnewid();
                $q = str_replace('`'.$wpdb->prefix.'posts` (',$wpdb->prefix.'posts (ID,',$q);
                $q = str_replace('VALUES (','VALUES ('.$ID.',',$q);
            }
	//wp_create_post_autosave
	}elseif(
			in_array('wp_create_post_autosave',$traces)&&
			in_array('_wp_put_post_revision',$traces)&&
			in_array('insert',$traces)&&
            preg_match("/INSERT INTO/",$q) &&
            preg_match("/'revision'/",$q) &&
            preg_match("/VALUES \((.*?),'(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','revision','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)',(.*?),(.*?),'(.*?)'\)/",$q,$matches)
		){
			$post_parent = $matches[17];
			$post_type = jcpt_whois($post_parent);
			if(in_array($post_type,$jcpt_options['enable'])){
				$ID = jcpt_getnewid();
				$q = str_replace('`'.$wpdb->prefix.'posts` (',$wpdb->prefix.$post_type.'s'.' (ID,',$q);
				$q = str_replace('VALUES (','VALUES ('.$ID.',',$q);
			}else{
                $ID = jcpt_getnewid();
                $q = str_replace('`'.$wpdb->prefix.'posts` (',$wpdb->prefix.'posts (ID,',$q);
                $q = str_replace('VALUES (','VALUES ('.$ID.',',$q);
            }
	//update revision
	}elseif(
			in_array('wp_create_post_autosave',$traces)&&
			in_array('_wp_put_post_revision',$traces)&&
			in_array('update',$traces)&&
            preg_match("/UPDATE/",$q) &&
            preg_match("/`ID` = (.*?)$/",$q,$matches)
		){
			$ID = $matches[1];
			$table = jcpt_whereis($ID);
			$q = str_replace($wpdb->prefix.'posts',$table,$q);
	}elseif(
    in_array('wp_insert_post',$traces)&&
    in_array('insert',$traces)&&
    preg_match("/INSERT INTO/",$q) &&
    preg_match("/VALUES \((.*?),'(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)','(.*?)',(.*?),(.*?),'(.*?)'\)/",$q,$matches)
  ){
    $post_type = $matches[9];
    if(in_array($post_type,$jcpt_options['enable'])){
      $ID = jcpt_getnewid();
      $q = str_replace('`'.$wpdb->prefix.'posts` (',$wpdb->prefix.$post_type.'s'.' (ID,',$q);
      $q = str_replace('VALUES (','VALUES ('.$ID.',',$q);
    }else{
        $ID = jcpt_getnewid();
        $q = str_replace('`'.$wpdb->prefix.'posts` (',$wpdb->prefix.'posts (ID,',$q);
        $q = str_replace('VALUES (','VALUES ('.$ID.',',$q);
    }
  // wp_insert_attachment
  }elseif(in_array('wp_insert_attachment',$traces) && in_array('insert',$traces) && (preg_match("/INSERT INTO/",$q,$matches))){
    $ID = jcpt_getnewid();
    $q = str_replace('`'.$wpdb->prefix.'posts` (',$wpdb->prefix.'posts (ID,',$q);
    $q = str_replace('VALUES (','VALUES ('.$ID.',',$q);
  //delete post
  }elseif(
        in_array('wp_delete_post',$traces) &&
        (
        preg_match("/SELECT \* FROM.*WHERE ID = (.*?)$/",$q,$matches)||
        preg_match("/DELETE FROM.*WHERE ID = (.*?)$/",$q,$matches)
        )
    ){
        if(isset($matches[1]) && is_numeric($matches[1])){
          $post_id = $matches[1];
          $post_type = jcpt_whois($post_id);
          if(in_array($post_type,$jcpt_options['enable'])){
            $table = jcpt_whereis($post_id);
            $q = str_replace($wpdb->prefix . "posts", $table, $q);
          }
        }
    //set_post_type
    }elseif(
        in_array('set_post_type',$traces) &&
        (
            preg_match("/UPDATE.*WHERE `ID` = (.*?)$/",$q,$matches)
        )
    ){
        if(isset($matches[1]) && is_numeric($matches[1])){
            $post_id = $matches[1];
            $post_type = jcpt_whois($post_id);
            if(in_array($post_type,$jcpt_options['enable'])){
                $table = jcpt_whereis($post_id);
                $q = str_replace($wpdb->prefix . "posts", $table, $q);
            }
        }
    //
    }

  /*global $wp_utils;
  $wp_utils->log($traces);
  $wp_utils->log($q);
  $wp_utils->log(str_repeat("*",400));*/
// mp($traces,false);
// mp($q,false);
  return $q;
}
function jcpt_get_function_in_array($a){
	return $a['function'];
}

//HOOKS
add_filter('get_post_metadata', 'jcpt_get_post_metadata',10,4);
function jcpt_get_post_metadata($value, $object_id, $meta_key, $single){
    global $wpdb,$jcpt_options;
    if(is_null($jcpt_options)){
        $jcpt_options = get_option('jcpt_options');
        //return $q;
    }
    $post_type = jcpt_whois($object_id);
    $table = jcpt_whereis($object_id);
    if(in_array($post_type,$jcpt_options['enable']) && in_array($meta_key,jcpt_get_metanames($post_type))){
        $columnname  = jcpt_get_column_by_metaname($post_type,$meta_key);
        $value = $wpdb->get_var("SELECT {$columnname} FROM {$table} WHERE ID={$object_id}");
        return ($single)?$value:array($value);
    }

    return null;
}

/**
 * ajout recherche
 */
global $jcpt_options;
if(is_null($jcpt_options)){
    $jcpt_options = get_option('jcpt_options');
}

if(is_array($jcpt_options['enable'])){
    foreach ($jcpt_options['enable'] as $postype) {
      if($_GET['post_type'] == $postype){
        eval("add_filter('posts_search', 'jcpt_".$postype."_search');");
        $function = "
            function jcpt_".$postype."_search(\$search){
                global \$wpdb,\$wp_query,\$jcpt_options;
                \$table = \$wpdb->prefix." . $postype . "s;
                \$columns = jcpt_get_columns('".$postype."');
                \$types = jcpt_get_types('".$postype."');
                foreach(\$columns as \$key => \$column){
                    if(!empty(\$wp_query->query_vars[\$column])){
                        if((is_numeric(\$wp_query->query_vars[\$column])) && (\$types[\$key] == 'BIGINT' || \$types[\$key] == 'INT(11)')){
                          \$search .= \" AND \" . \$table . \".\" . \$column . \" = \" . \$wp_query->query_vars[\$column] .  \" \";
                        }elseif(is_array(\$wp_query->query_vars[\$column])){
                          \$search .= \" AND \" . \$table . \".\" .\$column . \" IN (\" . implode(',',\$wp_query->query_vars[\$column]) . \") \";
                        }else{
                          \$search .= \" AND \" . \$table . \".\" .\$column . \" LIKE '%\" . addslashes(\$wp_query->query_vars[\$column]) . \"%' \";
                        }
                    }
                }

                return \$search;
            }
        ";

        eval($function);
    }
  }
}
