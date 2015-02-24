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
});