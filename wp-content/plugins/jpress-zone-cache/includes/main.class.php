<?php

class jPressZoneCache{

    public $table_cache_config;
    public $table_caches;
    public $dir_caches;

    function __construct( $dir = null ){
        global $wpdb;
        $this->table_cache_config = $wpdb->prefix.'jzc_cache_config';
        $this->table_caches = $wpdb->prefix.'jzc_caches';

        if ( is_null($dir) ){
            $this->dir_caches = get_template_directory() . '/zone-cache/';
        } else {
            $this->dir_caches = $dir;
        }


        add_action('admin_menu', array($this, 'admin_menu'), 999);
        add_action('admin_init', array($this, 'admin_init'));

        //preload event
        $jzc_preload_event = get_option('jzc-preload-event', array());
        //save post
        if ( in_array('save_post', $jzc_preload_event) ){
            add_action('save_post', array($this, 'schedule_preload_cache'));
        }
        //edit_term post
        if ( in_array('edit_term', $jzc_preload_event) ){
            add_action('edit_term', array($this, 'schedule_preload_cache'));
        }

        //cron
        add_filter('cron_schedules', array($this,'add_scheduled_interval'));
        if (!wp_next_scheduled('jzc_cron_preload')) {
            wp_schedule_event(time(), '5min', 'jzc_cron_preload');
        }
        add_action('jzc_cron_preload', array($this,'preload_cache'));
    }

    function admin_menu() {
        $cap = 'activate_plugins';
        add_menu_page(
            __('Zone cache', 'jzc'),
            __('Zone cache', 'jzc'),
            $cap,
            'zone-cache',
            array($this, 'cache_config_page'),
            'dashicons-performance'
        );
    }

    function cache_config_page(){
        require_once( JZC_PATH . '/admin/cache-config-page.php' );
    }

    function admin_init() {
        if( is_admin() ){
            $this->install();
        }
    }

    function install()
    {
        global $wpdb;

        //create theme dir
        if( !file_exists($this->dir_caches) ){
            @mkdir($this->dir_caches);
        }

        // create table on first install
        if( $wpdb->get_var("show tables like '$this->table_cache_config'") != $this->table_cache_config ) {
            $sql = "CREATE TABLE  ".$this->table_cache_config." (
                        id bigint(20) NOT NULL auto_increment,
                        name varchar(255) default NULL,
                        role int(1) default NULL,
                        langue int(1) default NULL,
                        zone_file varchar(255) default NULL,
                        preload  int(1) default NULL,
                        PRIMARY KEY  (`id`)
                    ) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
            $wpdb->query($sql);
        }

        if( $wpdb->get_var("show tables like '$this->table_caches'") != $this->table_caches ) {
            $sql = "CREATE TABLE  ".$this->table_caches." (
                        id bigint(20) NOT NULL auto_increment,
                        cache_id bigint(20) NOT NULL,
                        html longtext default NULL,
                        role varchar(100) default NULL,
                        langue varchar(50) default NULL,
                        date datetime DEFAULT NULL,
                        PRIMARY KEY  (`id`),
                        KEY `cache_id` (`cache_id`),
                        UNIQUE KEY `constraint_update` (`cache_id`,`role`,`langue`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
            $wpdb->query($sql);
        }
    }

    function preload_cache(){
        $preload = get_option('jzc_preload_all', false);
        if ( $preload == true ){
            jPressZoneCacheService::flush_and_preload_cache();
            delete_option('jzc_preload_all');
        }
    }

    function schedule_preload_cache(){
        $preload = get_option('jzc_preload_all', false);
        if ( $preload == false ){
            update_option('jzc_preload_all', true);
        }
    }

    function add_scheduled_interval($schedules) {
        $schedules['5min'] = array('interval'=>5*60, 'display'=>'Once every 5 minutes');
        return $schedules;
    }
}
