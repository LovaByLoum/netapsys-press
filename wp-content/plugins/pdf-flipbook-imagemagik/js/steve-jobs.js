/* Steve jobs' book */

function updateDepth(book, newPage) {
	var page = book.turn('page'),
		pages = book.turn('pages'),
		depthWidth = 16*Math.min(1, page*2/pages),
		nbPages = pages - ignorePage;
	
		newPage = newPage || page;
		
		if (nbPages<15){
		   pas = 20 + (10-nbPages);
		}else{
		   pas = 20;
		}

	if (newPage>3)
		$('.sj-book .p2 .depth').css({
			width: depthWidth,
			left: pas - depthWidth
		});
	else
		$('.sj-book .p2 .depth').css({width: 0});

		depthWidth = 16*Math.min(1, (pages-page)*2/pages);

	if (newPage<pages-3)
		$('.sj-book .p' + (numberPage-1) +' .depth').css({
			width: depthWidth,
			right: pas - depthWidth
		});
	else
		$('.sj-book .p' + (numberPage-1) +' .depth').css({width: 0});
	
}

function loadPage(page, pageElement) {
	// Create an image element
	if (numberPagePair==0 && page == numberPage-2){
		pageElement.find('.loader').remove();
	}else{
		var img = $('<img />');
	
		img.mousedown(function(e) {
			e.preventDefault();
		});
	
		img.load(function() {
			
			// Set the size
			$(this).css({width: '100%', height: '100%'});
	
			// Add the image to the page after loaded
	
			$(this).appendTo(pageElement);
	
			// Remove the loader indicator
			
			pageElement.find('.loader').remove();
		});
	
		// Load the page
	
		img.attr('src', pdfImageDir+'/' +  (page-ignorePage) + '.jpg');
		if (isMobileAndIpad == '0'){
			img.addClass('zoom-this');
		}
	}
}

function addPage(page, book) {

	var id, pages = book.turn('pages');
	var w, h;
	if (pdfFormat== 'double'){
		w = 460;
		h=582;
	}else{
		w = 582;
		h=460;
	}
	
	if (!book.turn('hasPage', page)) {

		var element = $('<div />',
			{'class': 'own-size',
				css: {width: w, height: h}
			}).
			html('<div class="loader"></div>');

		if (book.turn('addPage', element, page)) {
			loadPage(page,element);
		}

	}
}

function numberOfViews(book) {
		return book.turn('pages') / 2 + 1;
}

function getViewNumber(book, page) {
		return parseInt((page || book.turn('page'))/2 + 1, 10);
}

function zoomHandle(e) {

	if ($('.sj-book').data().zoomIn)
		zoomOut();
	else if (e.target && $(e.target).hasClass('zoom-this')) {
		zoomThis($(e.target));
	}

}

function zoomThis(pic) {

	var	position, translate,
		tmpContainer = $('<div />', {'class': 'zoom-pic'}),
		transitionEnd = $.cssTransitionEnd(),
		tmpPic = $('<img />'),
		zCenterX = $('#book-zoom').width()/2,
		zCenterY = $('#book-zoom').height()/2,
		bookPos = $('#book-zoom').offset(),
		picPos = {
			left: pic.offset().left - bookPos.left,
			top: pic.offset().top - bookPos.top
		},
		completeTransition = function() {
			$('#book-zoom').unbind(transitionEnd);

			if ($('.sj-book').data().zoomIn) {
				tmpContainer.appendTo($('body'));

				tmpPic.css({
					margin: position.top + 'px ' + position.left+'px'
				}).
				/*appendTo(tmpContainer).*/
				fadeOut(0).
				fadeIn(500);
			}
		};

		$('.sj-book').data().zoomIn = true;

		$('.sj-book').turn('disable', true);

		$(window).resize(zoomOut);
		
		tmpContainer.click(zoomOut);

		tmpPic.load(function() {
			var realWidth = $(this)[0].width,
				realHeight = $(this)[0].height,
				zoomFactor = realWidth/pic.width(),
				picPosition = {
					top:  (picPos.top - zCenterY)*zoomFactor + zCenterY + bookPos.top,
					left: (picPos.left - zCenterX)*zoomFactor + zCenterX + bookPos.left
				};


			position = {
				top: ($(window).height()-realHeight)/2,
				left: ($(window).width()-realWidth)/2
			};

			translate = {
				top: position.top-picPosition.top,
				left: position.left-picPosition.left
			};

			$('.samples .bar').css({visibility: 'hidden'});
			$('#slider-bar').hide();
			
		
			$('#book-zoom').transform(
				'translate('+translate.left+'px, '+translate.top+'px)' +
				'scale('+zoomFactor+', '+zoomFactor+')');

			if (transitionEnd)
				$('#book-zoom').bind(transitionEnd, completeTransition);
			else
				setTimeout(completeTransition, 1000);

		});

		tmpPic.attr('src', pic.attr('src'));

}

function zoomOut() {

	var transitionEnd = $.cssTransitionEnd(),
		completeTransition = function(e) {
			$('#book-zoom').unbind(transitionEnd);
			$('.sj-book').turn('disable', false);
			$('body').css({'overflow': 'auto'});
			moveBar(false);
		};

	$('.sj-book').data().zoomIn = false;

	$(window).unbind('resize', zoomOut);

	moveBar(true);

	$('.zoom-pic').remove();
	$('#book-zoom').transform('scale(1, 1)');
	$('.samples .bar').css({visibility: 'visible'});
	$('#slider-bar').show();

	if (transitionEnd)
		$('#book-zoom').bind(transitionEnd, completeTransition);
	else
		setTimeout(completeTransition, 1000);
}


function moveBar(yes) {
	if (Modernizr && Modernizr.csstransforms) {
		$('#slider .ui-slider-handle').css({zIndex: yes ? -1 : 10000});
	}
}

function setPreview(view) {
	var previewWidth = vignetteWidth*2,
	//var previewWidth = 125*2,
		numeroVignette = view*2-5,
		previewHeight = vignetteHeight,
		previewSrcFisrt = pdfImageDir + '/' + numeroVignette + '-vignette.jpg',
		previewSrcSecond = pdfImageDir + '/' + (numeroVignette+1) + '-vignette.jpg',
		preview = $(_thumbPreview.children(':first')),
		numPages = (view==1 || view==2 || view==$('#slider').slider('option', 'max') || view==($('#slider').slider('option', 'max')-1)) ? 1 : 2,
		width = (numPages==1) ? previewWidth/2 : previewWidth;

	_thumbPreview.
		addClass('no-transition').
		css({width: width + 15,
			height: previewHeight + 15,
			top: -previewHeight - 30,
			left: ($($('#slider').children(':first')).width() - width - 15)/2
		});

	preview.css({
		width: width,
		height: previewHeight
	});
	if(numeroVignette<0){
		previewSrcFisrt =null;
	}else if (numberPagePair==0 && numeroVignette>=numberPage-(ignorePage+2)){
		previewSrcFisrt = null;
		previewSrcSecond = null;
	}
	
	if(view==1){
		previewSrcFisrt = null;
		previewSrcSecond = null;
	}else if(view==2){
		previewSrcFisrt = null
	}else if(view==$('#slider').slider('option', 'max')){
		previewSrcFisrt = null;
		previewSrcSecond = null;
	}else if(view == ($('#slider').slider('option', 'max')-1)){
		previewSrcSecond = null;
	}
	
	if (preview.find('img.first').length ==0) {
		preview.
		append('<img class="first"/>').
		append('<img class="second"/>');

		setTimeout(function(){
			_thumbPreview.removeClass('no-transition');
		}, 0);

	}
	if(previewSrcFisrt){
		preview.find('img.first').attr('src',previewSrcFisrt).show();
	}else{
		preview.find('img.first').hide();
	}
	if(previewSrcSecond){
		preview.find('img.second').attr('src',previewSrcSecond).show();
	}else{
		preview.find('img.second').hide();
	}
}

function isChrome() {

	// Chrome's unsolved bug
	// http://code.google.com/p/chromium/issues/detail?id=128488

	return navigator.userAgent.indexOf('Chrome')!=-1;

}