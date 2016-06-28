<?php
/**
 * template de gestion de droits
 */
global $jpress_jm_capabilities;
$roles = get_editable_roles();
$options = get_option( JM_DROITS );
?>
<div class="wrap">
    <form method="post" action="">
        <table class="form-table">
            <tbody>
            <tr><h3>Gestion des privilèges</h3></tr>
            <tr>
                <td>
                    <table class="form-table bordered">
                        <thead>
                            <th><br>Privilèges</th>
                            <?php foreach ( $roles as $name => $role):?>
                                <th>
                                    <?php echo $role['name'];?>
                                    <br>
                                    <br>
                                    <input class="select-deselect-all" type="checkbox" value=""/></th>
                            <?php endforeach;?>
                        </thead>
                        <tbody>
                            <?php
                            $post_types = array(
                                JM_POSTTYPE_SOCIETE,
                                JM_POSTTYPE_OFFRE,
                                JM_POSTTYPE_CANDIDATURE
                            );
                            foreach ( $post_types as $pt):?>
                                <tr class="light">
                                    <th colspan="10"><?php echo ucfirst( $pt ); ?></th>
                                </tr>
                                <?php foreach ($jpress_jm_capabilities as $cap) :?>
                                    <tr>
                                        <th><?php
                                            $current_cap = sprintf($cap, $pt);
                                            echo $current_cap;
                                            ?>
                                        </th>
                                        <?php foreach ( $roles as $name => $role):
                                            $role_object = get_role( $name );
                                            ?>
                                            <td><input type="checkbox" name="<?php echo $name;?>[]" value="<?php echo $current_cap;?>" <?php if ( $role_object->has_cap( $current_cap ) ) : ?>checked<?php endif;?> ></td>
                                        <?php endforeach;?>
                                    </tr>
                                <?php endforeach;
                            endforeach;?>
                        </tbody>
                    </table>
                </td>
            </tr>

            </tbody>
        </table>
        <p class="submit"><input type="submit" name="jm_submit_capabilities" id="submit" class="button button-primary" value="Enregistrer"></p>
    </form>
</div>