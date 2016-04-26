<div class="wpi-notif" style="padding-left: 10px;">
    <p>
        Ensuite configurer ici les features à integrer dans votre themes.
    </p>
    <a href="javascript:void(0);" class="wpi-button show wpi-show" style="display: inline-block;">En savoir plus<span class="wpi-button-arrow show"></span></a>
    <div class="wpi-notif-section wpi-hide">
        <p>
            <ul style="list-style: disc;">
                <li>Options générales</li>
                <li>Gestion de font</li>
                <li>Gestion de couleur</li>
                <li>Gestion des réseaux sociaux</li>
                <li>Visual composer</li>
            </ul>
            <br>
            Votre theme comprendra nativement aussi : <br>
            <ul style="list-style: disc;">
                <li>Un gestionnaire de minification js/css</li>
                <li>Un generateur de classes de service</li>
                <li>Des outils de débogage</li>
                <li>Un gestionnaire de configuration multi-environnement</li>

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