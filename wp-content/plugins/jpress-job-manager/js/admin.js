jQuery(document).ready(function(){
    jQuery('.select-deselect-all').click(function(){
        _this = jQuery( this);
        _index_parent = _this.parent().index() -1;
        _all_td = _this.parents('tbody').eq(0).find('tr').find('td:eq(' + _index_parent +  ')');
        if ( _this.is(':checked') ){
            _all_td.find( 'input:not(.select-deselect-all)').attr( 'checked', 'checked');
        }else{
            _all_td.find( 'input:not(.select-deselect-all)').removeAttr( 'checked' );
        }
    })

})