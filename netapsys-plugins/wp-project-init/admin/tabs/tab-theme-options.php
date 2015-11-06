<div class="wpi-notif">
    <p><strong>ensuite La première étape consiste tout d'abord à generer un thème vide avec lequel vous allez demarrer votre developpement.</strong><br>
       Par thème vide, on sous-entend que le thème generé possedera les templates standards et natifs de WordPress tout en enlevant les fonctions inutiles et respectant l'arborescence selon le BestPractice et mettre en place divers outils de developpement, librairies frequement utilisés et les mésures de sécurité du site.</p>
</div>

<?php
if(!empty($message)):?>
    <div id="message" class="updated  below-h2">
        <p><?php echo $message;?></p>
    </div>
<?php endif;?>
<form class="wpi-form wpi-form<?php echo rand(1,12);?>" method="post" action="" enctype="multipart/form-data">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Nom de votre theme</th>
                <td>
                	<?php WP_Project_Init_Admin::render_fields('text','theme_name','Le nom qui apparaitra dans la liste des thèmes');?>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit"><input type="submit" name="<?php echo $current_tab;?>" id="submit" class="button button-primary" value="Generer"></p>
</form>