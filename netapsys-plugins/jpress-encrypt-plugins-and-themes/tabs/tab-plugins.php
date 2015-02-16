<?php $message = jpressep_encrypt_plugins();?>
<?php if(!empty($message)):?>
    <div id="message" class="updated  below-h2">
        <p><?php echo $message;?></p>
    </div>
<?php endif;?>
<form method="post" action="options-general.php?page=encrypt-files&tab=plugins">
    <h3>Encryptez les plugins de votre choix. </h3>
    <label>Cochez les plugins que vous voulez proteger dans la liste ci-dessous :</label>
    <ul>
        <?php $all_plugins = get_plugins();
        foreach ( $all_plugins as $path => $plug) :?>
            <li>
                <input id="<?php echo $path;?>" type="checkbox" name="plugs[]" value="<?php echo $path;?>"> <label for="<?php echo $path;?>"><?php echo $plug['Name'];?></label>
            </li>
        <?php endforeach;?>
    </ul>
    <p class="submit"><input type="submit" name="encrypt-plugins" id="submit" class="button button-primary" value="Encrypt"></p>
</form>