<?php

$posts_types = get_post_types();
wp_enqueue_script( 'accordion' );

if(isset($_POST["save"])){
  unset($_POST["save"]);
  update_option('wp_export_options',$_POST);
}

$wp_export_options = get_option('wp_export_options');
?>
<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2>Admin Column Search</h2>
    <br><br>
    <form method="post" action="" id="acs-cpt">
      <div id="side-sortables" class="accordion-container">
        <ul class="outer-border">
        <?php
          foreach($posts_types as $pt) :
          $posttype = get_post_type_object($pt);
        ?>
          <li class="control-section accordion-section top">
            <h3 class="accordion-section-title hndle" tabindex="0"><?php echo $posttype->labels->name ;?></h3>
            <div class="accordion-section-content " style="display: none;">
              <div class="inside">
                <input type="checkbox" name="enable[]" value="<?php echo $pt;?>" <?php if(isset($wp_export_options['enable']) && in_array($pt,$wp_export_options['enable'])){echo 'checked';}?>>
                <label>Ajout bouton d'export</label>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
        </ul>
      </div>
      <br>
      <input type="submit" class="button-primary" name="save" value="Enregistrer">
    </form>
</div>
