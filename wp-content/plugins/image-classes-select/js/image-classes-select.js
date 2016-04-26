(function( $, wp, _ ) {
	var frame;

	if ( ! wp.media.events ) {
		return;
	}

    function scriptClassesSelectCallback( view ) {
        view.on( 'post-render', function() {
            $('.media-frame-content .advanced-section .extra-classes input').livequery(function(){
                var _cssclassinput = $(this);
                var _classes = _cssclassinput.val();
                var classes_array = _classes.trim().split(" ").filter(function(el){return el!=""}).sort();

                _option ="";
                for (var k in ics_image_classes_list){
                    /*var _item = classes_array[_i];
                     _item = _item.trim();*/
                    if(ics_image_classes_list[k]!=""){
                        _selected = (!$.inArray( k, classes_array ))?' selected ':'';
                        _option += '<option value="' + k +'" '+_selected+'>' + ics_image_classes_list[k]+'</option>';
                    }
                }
                var _select = '<select data-setting="extraClasses">'+_option+'</select>';

                $(_select).insertAfter(_cssclassinput).css({'width':'70%'});
                _cssclassinput.remove();

            })
        });
        //link-class-name
	}

	wp.media.events.on( 'editor:image-edit', function( options ) {
		var dom = options.editor.dom,
			image = options.image,
			attributes;

        //attributes.className = image.className;
		//attributes.className = 'test';
		//options.metadata = _.extend( options.metadata, attributes );
	} );

    wp.media.events.on( 'editor:frame-create', function( options ) {
        frame = options.frame;
        frame.on( 'content:render:image-details', scriptClassesSelectCallback );
    } );

	wp.media.events.on( 'editor:image-update', function( options ) {
		var editor = options.editor,
			dom = editor.dom,
			image  = options.image,
			model = frame.content.get().model;

			dom.setStyle( image, 'marginRight', '10' );

	} );

})( jQuery, wp, _ );
