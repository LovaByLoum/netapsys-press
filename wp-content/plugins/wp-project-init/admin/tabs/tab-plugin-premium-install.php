<?php
include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; //for plugins_api..
?>

<div class="wpi-notif" style="padding-left: 10px;">
    <p>
        Activer les plugins payantes pour votre projet si besoin.
    </p>
</div>

<?php
if(!empty($message)):?>
    <div id="message" class="updated  below-h2">
        <p><?php echo $message;?></p>
    </div>
<?php endif;?>
<form class="wpi-form  wpi-form<?php echo rand(1,12);?>" method="post" action="" enctype="multipart/form-data">
    <?php global $wppi_plugins_premium;
    $plugins_to_active = array();
    ?>

    <?php foreach ( $wppi_plugins_premium as $categorie => $plugins): ?>
      <table class="form-table oddeven">
        <tbody>
          <tr class="head">
            <th colspan="2" scope="row"><?php echo $categorie;?></th>
          </tr>
          <?php foreach ( $plugins as $plugin ):
            //check api
            /*$api = plugins_api('plugin_information', array('slug' => $plugin, 'fields' => array('sections' => false) ) );
            $name = $api->name;
            $version = 'Version ' . $api->version;
            $download = $api->download_link;
            if ( is_wp_error($api) ) continue;*/

            $name = $plugin;
            $version = '';
            $download = '';

            ?>
          <tr>
            <td style="width:70%">
              <?php echo $name;?>
              <em><?php echo $version;?></em>
            </td>
            <td>
              <?php
              if ( !is_dir( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin) ){
                $action = 'wppi-install-premium-plugin';
                $slug = $plugin;
                $url = wp_nonce_url(
                  add_query_arg(
                    array(
                      'action' => $action,
                      'plugin' => $slug
                    ),
                    admin_url( 'admin.php' )
                  ),
                  $action.'_'.$slug
                );
                echo '<a href="' . $url . '">Installer maintenant</a><br>';
                if ( !empty($download)){
                  echo '<a target="_blank" href="' . $download . '">Télécharger la dernière version</a>';
                }
              }else{
                $plugin_path = WP_Project_Init_Admin::get_plugin_path($plugin);
                if ( is_plugin_active($plugin_path) ){
                  echo 'Déjà actif';
                }else{
                  $active_url = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin='.$plugin_path), 'activate-plugin_'.$plugin_path);
                  echo '<a href="' . $active_url . '">Activer</a>';
                }

              }
              ?>


            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    <?php endforeach;?>

    <!--p class="submit"><input type="submit" name="<?php echo $current_tab;?>" id="submit" class="button button-primary" value="Installer"></p-->
</form>