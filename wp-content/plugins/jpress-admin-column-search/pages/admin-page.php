<?php
$posts_types = get_post_types();
wp_enqueue_script( 'accordion' );

if( isset( $_POST["acssubmit"] ) ) {
  unset($_POST["acssubmit"]);
  update_option( 'jpress_acs_options', $_POST );
}

$acs_options = get_option( 'jpress_acs_options' );
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2>Admin Column Search</h2>
    <br><br>
    <form method="post" action="" id="acs-cpt">
      <div id="side-sortables" class="accordion-container">
        <ul class="outer-border">
        <?php
        global $wpdb;
        $taxonomies = get_taxonomies();
        foreach ( $posts_types as $pt ):
          $posttype = get_post_type_object( $pt );
          $columns = get_column_headers( "edit-{$pt}" );
          $table = _get_list_table( 'WP_Posts_List_Table', array( 'screen' => $pt ) );
          $columns = array_filter( array_merge( $columns, $table->get_columns() ) );
          unset( $columns['cb'] );
          unset( $columns['date'] );
          unset( $columns['comments'] );

          /*$metakey = $wpdb->get_col(
            "SELECT meta_key FROM {$wpdb->prefix}postmeta
            WHERE post_id = (
              SELECT ID FROM {$wpdb->prefix}posts  AS p
              WHERE p.post_type = '" . $pt . "' AND p.post_status = 'publish'
              ORDER BY ID DESC LIMIT 1
            ) "
          );*/

          $metakey = $wpdb->get_col(
            "SELECT DISTINCT pm.meta_key FROM {$wpdb->prefix}postmeta  AS pm
            INNER JOIN {$wpdb->prefix}posts AS p ON p.ID = pm.post_id
            WHERE p.post_type = '" . $pt . "' AND p.post_status = 'publish'
            ORDER BY pm.meta_key"
          );
          ?>
            <li class="control-section accordion-section top">
              <h3 class="accordion-section-title hndle" tabindex="0"><?php echo $posttype->labels->name ;?></h3>
              <div class="accordion-section-content " style="display: none;">
                <div class="inside">
                  <label><input type="checkbox" name="enable[]" value="<?php echo $pt;?>" <?php if ( isset( $acs_options['enable'] ) && in_array( $pt, $acs_options['enable'] ) ) { echo 'checked'; }?>>  <?php echo __("Enable Admin Column Search", "jpress-acs" );?></label><br><br>
                   <table id="taxo_table" class="widefat">
                    <thead>
                      <tr>
                        <th><?php echo __("Enable", "jpress-acs" );?></th>
                        <th><?php echo __("Column", "jpress-acs" );?></th>
                        <th><?php echo __("Post Data", "jpress-acs" );?></th>
                        <th><?php echo __("Taxonomy", "jpress-acs" );?></th>
                        <th><?php echo __("Post Meta Key", "jpress-acs" );?></th>
                      </tr>
                    </thead>

                    <tfoot>
                      <tr>
                        <th><?php echo __("Enable", "jpress-acs" );?></th>
                        <th><?php echo __("Column", "jpress-acs" );?></th>
                        <th><?php echo __("Post Data", "jpress-acs" );?></th>
                        <th><?php echo __("Taxonomy", "jpress-acs" );?></th>
                        <th><?php echo __("Post Meta Key", "jpress-acs" );?></th>
                      </tr>
                    </tfoot>

                    <tbody id="sortable" class="taxbody ui-sortable">
                      <?php foreach ( $columns as $k => $column) : ?>
                      <tr>
                        <td>
                          <input type="checkbox" name="colonne[<?php echo $pt;?>][<?php echo $k;?>]" value="1" <?php if( isset( $acs_options['colonne'][$pt] ) && $acs_options['colonne'][$pt][$k] == 1 ){ echo 'checked'; } ?>/>
                        </td>
                        <td>
                          <label><?php echo $column;?></label>
                        </td>
                        <td>
                          <select name="postdata[<?php echo $pt;?>][<?php echo $k;?>]">
                          	<option value=""><?php echo __("None", "jpress-acs" );?></option>
                            <?php
                            //compatibilité jcpt create post table
                            global $wpdb;
                            $fields = $wpdb->get_results( "SHOW COLUMNS FROM {$wpdb->prefix}{$pt}s" );
                            if( ! $fields ) {
                                $fields = $wpdb->get_results( "SHOW COLUMNS FROM {$wpdb->prefix}posts" );
                            }
                            if( $fields ) {
                              foreach ( $fields as $field ) :?>
                                <option value="<?php echo $field->Field;?>" <?php if ( isset( $acs_options['postdata'][$pt] ) && $acs_options['postdata'][$pt][$k] == $field->Field ) { echo 'selected'; } ?>><?php echo $field->Field;?></option>
                              <?php endforeach;
                            }
                            ?>
                          </select>
                        <label><input <?php if ( isset( $acs_options['postdata_field'][$pt] ) && $acs_options['postdata_field'][$pt][$k]  == 'text' ) { echo 'checked'; } elseif ( ! isset( $acs_options['postdata_field'][$pt][$k] ) ) { echo 'checked'; } ?> type="radio" name="postdata_field[<?php echo $pt;?>][<?php echo $k;?>]" value="text"/><?php echo __("Text", "jpress-acs" );?></label>
                        <label><input <?php if ( isset( $acs_options['postdata_field'][$pt] ) && $acs_options['postdata_field'][$pt][$k]  == 'select' ){ echo 'checked'; } ?> type="radio" name="postdata_field[<?php echo $pt;?>][<?php echo $k;?>]" value="select"/><?php echo __("Select", "jpress-acs" );?></label>
                        </td>
                        <td>
                          <select name="tax[<?php echo $pt;?>][<?php echo $k;?>]">
                          	<option value=""><?php echo __("None", "jpress-acs" );?></option>
                            <?php foreach( $taxonomies as $tx ) : ?>
                            <option value="<?php echo $tx;?>" <?php if ( isset( $acs_options['tax'][$pt] ) && $acs_options['tax'][$pt][$k]  == $tx ) { echo 'selected'; } ?>><?php echo $tx;?></option>
                            <?php endforeach;?>
                          </select>
                        </td>
                        <td>
                          <select name="meta[<?php echo $pt;?>][<?php echo $k;?>]">
                          	<option value=""><?php echo __("None", "jpress-acs" );?></option>
                            <?php foreach ( $metakey as $mt ) :
                              /*if(preg_match('/^_/', $mt))
                                continue;*/
                              ?>
                            <option value="<?php echo $mt;?>" <?php if ( isset( $acs_options['meta'][$pt] ) && $acs_options['meta'][$pt][$k] == $mt ) { echo 'selected'; } ?>><?php echo $mt;?></option>
                            <?php endforeach;?>
                          </select>
                          <label><input <?php if ( isset ( $acs_options['meta_field'][$pt] ) && $acs_options['meta_field'][$pt][$k] == 'text' ) { echo 'checked'; } elseif ( ! isset( $acs_options['meta_field'][$pt][$k] ) ) { echo 'checked'; } ?> type="radio" name="meta_field[<?php echo $pt;?>][<?php echo $k;?>]" value="text"/><?php echo __("Text", "jpress-acs" );?></label>
                          <label><input <?php if ( isset ( $acs_options['meta_field'][$pt] ) && $acs_options['meta_field'][$pt][$k] == 'select' ) { echo 'checked'; } ?> type="radio" name="meta_field[<?php echo $pt;?>][<?php echo $k;?>]" value="select"/><?php echo __("Select", "jpress-acs" );?></label>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>

                </div>
              </div>
            </li>
        <?php endforeach; ?>
        </ul>
      </div>
      <br>
      <input type="submit" class="button-primary" name="acssubmit" value="<?php echo __("Save", "jpress-acs" );?>">
    </form>
</div>