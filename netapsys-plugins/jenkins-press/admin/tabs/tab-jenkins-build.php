<style>
    .form-table{
        border: 1px gray dotted;
    }
</style>
<?php
if(!empty($message)):?>
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
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
        <tr><td colspan="2"><h2>Antcall enable</h2></td></tr>
        <tr>
            <td colspan="2">
                <label for="antcall_pmd">Activer PMD Antcall</label> <input id="antcall_pmd" name="antcall_pmd" type="checkbox" value="1" <?php if (jp_secure_array_key('antcall_pmd',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label for="antcall_phpcpd">Activer CPD Antcall</label> <input id="antcall_phpcpd" name="antcall_phpcpd" type="checkbox" value="1" <?php if (jp_secure_array_key('antcall_phpcpd',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <label for="antcall_phpcs">Activer Checkstyle Antcall</label> <input id="antcall_phpcs" name="antcall_phpcs" type="checkbox" value="1" <?php if (jp_secure_array_key('antcall_phpcs',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
        <!--php files-->
        <tr>
            <td colspan="2">
                <label for="phpfiles_section">Activer php files section</label> <input id="phpfiles_section" name="phpfiles_section" type="checkbox" value="1" <?php if (jp_secure_array_key('phpfiles_section',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
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
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
        <!--pdepend-->
        <tr>
            <td colspan="2">
                <label for="pdepend_section">Activer pdepend section</label> <input id="pdepend_section" name="pdepend_section" type="checkbox" value="1" <?php if (jp_secure_array_key('pdepend_section',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
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
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
        <!--pmd-->
        <tr>
            <td colspan="2">
                <label for="pmd_section">Activer pmd section</label> <input id="pmd_section" name="pmd_section" type="checkbox" value="1" <?php if (jp_secure_array_key('pmd_section',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
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
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
        <!--php cpd-->
        <tr>
            <td colspan="2">
                <label for="phpcpd_section">Activer php cpd section</label> <input id="phpcpd_section" name="phpcpd_section" type="checkbox" value="1" <?php if (jp_secure_array_key('phpcpd_section',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
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
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
        <!--checkstyle-->
        <tr>
            <td colspan="2">
                <label for="checkstyle_section">Activer checkstyle section</label> <input id="checkstyle_section" name="checkstyle_section" type="checkbox" value="1" <?php if (jp_secure_array_key('checkstyle_section',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
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
        </tbody>
    </table>
    <table class="form-table">
        <tbody>
        <!--code browser-->
        <tr>
            <td colspan="2">
                <label for="cb_section">Activer code browser section</label> <input id="cb_section" name="cb_section" type="checkbox" value="1" <?php if (jp_secure_array_key('cb_section',$jenpress_options, '0')):?>checked<?php endif;?>/>
            </td>
        </tr>
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