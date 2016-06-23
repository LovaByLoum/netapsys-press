jQuery(document).ready(function($) {

    $('.field_type-repeater').each( function() {
        if( $( '.acf-input-table', $(this) ).hasClass('row_layout') ) {
            $(this).find('.acf_input tr').eq(0).addClass('first_tr');
        }
    });

    $('.repeater-footer .add-row-end.acf-button').live("click", function(){
        $(this).parents('.acf-input-table').find('tr.row').each(function() {
            $(this).find('.acf_input tr').eq(0).addClass('first_tr');
        });
        var $repeate_div = $(this).parents('.repeater');
        setTimeout(function(){
            $repeate_div.find('.row_layout tr.row').each(function() {
                $(this).find('.acf_input tr').eq(0).addClass('first_tr');
            });
        },500);
    });

    // Hide all but the first Rows
    $(".field_type-repeater .repeater .row .acf_input-wrap table.acf_input").addClass('collapsed-repeater');

    // Show/hide all rows if label gets clicked
    $(".field_type-repeater .repeater .row .acf_input-wrap table.acf_input .label").live("click", function(){
        _this = $(this);
        _parent =  $(this).parents('.acf_input').eq(0);
        _parent.toggleClass('collapsed-repeater');
        if ( _parent.hasClass('collapsed-repeater') ) {
            var _value = _this.parents('.acf_input').eq(0).find('input[type=text]').val();
            _value = _value.substring(0,20);
            if ( _value ) {
                _this.find('label').append('<em class="collapse-value">' + '(' + _value + '...)' + '</em>' );
            }
        } else {
            _this.find('.collapse-value').remove();
        }
    });

});