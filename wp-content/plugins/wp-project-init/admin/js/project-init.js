jQuery(function(){
    if( jQuery('.wpi-notif .wpi-notif-section').length > 0 ) {

        jQuery('.wpi-notif a.wpi-button.show').click(function(){
            if ( jQuery(this).parents('.wpi-notif').find('.wpi-notif-section').is(':hidden') ) {
                jQuery(this).slideUp(200);
                jQuery(this).parents('.wpi-notif').find('.wpi-notif-section').slideDown(200);
            }
        });

        jQuery('.wpi-notif a.wpi-button.hide').click(function(){
            if ( jQuery(this).parents('.wpi-notif').find('.wpi-notif-section').is(':visible') ) {
                jQuery(this).parents('.wpi-notif').find('a.wpi-button.show').slideDown(200);
                jQuery(this).parents('.wpi-notif').find('.wpi-notif-section').slideUp(200);
                jQuery(this).parents('.wpi-notif').find('a.wpi-button.show').show();
            }
        });
    }

    jQuery('[name=type_profil]').change(function(){
        jQuery('.type_profil_choice').hide();
        jQuery('#type_profil' + jQuery(this).val()).show();
    })

    jQuery('#loadprofile').change(function(){
        if (jQuery(this).val() != ''){
            window.location.href = window.location.href + '&profile=' + jQuery(this).val() + '&type_profile='+jQuery('[name=type_profil]:checked').val();
        }
    })
});