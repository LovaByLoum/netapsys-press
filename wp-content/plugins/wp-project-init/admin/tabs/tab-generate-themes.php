<div class="wpi-notif">
    <p><strong>La première étape consiste tout d'abord à generer un thème vide avec lequel vous allez demarrer votre developpement.</strong></p>
    <a href="javascript:void(0);" class="wpi-button show wpi-show" style="display: inline-block;">En savoir plus sur le contenu du thème generé<span class="wpi-button-arrow show"></span></a>
    <div class="wpi-notif-section wpi-hide">
      <p>Par thème vide, on sous-entend que le thème generé possedera les templates standards et natifs de WordPress tout en enlevant les fonctions inutiles et respectant l'arborescence selon le BestPractice et mettre en place divers outils de developpement, librairies frequement utilisés et les mésures de sécurité du site. Le thème est basé sur le thème standard de WordPress.</p>

      <p>Votre theme comprendra nativement aussi : </p>
      <ul style="list-style: disc;margin-left: 20px;">
        <li>Un gestionnaire de minification js/css</li>
        <li>Un generateur de classes de service</li>
        <li>Des outils de débogage</li>
        <li>Un gestionnaire de configuration multi-environnement</li>

      </ul>

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
    <h3>Informations générales :</h3>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Nom de votre theme</th>
                <td>
                	<?php WP_Project_Init_Admin::render_fields('text','theme_name',true,'Le nom qui apparaitra dans la liste des thèmes');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Nom machine de votre theme</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('text','theme_slug',true,'Ne devra contenir que des caractères alphanumériques et le caractère _ ');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Déscription</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('textarea','theme_desc',true,'');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Tags</th>
                <td>
                	<?php WP_Project_Init_Admin::render_fields('textarea','theme_tags',false,'Séparez les tags par des virgules');?>                   </td>
            </tr>
            <tr>
                <th scope="row">Auteur</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('text','theme_author',true,'');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Site de l'auteur</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('text','theme_author_site',false,'');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Version</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('text','theme_version',false,'','1.0');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Screenshot</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('file','theme_screenshot',true,'Capture d\'écran du thème, prendre la home du site (doit être au format PNG)');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Logo admin</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('file','theme_logo_admin',true,'Le logo qui apparaitra dans le formulaire d\'authentification (doit être au format PNG)');?>
                </td>
            </tr>
            <tr>
                <th scope="row">Préfixe des fonctions</th>
                <td>
                    <?php WP_Project_Init_Admin::render_fields('text','theme_prefix',true,'Ne devra contenir que des caractères alphanumériques');?>
                </td>
            </tr>
        </tbody>
    </table>

    <fieldset class="bordered">
        <h3>Configuration des templates secondaires :</h3>
        <label>Activer ici les templates facultatifs dont vous n'aurez pas forcement besoin selon votre projet. </label>
        <a target="__blank" href="<?php echo WPI_URL;?>/admin/images/wp-template-hierarchy.jpg"><em>En savoir plus</em></a>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">archive.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_archive',false,'Activer le template, si votre site a besoin d\'une page d\'archive');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">author.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_author',false,'Activer le template, si votre site a besoin d\'une page d\'auteur');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">attachment.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_attachment',false,'Activer le template, si les médias ont besoin d\'être afficher dans une page/template');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">category.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_category',false,'Activer le template, si votre site a besoin d\'une page de classification par catégorie');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">comments.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_comment',false,'Activer le template, uniquement si votre site autorise les commentaires');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">taxonomy.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_taxonomy',false,'Activer le template, si votre site a besoin d\'une page pour les taxonomies personnalisés');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">date.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_date',false,'Activer le template, si vous avez besoin d\'une page d\'archive par date');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">search.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_search',false,'Activer le template, uniquement si votre site utilise le moteur de recherche');?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">tag.php</th>
                    <td>
                        <?php WP_Project_Init_Admin::render_fields('checkbox','theme_tpl_tag',false,'Activer le template, si votre site a besoin d\'une page de classification par mot clé');?>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    <br>
    <br>
    <fieldset class="bordered">
      <h3>Options du thème :</h3>
      <label>Activer ici le theme options si votre thème a besoin d'une interface personnalisée pour gérer les options du thème. </label>
      <br>
      <br>
      <?php WP_Project_Init_Admin::render_fields('checkbox', 'theme_options',false,'Activer le theme option panel', 'checked' );?>
    </fieldset>

    <?php WP_Project_Init_Admin::hidden_fields();?>
    <p class="submit"><input type="submit" name="<?php echo $current_tab;?>" id="submit" class="button button-primary" value="Generer"></p>
</form>