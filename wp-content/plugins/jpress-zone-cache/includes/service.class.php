<?php
class jPressZoneCacheService{

    public static function get_list_cache_config( $offset = 0, $limit = NULL )
    {
        global $wpdb, $jpress_zone_cache;
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM ". $jpress_zone_cache->table_cache_config . " WHERE 1=1 " ;
        $sql .=  " ORDER BY id ASC " ;

        if(!is_null($limit)){
            $sql .= " LIMIT " . $offset . ", " . $limit ;
        }
        $results = array();
        $results["data"] = $wpdb->get_results($sql);
        $sql = "SELECT FOUND_ROWS() as nbrTotal;";
        $results["total"] = $wpdb->get_var($sql);

        return  $results;
    }

    public static function get_list_caches($offset = 0, $limit = NULL)
    {
        global $wpdb, $jpress_zone_cache;
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM ".$jpress_zone_cache->table_caches . " WHERE 1=1 " ;
        $sql .=  " ORDER BY id ASC " ;

        if(!is_null($limit) && $limit>0){
            $sql .= " LIMIT " . $offset . ", " . $limit ;
        }
        $results = array();
        $results["data"] = $wpdb->get_results($sql);
        $sql = "SELECT FOUND_ROWS() as nbrTotal;";
        $results["total"] = $wpdb->get_var($sql);

        return  $results;
    }

    public static function set_cache_config($name, $role, $langue, $zone_file, $precharger = 0 )
    {
        global $wpdb, $jpress_zone_cache;
        $wpdb->insert($jpress_zone_cache->table_cache_config, array(
            'name' => sanitize_title($name),
            'role' => $role,
            'langue' => $langue,
            'zone_file' => $zone_file,
            'preload' => $precharger,
        ));
    }

    public static function delete_cache_config($ids)
    {
        global $wpdb, $jpress_zone_cache;
        $ids = implode(',',$ids);
        $sql = "DELETE FROM {$jpress_zone_cache->table_cache_config} WHERE id IN(" . $ids .")";
        $wpdb->query($sql);
    }

    public static function delete_caches($ids)
    {
        global $wpdb, $jpress_zone_cache;
        $ids = implode(',',$ids);
        $sql = "DELETE FROM {$jpress_zone_cache->table_caches} WHERE id IN(" . $ids .")";
        $wpdb->query($sql);
    }

    public static function get_cache_by( $field, $id )
    {
        global $wpdb, $jpress_zone_cache;
        $sql = "SELECT * FROM {$jpress_zone_cache->table_cache_config} WHERE {$field} = '" . $id . "'";
        return $wpdb->get_row($sql);
    }


    public static function generate_caches($ids = null)
    {
        global $wpdb, $jpress_zone_cache;
        if ( !empty($ids) ){
            $ids = implode(',',$ids);
            $sql = "SELECT * FROM {$jpress_zone_cache->table_cache_config} WHERE id IN(" . $ids .")";
        } else {
            $sql = "SELECT * FROM {$jpress_zone_cache->table_cache_config}";
        }

        $results = $wpdb->get_results($sql);
        foreach ($results as $result) {
            self::generate_cache($result);
        }
    }

    public static function get_roles() {
        $editable_roles = get_editable_roles();
        return array_keys($editable_roles);
    }

    public static function get_users_by_role(){
        $all_roles = self::get_roles();
        $users_distinct_by_role = array();
        foreach ( $all_roles as $role ){
            $users = get_users(array(
                'role' => $role,
                'number' => 1,
            ));
            if ( !empty($users) ){
                $users_distinct_by_role[$role] = $users[0]->data->ID;
            }
        }
        return $users_distinct_by_role;
    }

    public static function generate_cache($cache){
        global $jpress_zone_cache, $current_user;

        //language parameter with wpml
        if ( $cache->langue == '1' && class_exists('SitePress') ){
            global $sitepress;
            //keep original language code
            $orig_lang = ICL_LANGUAGE_CODE;

            $langues = $sitepress->get_active_languages();
            foreach ( $langues as $lang) {
                $sitepress->switch_lang($lang['code'], false);

                if ( $cache->role == '1' ){
                    $users = self::get_users_by_role();
                    $orig_user = $current_user->ID;
                    foreach ( $users as $role => $user_id ){
                        //switch to user
                        wp_set_current_user($user_id);

                        //get file generated html content
                        ob_start();
                        include ($jpress_zone_cache->dir_caches . $cache->zone_file );
                        $output = ob_get_contents();
                        ob_end_clean();
                        self::save_caches($output, $cache->id, $role, $lang['code']);

                    }
                    //switch to original user
                    wp_set_current_user($orig_user);
                } else {
                    //get file generated html content
                    ob_start();
                    include ($jpress_zone_cache->dir_caches . $cache->zone_file );
                    $output = ob_get_contents();
                    ob_end_clean();
                    self::save_caches($output, $cache->id, null, $lang['code']);
                }

            }

            //rollback to original language code
            $sitepress->switch_lang($orig_lang, false);
        } else {

            if ( $cache->role == '1' ){
                $users = self::get_users_by_role();
                $orig_user = $current_user->ID;
                foreach ( $users as $role => $user_id ){
                    //switch to user
                    wp_set_current_user($user_id);

                    //get file generated html content
                    ob_start();
                    include ($jpress_zone_cache->dir_caches . $cache->zone_file );
                    $output = ob_get_contents();
                    ob_end_clean();
                    self::save_caches($output, $cache->id, $role, null);

                }
                //switch to original user
                wp_set_current_user($orig_user);
            } else {
                //get file generated html content
                ob_start();
                include ($jpress_zone_cache->dir_caches . $cache->zone_file );
                $output = ob_get_contents();
                ob_end_clean();
                self::save_caches($output, $cache->id, null, null);
            }

        }

    }

    public static function save_caches($html, $cache_id, $role = NULL, $langue = NULL){
        global $wpdb, $jpress_zone_cache;

        $generated_date = date('Y-m-d H:i:s');
        $cache = self::get_cache_by('id', $cache_id);

        $postvar = array(
            'cache_id' => $cache_id,
            'date' => $generated_date
        );

        if(!is_null($role)){
            $postvar['role'] = $role;
        } else {
            $postvar['role'] = 'all';
        }

        if(!is_null($langue)){
            $postvar['langue'] = $langue;
        } else {
            $postvar['langue'] = 'all';
        }

        $postvar['html'] = '<!--ZONE CACHE BEGIN : ' . $cache->name . '/lang:' . $postvar['langue'] . '/role:' . $postvar['role'] . ' -->' . $html . '<!--ZONE CACHE END :  generated by jPress-Zone-Cache on ' . $generated_date . '-->';

        $wpdb->insert($jpress_zone_cache->table_caches, $postvar);
    }

    public static function get_cache( $cache_slug, $role = NULL, $lang = NULL ){
        global $wpdb, $jpress_zone_cache;
        $sql = "SELECT tc.html FROM {$jpress_zone_cache->table_caches} AS tc
    		INNER JOIN 	{$jpress_zone_cache->table_cache_config} AS tcc ON tc.cache_id =  tcc.id
		    WHERE tcc.name = '" . $cache_slug ."' ";

        if($role)
            $sql .= " AND tc.role = '" . $role . "' " ;

        if($lang)
            $sql .= " AND tc.langue = '" . $lang . "' " ;

        $sql .=  " LIMIT 1" ;

        return $wpdb->get_var($sql);
    }

    public static function purge_cache($slug, $lang = NULL, $role=NULL )
    {
        global $wpdb, $jpress_zone_cache;
        $cache = jPressZoneCacheService::get_cache_by('name',$slug);

        $sql = "DELETE FROM {$jpress_zone_cache->table_caches} WHERE cache_id = " . $cache->id ;
        if ( !is_null($lang) ){
            $sql.= " AND langue ='" . $lang."' ";
        }
        if ( !is_null($role) ){
            $sql.= " AND role ='" . $role."' ";
        }
        $wpdb->query($sql);
    }

    public static function purge_all()
    {
        global $wpdb, $jpress_zone_cache;
        $sql = "TRUNCATE TABLE {$jpress_zone_cache->table_caches}";
        $wpdb->query($sql);
    }

    public static function flush_and_preload_cache(){
        self::purge_all();
        self::generate_caches();
    }
}