<?php
/**
 * Plugin Name: jPress Archive
 * Plugin URI:
 * Text Domain: jpress_archive
  * Description: Mise en place de la fonctionnalité d'archivage par post_status pour tous les post type
 * Author: Johary Ranarimanana
 * Version: 0.0.1
 */

function jpress_archive_post_status(){
    register_post_status( 'archived', array(
        'label'                     => __( 'Archivé', 'jpress_archive'),
        'public'                    => false,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Archivé <span class="count">(%s)</span>', 'Archivé <span class="count">(%s)</span>' ),
    ) );
}
add_action( 'init', 'jpress_archive_post_status' );

add_action('admin_head', 'jpress_archive_head');
function jpress_archive_head(){
     global $wp_post_types;
     ?>
        <script>
            jQuery(document).ready(function(){
                <?php
                foreach ( $wp_post_types as $pt=>$data) :
                    switch($pt){
                        case 'post':?>
                            jQuery('<li><a href="edit.php?post_type=<?php echo $pt;?>&amp;post_status=archived">Archives</a></li>').appendTo('#menu-posts ul.wp-submenu');
                            <?php break;
                        case 'page':?>
                            jQuery('<li><a href="edit.php?post_type=<?php echo $pt;?>&amp;post_status=archived">Archives</a></li>').appendTo('#menu-pages ul.wp-submenu');
                            <?php break;
                        default: ?>
                            jQuery('<li><a href="edit.php?post_type=<?php echo $pt;?>&amp;post_status=archived">Archives</a></li>').appendTo('#menu-posts-<?php echo $pt;?> ul.wp-submenu');
                            <?php break;
                    }
                endforeach;?>
            })
        </script>
    <?php
}

add_filter( 'post_row_actions','jpress_archive_add_archive_link',10,2);
function jpress_archive_add_archive_link( $actions, $id ) {
    global $post, $current_screen, $mode;

    $post_type_object = get_post_type_object( $post->post_type );

    if ( ! current_user_can( $post_type_object->cap->delete_post, $post->ID ) )
        return $actions;

    if($post->post_status != "archived"){
        $archive_link = admin_url( 'admin.php?post=' . $post->ID . '&action=jpress_archive' );
        $archive_link =   wp_nonce_url( $archive_link, "jpress_archive-{$post->post_type}_{$post->ID}" );
        $actions['archive'] = '<a href="' . $archive_link
            . '" title="'
            . esc_attr( __( 'Déplacer dans les archives', 'jpress_archive'  ) )
            . '">' . __( 'Archiver', 'jpress_archive'  ) . '</a>';
    }else{
        $archive_link = admin_url( 'admin.php?post=' . $post->ID . '&action=jpress_unset_archive' );
        $archive_link =   wp_nonce_url( $archive_link, "jpress_unset_archive-{$post->post_type}_{$post->ID}" );
        $actions['restore'] = '<a href="' . $archive_link
            . '" title="'
            . esc_attr( __( 'Restaurer', 'jpress_archive'  ) )
            . '">' . __( 'Restaurer', 'jpress_archive'  ) . '</a>';
    }

    return $actions;
}

add_action( 'admin_action_jpress_archive', 'jpress_archive_post_type'  );
function jpress_archive_post_type () {

    if ( ! (
        isset( $_GET['post']) ||
        ( isset( $_REQUEST['action']) && 'jpress_archive' == $_REQUEST['action'] )
    ) ) {
        wp_die( __( 'Aucun post à archiver !',  'jpress_archive'  ) );
    }

    $id = (int) ( isset( $_GET['post']) ? $_GET['post'] : $_REQUEST['post']);

    if ( $id ) {
        $redirect_post_type = '';
        $archived_post_type = get_post_type( $id );
        if ( ! empty( $archived_post_type ) )
            $redirect_post_type = 'post_type=' . $archived_post_type . '&';

        // add old post_status to post meta
        $pst = get_post($id);
        add_post_meta( $id, 'jpress_archive_old_post_status', $pst->post_status, TRUE );
        // change post status
        jpress_change_post_status( $id, 'archived' );

        wp_redirect( admin_url( 'edit.php?' . $redirect_post_type . '&post_status=archived&archived=1&ids=' . $id ) );
        exit;
    } else {
        wp_die( __( 'Désolé, aucun ID spécifié.', 'jpress_archive' ) );
    }

}

add_action( 'admin_action_jpress_unset_archive', 'jpress_unset_archive_post_type'  );
function jpress_unset_archive_post_type () {

    if ( ! (
        isset( $_GET['post']) ||
        ( isset( $_REQUEST['action']) && 'jpress_unset_archive' == $_REQUEST['action'] )
    ) ) {
        wp_die( __('Aucun post à restaurer !', 'jpress_archive' ) );
    }

    $id = (int) ( isset( $_GET['post']) ? $_GET['post'] : $_REQUEST['post']);

    if ( $id ) {
        $redirect_post_type = '';
        // get archived post type
        $archived_post_type = get_post_type( $id );
        $archived_post_status = get_post_meta( $id, 'jpress_archive_old_post_status', TRUE );
        if ( ! empty( $archived_post_status ) )
            $redirect_post_type = 'post_type=' . $archived_post_type . '&';
        // change post status to old archived post status
        jpress_change_post_status( $id, $archived_post_status );
        // remove archived post type on post meta
        delete_post_meta( $id, 'jpress_archive_old_post_status' );
        // redirect to edit-page od post type
        wp_redirect( admin_url( 'edit.php?' . $redirect_post_type . 'unset_archived=1&ids=' . $id ) );
        exit;
    } else {
        wp_die( __( 'Désolé, aucun ID spécifié', 'jpress_archive' ) );
    }

}

add_action( 'admin_notices', 'jpress_archive_get_admin_notices'  );
function jpress_archive_get_admin_notices () {

    settings_errors( 'archived_message' );
    settings_errors( 'unset_archived_message' );
}

add_action( 'admin_init', 'jpress_archive_add_settings_error' );
function jpress_archive_add_settings_error () {

    $message_archived = NULL;
    $message_unset_archived = NULL;

    if ( isset( $_REQUEST['archived'] ) ) {
        $message_archived = sprintf(
            _n( 'L\'élément a été déplacé dans les archives.',
                '%s éléments déplacés dans les archives.',
                $_REQUEST['archived'],
                '', 'jpress_archive' ),
            number_format_i18n( $_REQUEST['archived'] )
        );
        $ids = isset( $_REQUEST['ids']) ? $_REQUEST['ids'] : 0;
    }

    if ( isset( $_REQUEST['unset_archived'] ) ) {
        $message_unset_archived = sprintf(
            _n( 'L\'élément a été restauré : %2$s.',
                '%1$s éléments ont été restauré : %2$s.',
                $_REQUEST['unset_archived'],
                '', 'jpress_archive' ),
            number_format_i18n( $_REQUEST['unset_archived'] ),
            '<code>' . get_post_type( $_REQUEST['ids'] ) . '</code>'
        );
    }

    if ( isset( $_REQUEST['archived'] ) && (int) $_REQUEST['archived'] ) {
        add_settings_error(
            'archived_message',
            'archived',
            $message_archived,
            'updated'
        );
    }

    if ( isset( $_REQUEST['unset_archived'] ) && (int) $_REQUEST['unset_archived'] ) {
        add_settings_error(
            'unset_archived_message',
            'unset_archived',
            $message_unset_archived,
            'updated'
        );
    }
}

function jpress_change_post_status($id, $ps){
    global $wpdb;
    //compatibility with jcpt create pst table
    if(function_exists('jcpt_whois')){
        $post_type = jcpt_whois($id);
        $wpdb->query("UPDATE {$wpdb->prefix}{$post_type}s SET post_status='" . $ps ."' WHERE ID = {$id}");
    }else{
        $wpdb->query("UPDATE {$wpdb->prefix}posts SET post_status='" . $ps ."' WHERE ID = {$id}");
    }
}

add_filter('posts_where', 'jpress_archive_posts_where');
function jpress_archive_posts_where($sql){
    global $pagenow, $wpdb;
    if($pagenow== "edit.php" && strpos($sql, "post_status = 'archived'")){
        $sql = str_replace("OR {$wpdb->prefix}posts.post_status = 'archived'", ' ',$sql);
    }
    return $sql;
}

add_filter('display_post_states', 'jpress_archive_display_post_states',10,2);
function jpress_archive_display_post_states($post_states, $post){
    if($post->post_status == 'archived'){
        $post_states['archived'] = "Archive";
    }
    return $post_states;
}

add_action('admin_footer', 'jpress_archive_append_post_status_list');
function jpress_archive_append_post_status_list(){
    global $post, $pagenow;
    $complete = '';
    $label = '';
    if($pagenow == 'post.php' ||$pagenow == 'post-new.php'){
        if($post->post_status == 'archived'){
            $complete = ' selected="selected"';
            $label = '<span id="post-status-display"> Archivé</span>';
        }
        echo '
          <script>
          jQuery(document).ready(function(){
               jQuery("select#post_status").append(\'<option value="archived" '.$complete.'>Archivé</option>\');
               jQuery(".misc-pub-section label").append(\''.$label.'\');
          });
          </script>
          ';
    }
}