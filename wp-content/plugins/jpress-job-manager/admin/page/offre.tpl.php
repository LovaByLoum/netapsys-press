<?php
global $jpress_jm_offre_fields;
$options = get_option( JM_OPTIONS );
?>
<div class="wrap">
    <form method="post" action="">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">Champs basiques actifs</th>
                <td>
                    <?php foreach ( $jpress_jm_offre_fields as $field):?>
                        <fieldset>
                            <label for="fields-<?php echo $field['metakey'];?>">
                                <input name="fields-offre[]" type="checkbox" id="fields-<?php echo $field['metakey'];?>" value="<?php echo $field['metakey'];?>" <?php if( jpress_jm_is_in_options( $field['metakey'], 'fields-offre' ) ) :?>checked<?php endif;?>>
                                <?php echo $field['label'];?>
                            </label>
                        </fieldset>
                    <?php endforeach;?>
                </td>
            </tr>

            <tr>
                <th scope="row">Colonnes actifs en BO</th>
                <td>
                    <?php foreach ( $jpress_jm_offre_fields as $field):
                        if($field['enable'] == 0) continue; ?>
                        <fieldset>
                            <label for="column-<?php echo $field['metakey'];?>">
                                <input name="column-offre[]" type="checkbox" id="column-<?php echo $field['metakey'];?>" value="<?php echo $field['metakey'];?>" <?php if( jpress_jm_is_in_options( $field['metakey'], 'column-offre' ) ) :?>checked<?php endif;?>>
                                <?php echo $field['label'];?>
                            </label>
                        </fieldset>
                    <?php endforeach;?>
                </td>
            </tr>

            </tbody>
        </table>
        <p class="submit"><input type="submit" name="jm_submit_offre" id="submit" class="button button-primary" value="Enregistrer"></p>
    </form>
</div>