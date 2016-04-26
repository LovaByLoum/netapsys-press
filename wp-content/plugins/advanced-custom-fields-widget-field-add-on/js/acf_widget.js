jQuery(document).ready(function(){
    jQuery('.choice_widget').livequery(function(){
      	jQuery('.choice_widget').change(function(){
      		_this = jQuery(this);
      		_this.parent().find('img.acf_loading').show();
      		_table = _this.parents('.acf_widget_container tbody');
      		_input = _this.parents('.acf_widget_field_block').find('input.acf_widget_true_value');
      		_type = _this.parents('.acf_widget_field_block').find('input.acf_widget_type');
      		jQuery.ajax({
      			url: ajaxurl,
				data: {
					action:'acf_load_widget',
					value:_this.val()
				},
				type: 'post',
				dataType: 'html',
				success: function(_html){
					_table.html(_html);	
					_table.find('[name]').each(function(){
						jQuery(this).attr('name',_input.attr('name')+'['+jQuery(this).attr('name')+']');
					});
					_type.val(_this.val());		
				}
      		});
      	});
    });
});
