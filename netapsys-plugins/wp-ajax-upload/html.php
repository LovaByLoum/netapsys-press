<!--script type="text/javascript">window.wpauUploadCount = 0;</script-->
<div id="wpau-upload-container-<?php echo $atts['id'];?>" class="wpau-upload-container">
    <a id="wpau-uploader-<?php echo $atts['id'];?>" data-id="<?php echo $atts['id'];?>" class="wpau_button" href="javascript:;">Upload</a>

    <div class="wpau-upload-imagelist">
        <ul id="wpau-ul-list" class="wpau-upload-list">
        	<?php if(isset($atts['aid']) && $atts['aid']>0):
        		  $img = wp_get_attachment_image($atts['aid'],isset($atts['size'])?$atts['size']:'thumbnail');
        	?>
	        	<li class="wpau-uploaded-files">
              <?php echo $img;?>
              <br>
              <a href="#" class="action-delete" data-upload_id="<?php echo $atts['id'];?>">Supprimer</a>
              <input type="hidden" name="wpau_image_id_<?php echo $atts['id'];?>[]" value="<?php echo $atts['aid'];?>">
            </li>
          <?php endif;?>
        </ul>
    </div>

</div>