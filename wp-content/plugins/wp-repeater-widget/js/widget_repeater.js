//gestion repetiteur
jQuery( function () {
    jQuery("#widgets-right").find(".widgets-holder-wrap .wr_add_item").live('click', function(item) {
        var link_add = jQuery(this);
        var div_widget = jQuery(this).parents('.widget-content');
        var nb_item_display = div_widget.find("#wr_widget_repeater_nb_item").val();
        nb_item_display = parseInt(nb_item_display);
        var last_fieldset_item = div_widget.find('fieldset:last');
        var the_clone = div_widget.find('fieldset:last').clone();

        the_clone.find('input,select').each( function() {
            jQuery(this).removeAttr('value');
            jQuery(this).find('option').removeAttr('selected');
            var input_name = jQuery(this).attr('name');
            if (typeof input_name != "undefined"){
                input_name = input_name.replace(nb_item_display, (nb_item_display+1));
                jQuery(this).attr('name', input_name);
            }
        });
        _text = div_widget.find('fieldset:first legend span').text();
        the_clone.find('legend').html("<span>"+_text+"</span> n°"+(nb_item_display+1)+'<a class="wr_del_item" style="background: url(images/xit.gif) no-repeat -10px 0px;padding:0 3px;margin-left:5px;cursor:pointer;">&nbsp;</a>');
        the_clone.find('.wr_item_numero').val(nb_item_display+1);
        the_clone.find('img.preview').remove();

        // Affichage
        jQuery("<fieldset style='border: 1px dotted #DFDFDF; padding: 5px;'>"+the_clone.html()+"</fieldset>").insertAfter(last_fieldset_item);
        div_widget.find("#wr_widget_repeater_nb_item").val((nb_item_display+1));

        return false;
    });

    jQuery("#widgets-right").find(".widgets-holder-wrap .wr_del_item").live('click', function(item) {
        var fieldset = jQuery(this).parents('fieldset');
        var div_widget = jQuery(this).parents('.widget-content');
        var nb_item_display = div_widget.find("#wr_widget_repeater_nb_item").val();
        nb_item_display = parseInt(nb_item_display);

        if(nb_item_display==1)
            return false;

        fieldset.remove();

        div_widget.find('fieldset').each(function(index){
            var current_fieldset = jQuery(this);
            var new_position = index+1;
            var old_position = parseInt(current_fieldset.find('.wr_item_numero').val());

            current_fieldset.find('input,selected').each( function() {
                var input_name = jQuery(this).attr('name');
                if (typeof input_name != "undefined"){
                    input_name = input_name.replace(old_position, new_position);
                    jQuery(this).attr('name', input_name);
                }
            });
            _text = div_widget.find('fieldset:first legend span').text();
            current_fieldset.find('legend').html("<span>"+_text+"</span> n°"+(new_position)+'<a class="wr_del_item" style="background: url(images/xit.gif) no-repeat -10px 0px;padding:0 3px;margin-left:5px;cursor:pointer;">&nbsp;</a>');

            current_fieldset.find('.wr_item_numero').val(new_position);

        });

        div_widget.find("#wr_widget_repeater_nb_item").val((nb_item_display-1));

    });
});

//gestion image choose
var wr_widget_repeater_image_context;
var wr_widget_image_thickbox_updater;
jQuery( function () {
    jQuery('a.wr_widget_repeater_image_field').live('click', function(e){
        e.preventDefault();
        var href = jQuery(this).attr('href'), width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
        if ( ! href ) return;
        href = href.replace(/&width=[0-9]+/g, '');
        href = href.replace(/&height=[0-9]+/g, '');
        jQuery(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
        wr_widget_repeater_image_context = jQuery(this).parents('fieldset');
        jQuery('#TB_title').remove();
        tb_show(jQuery(this).attr('title'), jQuery(this).attr('href'), false);
        wr_widget_image_thickbox_updater = setInterval( wr_widget_image_update_thickbox, 500 );
    });
});

function wr_widget_image_update_thickbox(){
    if(wr_widget_repeater_image_context){

        // need to add our own button
        if(jQuery('#TB_iframeContent').contents().find('td.savesend').length){
            jQuery('#TB_iframeContent').contents().find('td.savesend').each(function(){
                if(jQuery(this).find('input.gm-widget-image-choose').length==0){
                    jQuery(this).find('input').hide();
                    jQuery(this).prepend('<input type="submit" name="gmwidgetimagechoose" class="gm-widget-image-choose button" value="Use this image" />');
                }
            });
        }

        // need to handle the click item
        jQuery('#TB_iframeContent').contents().find('td.savesend input.gm-widget-image-choose').unbind('click').click(function(e){
            e.preventDefault();
            wr_widget_image_parent = jQuery(this).parent().parent().parent();
            wr_widget_image_id = wr_widget_image_parent.find('td.imgedit-response').attr('id').replace('imgedit-response-','');
            wr_widget_image_thumb = wr_widget_image_parent.parent().parent().find('img.pinkynail').attr('src');
            wr_widget_image_url = wr_widget_image_parent.find('.urlfield').val();
            wr_widget_image_ref = wr_widget_image_parent.clone();

            wr_widget_image_return(wr_widget_image_id,wr_widget_image_thumb,wr_widget_image_url);

            // close everything and wrap up
            wr_widget_repeater_image_context = false;
            tb_remove();
        });

        // update button
        if(jQuery('#TB_iframeContent').contents().find('.media-item .savesend input[type=submit], #insertonlybutton').length){
            jQuery('#TB_iframeContent').contents().find('.media-item .savesend input[type=submit], #insertonlybutton').val('Use this image');
        }
        if(jQuery('#TB_iframeContent').contents().find('#tab-type_url').length){
            jQuery('#TB_iframeContent').contents().find('#tab-type_url').hide();
        }
        if(jQuery('#TB_iframeContent').contents().find('tr.post_title').length){
            // we need to ALWAYS get the fullsize since we're retrieving the guid
            // if the user inserts an image somewhere else and chooses another size, everything breaks
            jQuery('#TB_iframeContent').contents().find('tr.image-size input[value="full"]').prop('checked', true);
            jQuery('#TB_iframeContent').contents().find('tr.post_title,tr.image_alt,tr.post_excerpt,tr.image-size,tr.post_content,tr.url,tr.align,tr.submit>td>a.del-link').hide();
        }
    }

    if(jQuery('#TB_iframeContent').contents().length==0&&wr_widget_repeater_image_context){
        // the thickbox was closed
        clearInterval(wr_widget_image_thickbox_updater);
        wr_widget_repeater_image_context = false;
    }
}

function wr_widget_image_return(wr_widget_image_id,wr_widget_image_thumb,wr_widget_image_url){
    // show our image for reference
    wr_image_container = wr_widget_repeater_image_context.find('a.wr_widget_repeater_image_field').parent();
    wr_image_container.find('img.preview').remove();
    wr_image_container.append('<img class="preview" src="' + wr_widget_image_url + '" width="210" alt="Image" />');

    // save our image id
    wr_image_container.find('input').val(wr_widget_image_url);
}
