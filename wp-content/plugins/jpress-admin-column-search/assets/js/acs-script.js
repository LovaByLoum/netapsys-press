jQuery(document).ready(function(){
    if(typeof acs_values != 'undefined'){
        jQuery(window).load(function(){
            jQuery.each(acs_values, function(index, value) {
                jQuery('select[name="acs_search[' + index + ']"]').val(value);
                jQuery('select[name="acs_search[' + index + ']"]').find('option[value="' + value + '"]').prop('selected', true);
            });
        });
    }
    jQuery('#posts-filter').keyup(function(e) {
        if(e.keyCode == 13) { // KeyCode de la touche entrée
            jQuery('.acs_search_submit').click();
        }
    });
    if(jQuery('.wp-list-table thead').find('.acs_input_cible').length>0){
        var td='';
        var classes;
        for(i=1;i<=jQuery('.wp-list-table thead tr th').length;i++){
            classes = jQuery('.wp-list-table thead tr th').eq(i-1).attr('class');
            classes = classes.match(/column-([a-z0-9_-]+)/);
            td+='<td class="column-'+classes[1]+'"></td>';
        }
        jQuery('<tr class="acs_row">'+td+'</tr>').insertBefore(jQuery('.wp-list-table tbody tr:first'));
        jQuery('.wp-list-table thead tr th').each(function(index){
            if(jQuery(this).find('.acs_input_cible').length>0){
                column = jQuery(this).find('.acs_input_cible').data('col');
                if(typeof acs_values != 'undefined'){
                    values=acs_values[column];
                }else{
                    values='';
                }
                if(acs_dropbox[column]){
                    input_form = acs_dropbox[column];
                }else{
                    input_form = '<input type="text" name="acs_search['+column+']" class="acs_input" value="'+values+'"/>';
                }
                jQuery('.wp-list-table tbody tr:first td').eq(index).append(input_form);
                jQuery(this).find('.acs_input_cible').remove();
            }
        });
        jQuery('.wp-list-table tbody tr:first td:first').append('<div class="acs_search_wrap"><input type="submit" id="acs_search" name="acs_search_submit" value="Go" title="Rechercher" class="acs_search_submit button-secondary"/></div>');

        /*remove default wp category dropbox to fix conflict*/
        jQuery('#cat').remove();
    }
});