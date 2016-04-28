<?php
include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; //for plugins_api..
?>

<div class="wpi-notif" style="padding-left: 10px;">
    <p>
        Ensuite installer ici les plugins de base pour votre projet. Vous pouvez créer un profil d'installation.
    </p>
</div>

<?php
if(!empty($message)):?>
    <div id="message" class="updated  below-h2">
        <p><?php echo $message;?></p>
    </div>
<?php endif;?>
<form class="wpi-form  wpi-form<?php echo rand(1,12);?>" method="post" action="" enctype="multipart/form-data">
    <?php global $wppi_plugins;
    $type_profile_choice = isset($_REQUEST['type_profile']) ? $_REQUEST['type_profile'] : 1;
    $selected_profile = isset($_REQUEST['profile']) ? $_REQUEST['profile'] : '';
    $plugins_to_active = array();
    if ( !empty($selected_profile) ){
      $plugins_to_active = WP_Plugin_Manager::get_profile($selected_profile);
    }

    ?>

    <table class="form-table">
      <tbody>
      <tr>
        <td colspan="2">
          <label><input  type="radio" value="1" name="type_profil" <?php if ( $type_profile_choice == 1):?>checked<?php endif;?>> Nouveau profil</label><br>
          <label><input  type="radio" value="2" name="type_profil" <?php if ( $type_profile_choice == 2):?>checked<?php endif;?>> Charger un profil existant</label>
        </td>
      </tr>
      <tr id="type_profil2" class="type_profil_choice" <?php if ( $type_profile_choice != 2):?>style="display:none;"<?php endif;?>>
        <th scope="row">Charger le profil</th>
        <td>
          <?php
          $nodes = glob(WPI_PROFILES_PATH . '*.profile');
          ?>
          <select id="loadprofile" name="profile_file">
            <option>Sélectionner</option>
            <?php foreach ( $nodes as $node ):
              $pi = pathinfo($node);
              ?>
            <option value="<?php echo $pi['basename'];?>" <?php if( $selected_profile == $pi['basename'] ):?>selected<?php endif;?>><?php echo $pi['basename'];?></option>
            <?php endforeach;?>
          </select>
        </td>
      </tr>
      <tr id="type_profil1" class="type_profil_choice" <?php if ( $type_profile_choice != 1):?>style="display:none;"<?php endif;?>>
        <th scope="row">Nom du profil</th>
        <td>
          <input class="regular-text " type="text" value="" name="profile_name"><br>
          <em>Enregistrer la sélection dans ce profil</em>
        </td>
      </tr>
      </tbody>
    </table>

    <?php foreach ( $wppi_plugins as $categorie => $plugins): ?>
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
              <?php
              WP_Project_Init_Admin::render_fields('checkbox', 'plugin[' . $plugin . ']', false, $name, (in_array($plugin, $plugins_to_active) ? 'checked' : '' ) );
              ?>
              <em><?php echo $version;?></em>
            </td>
            <td>
              <?php
              if ( !is_dir( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin) ){
                $action = 'install-plugin';
                $slug = $plugin;
                $url = wp_nonce_url(
                  add_query_arg(
                    array(
                      'action' => $action,
                      'plugin' => $slug
                    ),
                    admin_url( 'update.php' )
                  ),
                  $action.'_'.$slug
                );
                echo '<a href="' . $url . '">Installer maintenant</a><br>';
                if ( !empty($download)){
                  echo '<a target="_blank" href="' . $download . '">Télécharger la dernière version</a><br>';
                }
              }else{
                $plugin_path = WP_Project_Init_Admin::get_plugin_path($plugin);
                if ( is_plugin_active($plugin_path) ){
                  echo 'Déjà actif<br>';
                }else{
                  $active_url = wp_nonce_url(self_admin_url('plugins.php?action=activate&plugin='.$plugin_path), 'activate-plugin_'.$plugin_path);
                  echo '<a href="' . $active_url . '">Activer</a><br>';
                }

              }
              $view_link = '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin . '&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox" title="En savoir plus">En savoir plus</a>';
              echo $view_link;
              ?>
            </td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    <?php endforeach;?>

    <p class="submit"><input type="submit" name="<?php echo $current_tab;?>" id="submit" class="button button-primary" value="Installer"></p>
</form>