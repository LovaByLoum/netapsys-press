jQuery(document).ready(function(){
    jQuery('input.jpress-datepicker').datepicker({
        dateFormat : 'yy-mm-dd',
        altField : '',
        altFormat :  'yy-mm-dd',
        changeYear: true,
        yearRange: '-100:+100',
        changeMonth: true,
        showButtonPanel : true,
        firstDay: 1,
        showButtonPanel: true,
        closeText: 'Fermer'
    })
})