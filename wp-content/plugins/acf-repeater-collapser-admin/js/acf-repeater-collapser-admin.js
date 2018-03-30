jQuery(document).ready(function($) {
    $('.field_type-repeater .repeater .acf-input-table.row_layout > tbody > tr.row > .acf_input-wrap > .acf_input').livequery(function(){
        _this = $(this);
        _this.addClass('collapsed-repeater');
        var _value = _this.find('input[type=text][value!=""]').eq(0).val();
        if ( !_value ) {
            if (  _this.find('select').length > 0 ){
                if( _this.find('select option:selected').length > 0 ){
                    _value = _this.find('select option:selected').eq(0).text();
                } else {
                    _value = _this.find('select option').eq(0).text();
                }
            } else {
                _value = 'NAN';
            }
        }
        if ( _value ) {
            _value = _value.substring(0,20);
            _collapsed_wrapper = _this.find('> tbody > tr:first-child > td.label > label');
            if ( _collapsed_wrapper.length > 0 ){
                _collapsed_wrapper.append('<em class="collapse-value">' + '(' + _value + '...)' + '</em>' );
            }
        }
    })

    // Show/hide all rows if label gets clicked
    $(".field_type-repeater .repeater .acf-input-table.row_layout > tbody > tr.row > .acf_input-wrap > .acf_input > tbody > tr:first-child > td.label").live("click", function(){
        _this = $(this);
        _parent =  $(this).parents('.acf_input').eq(0);
        var _value = "";
        _parent.find('input[type=text]').each(function(){
            if ( _value == "" && jQuery(this).val() != "" ){
                _value = jQuery(this).val();
            }
        });
        _parent.toggleClass('collapsed-repeater');
        if ( _parent.hasClass('collapsed-repeater') ) {
            if ( !_value ) {
                _value = _parent.find('select option:selected').eq(0).text();
            }
            if ( _value ) {
                _value = _value.substring(0,20);
                _this.find('>label').append('<em class="collapse-value">' + '(' + _value + '...)' + '</em>' );
            }
        } else {
            _this.find('.collapse-value').remove();
        }
    });

});