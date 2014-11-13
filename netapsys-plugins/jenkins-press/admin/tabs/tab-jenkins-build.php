<?php if(!empty($message)):?>
    <div id="message" class="updated  below-h2">
        <p><?php echo $message;?></p>
    </div>
<?php endif;?>
<form method="post" action="options-general.php?page=jenkins-press&tab=jenkins-build" style="background:url(<?php echo plugins_url('jenkins-press/images/jenkins.png'); ?>);">

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">Project name</th>
            <td>
                <input name="project_name" type="text" value="<?php echo jp_secure_array_key('project_name',$jenpress_options, sanitize_title( get_bloginfo('name' )));?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">Base directory</th>
            <td>
                <input name="basedir" type="text" value="<?php echo jp_secure_array_key('basedir',$jenpress_options, '.');?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">Script suffix</th>
            <td>
                <input name="scriptsuffix" type="text" value="<?php echo jp_secure_array_key('scriptsuffix',$jenpress_options, '.bat');?>" class="regular-text">
            </td>
        </tr>
        <!--php files-->
        <tr>
            <th scope="row">php-files Include files</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="includefiles"><?php echo jp_secure_array_key('includefiles',$jenpress_options, "**/*.php");?></textarea>
                <br/><em>Une valeur par ligne</em>
            </td>
        </tr>
        <tr>
            <th scope="row">php-files Exclude files</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="excludefiles"><?php echo jp_secure_array_key('excludefiles',$jenpress_options, "wp-admin/**\nwp-config/**\nwp-includes/**\nwp-content/languages/**\nwp-content/mu-plugins/**");?></textarea>
                <br/><em>Une valeur par ligne</em>
            </td>
        </tr>
        <!--pdepend-->
        <tr>
            <th scope="row">pdepend path to ignore</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="pdependignore"><?php echo jp_secure_array_key('pdependignore',$jenpress_options, "wp-includes\nwp-admin\nwp-content/themes\nwp-content/mu-plugins");?></textarea>
                <br/><em>Une valeur par ligne</em>
            </td>
        </tr>
        <tr>
            <th scope="row">pdepend path to include</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="pdependpath"><?php echo jp_secure_array_key('pdependpath',$jenpress_options, "wp-content/plugins/custom");?></textarea>
                <br/><em>Une valeur par ligne.<br>Mettre dans ce champs les chemins à inclure selon le projet.</em>
            </td>
        </tr>
        <!--pmd-->
        <tr>
            <th scope="row">pmd path to exclude</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="pmdignore"><?php echo jp_secure_array_key('pmdignore',$jenpress_options, "wp-includes\nwp-admin\nwp-content/themes\nwp-content/mu-plugins");?></textarea>
                <br/><em>Une valeur par ligne</em>
            </td>
        </tr>
        <tr>
            <th scope="row">pmd path to include</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="pmdpath"><?php echo jp_secure_array_key('pmdpath',$jenpress_options, "wp-content/plugins/custom");?></textarea>
                <br/><em>Une valeur par ligne.<br>Mettre dans ce champs les chemins à inclure selon le projet.</em>
            </td>
        </tr>
        <!--php cpd-->
        <tr>
            <th scope="row">php cpd path to exclude</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="phpcpdignore"><?php echo jp_secure_array_key('phpcpdignore',$jenpress_options, "wp-includes\nwp-admin\nwp-content/themes\nwp-content/mu-plugins");?></textarea>
                <br/><em>Une valeur par ligne</em>
            </td>
        </tr>
        <tr>
            <th scope="row">php cpd path to include</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="phpcpdpath"><?php echo jp_secure_array_key('phpcpdpath',$jenpress_options, "wp-content/plugins/custom");?></textarea>
                <br/><em>Une valeur par ligne.<br>Mettre dans ce champs les chemins à inclure selon le projet.</em>
            </td>
        </tr>
        <!--checkstyle-->
        <tr>
            <th scope="row">checkstyle path to exclude</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="checkstyleignore"><?php echo jp_secure_array_key('checkstyleignore',$jenpress_options, "wp-includes\nwp-admin\nwp-content/themes\nwp-content/mu-plugins");?></textarea>
                <br/><em>Une valeur par ligne</em>
            </td>
        </tr>
        <tr>
            <th scope="row">checkstyle path to include</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="checkstylepath"><?php echo jp_secure_array_key('checkstylepath',$jenpress_options, "wp-content/plugins/custom");?></textarea>
                <br/><em>Une valeur par ligne.<br>Mettre dans ce champs les chemins à inclure selon le projet.</em>
            </td>
        </tr>
        <!--code browser-->
        <tr>
            <th scope="row">code browser path to exclude</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="cbignore"><?php echo jp_secure_array_key('cbignore',$jenpress_options, "wp-includes\nwp-admin\nwp-content/themes\nwp-content/mu-plugins");?></textarea>
                <br/><em>Une valeur par ligne</em>
            </td>
        </tr>
        <tr>
            <th scope="row">code browser path to include</th>
            <td>
                <textarea style="width: 352px;height: 117px;" name="cbpath"><?php echo jp_secure_array_key('cbpath',$jenpress_options, "wp-content/plugins/custom");?></textarea>
                <br/><em>Une valeur par ligne.<br>Mettre dans ce champs les chemins à inclure selon le projet.</em>
            </td>
        </tr>

        </tbody>
    </table>
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Enregistrer les modifications"></p>
</form>