<?php $message = jpressep_encrypt_themes();?>
<?php if(!empty($message)):?>
    <div id="message" class="updated  below-h2">
        <p><?php echo $message;?></p>
    </div>
<?php endif;?>
<form method="post" action="options-general.php?page=encrypt-files&tab=themes">
    <h3>Encryptez les thèmes de votre choix. </h3>
    <label>Cochez les thèmes que vous voulez proteger dans la liste ci-dessous :</label>
    <ul>
        <?php
        global $wp_themes;
        $all_themes = wp_get_themes();
        foreach ( $all_themes as $path => $theme) :?>
            <li>
                <input id="<?php echo $path;?>" type="checkbox" name="themes[]" value="<?php echo $path;?>"> <label for="<?php echo $path;?>"><?php echo $theme->Name;?></label>
            </li>
        <?php endforeach;?>
    </ul>
    <p class="submit"><input type="submit" name="encrypt-themes" id="submit" class="button button-primary" value="Encrypt"></p>
</form>