<?php
class SDocument{

  public static function admin_init(){
    add_filter('mce_external_plugins', array('SDocument','addScriptTinymce'));
    add_filter('mce_buttons', array('SDocument','addButtonToRegister'));
  }

  public static function addButtonToRegister($buttons){
    array_push($buttons, "|", "document");

    return $buttons;
  }

  public static function formulaireDocument(){
    $type_documents = CInfos_reglementee::get_published_term( TAX_DOCUMENT, array('hide_empty' => false, 'parent'=>0) );
    ob_start();
    ?>
    <style type="text/css">
       .wrapper-field {
         margin-top: 10px;
       }
       fieldset {
         border:1px solid #000000;
         padding: 5px;
       }
       .wrapper-field span.select-container.views{
         display: block;
       }
       .wrapper-field span.select-container{
         display: none;
       }
    </style>
    <script type="text/javascript">
      jQuery('.type-affichage').click(function(){
        jQuery('.type-affichage').removeClass('checked');
        jQuery('.type-affichage').removeAttr('checked');
        jQuery(this).addClass('checked');
        jQuery(this).attr('checked','checked');
      });
      jQuery(document).ready(function(){
          jQuery("select#type-documents").change(function() {
            var type_doc = jQuery('option:selected', this).val();
            jQuery('.select-container').removeClass('views');
            if (type_doc != 0){
              jQuery('label.doc_id').html('Les documents :');
              jQuery(".field_"+type_doc).addClass('views');
            }else{
              jQuery('label.doc_id').html('Veuillez Séléctionner le type de document');
            }
          });
      });
    </script>
    <h2>Inserer des documents téléchargeables</h2>
    <div class="wrapper-field">
      <fieldset>
        <legend>Type d'affichage</legend>
        <input class="type-affichage checked" type="radio" name="type-affichage" value="1" checked="checked" id="type1">
        <label for="type1">Type 1</label>
        <img src="<?php echo get_template_directory_uri();?>/inc/shortcode/js/images/download-type1.png" width="200">
        <br><br>
        <input class="type-affichage" type="radio" name="type-affichage" value="2" id="type2">
        <label for="type2">Type 2</label>
        <img src="<?php echo get_template_directory_uri();?>/inc/shortcode/js/images/download-type2.png" width="200" >
      </fieldset>
    </div>
    <div class="wrapper-field">
      <fieldset>
        <legend>Veuillez séléctionner les documents à inserer dans la liste ci-dessous. <em>(Vous pouvez séléctionner plusieurs documents)</em></legend>
        <label>Type de document :</label><br />
        <select  id="type-documents" >
          <option value="0"><?php echo __('Sélectionnez', 'wordpress-form-manager');?></option>
          <?php foreach ($type_documents as $type_document) : ?>
          <option value="<?php echo $type_document->term_id;?>" <?php if ( $default['document_data']['term_id'] == $type_document->term_id):?>selected<?php endif;?>><?php echo $type_document->name;?></option>
          <?php endforeach;?>
        </select><br>
        <label class="doc_id">Veuillez Séléctionner le type de document</label><br />
          <?php foreach ($type_documents as $type_document):
                  $documents = CInfos_reglementee::getBy(NULL, $type_document->term_id, FALSE);?>
          <span class="field_<?php echo $type_document->term_id;?> select-container">
                <select id="doc_id" name="doc_id" multiple="multiple" >
                  <?php foreach($documents as $row) : ?>
                    <option value="<?php echo $row->id; ?>" class="option-<?php echo $type_document->term_id;?>"><?php echo $row->titre; ?></option>
                  <?php endforeach; ?>
                </select>
          </span>
          <?php endforeach;?>
      </fieldset>
    </div>
    <input id="submit-doc"  type="submit" value="Inserer les documents">
    <?php
    $content = ob_get_contents();
    ob_clean();

    echo $content;
    die();
  }

  public static function render_shortcode($attr){
    $list_id = explode(',',$attr['id']);
      $type = $attr["type"];
    ob_start();
    ?>
    <?php
    if($type == 1):
      if(count($list_id) > 0) : ?>
       <ul class="down-publication list-height">
          <?php foreach($list_id as $id) : $doc = CInfos_reglementee::getById($id); ?>
            <li class="assemble-generale">
                <span><?php echo wp_limite_text($doc->titre,100); ?></span>
                <?php if($doc->id_doc) : ?>
                <a href="<?php echo get_option('siteurl') . '/download.php?file=' . $doc->id_doc;?>" title="<?php echo esc_attr($doc->titre); ?>" class="btn-down">
                  <img src="<?php echo get_template_directory_uri()?>/images/design/download.png" alt=""/><?php echo __('Télécharger','ldc'); ?>
                  <small><?php echo $doc->file_size; ?></small>
                </a>
                <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif;
     elseif($type == 2):
         if(count($list_id) > 0) : ?>
             <ul class="down-publication down-kitMedia list-height">
                 <?php foreach($list_id as $id) : $doc = CInfos_reglementee::getById($id); ?>
                     <li>
                         <span><?php echo wp_limite_text($doc->titre,30); ?></span>
                         <?php if($doc->id_doc) : ?>
                             <a href="<?php echo get_option('siteurl') . '/download.php?file=' . $doc->id_doc;?>" title="<?php echo esc_attr($doc->titre); ?>" class="btn-down">
                                 <img src="<?php echo get_template_directory_uri()?>/images/design/download.png" alt=""/><?php echo __('Télécharger','ldc'); ?>
                                 <small><?php echo $doc->file_size; ?></small>
                             </a>
                         <?php endif; ?>
                     </li>
                 <?php endforeach; ?>
             </ul>
         <?php endif;
     endif;?>
  <?php
    $content = ob_get_contents();
    ob_clean();

    return $content;
  }

  public static function addScriptTinymce($script){
    $script['document'] = get_template_directory_uri() . '/inc/shortcode/js/document.js';

    return $script;
  }
}
add_action( 'wp_ajax_addShortcodeDocument', array( 'SDocument', 'formulaireDocument' ) );
add_action( 'admin_init', array ('SDocument', 'admin_init' ));
add_shortcode('document',array ('SDocument', 'render_shortcode'));
