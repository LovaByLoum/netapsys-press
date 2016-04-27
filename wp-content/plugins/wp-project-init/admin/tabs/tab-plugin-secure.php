<div class="wpi-notif" style="padding-left: 10px;">
    <p>
        Mettre en place les must-use plugins de sécurité. Fortement recommandé.
    </p>
    <a href="javascript:void(0);" class="wpi-button show wpi-show" style="display: inline-block;">En savoir plus<span class="wpi-button-arrow show"></span></a>
    <div class="wpi-notif-section wpi-hide">
        <p>
            <ul style="list-style: disc;">
                <li>Limitation de login infructueuse</li>
                <li>Suppression meta generator</li>
                <li>Désactivation de l'autocompletion du champ mot de passe</li>
                <li>Suppression du lien mot  de passe oublié</li>
                <li>Suppréssion identifiant auteur</li>
                <li>Captcha sur le login form</li>
            </ul>
        </p>
        <a href="javascript:void(0);" class="wpi-button hide" style="float:right;">Fermer<span class="wpi-button-arrow hide"></span></a>
    </div>

        </div>

<?php
if(!empty($message)):?>
    <div id="message" class="updated  below-h2">
        <p><?php echo $message;?></p>
    </div>
<?php endif;?>
<form class="wpi-form wpi-form<?php echo rand(1,12);?>" method="post" action="" enctype="multipart/form-data" style="min-height: 600px;">
  <table class="form-table">
    <tbody>
    <tr>
      <th scope="row">Login Captcha</th>
      <td>
        <?php WP_Project_Init_Admin::render_fields('checkbox','mu_captcha',false,'Activer le captcha sur le login form', 'checked');?>
      </td>
    </tr>
    <tr>
      <th scope="row">Login lock down</th>
      <td>
        <?php WP_Project_Init_Admin::render_fields('checkbox','mu_lock',false,'Activer le loginlock down sur le login form.<br>Bloque les Ips des utilisateurs ayant fait plusieurs tentatives de login infructueuses pendant un laps de temps configurable.' , 'checked');?>
      </td>
    </tr>
    <tr>
      <th scope="row">WP Secure</th>
      <td>
        <?php WP_Project_Init_Admin::render_fields('checkbox','mu_secure',false,'Activer divers scripts de sécurisation', 'checked');?>
      </td>
    </tr>
    </tbody>
  </table>
  <p class="submit"><input type="submit" name="<?php echo $current_tab;?>" id="submit" class="button button-primary" value="Activer"></p>
</form>