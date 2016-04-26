<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<style>
body, ul, table, form{
	margin:0;
	padding:0;
	overflow:<?php if(commonTools::isMobile(true)):?>hidden<?php else:?>scroll<?php endif;?>;
}
</style>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/jquery.min.1.7.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/modernizr.2.5.3.min.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/hash.js"></script>
<link href="<?php echo plugin_dir_url(__FILE__);?>css/flip.css" rel="stylesheet"/>
<?php 

function count_files($dir){
		$num = 0;
		$dir_handle = opendir($dir);
		while (false !== ($file = readdir($dir_handle))) {
    		if($file != '.' && $file != '..' && !is_dir($filedir) && !preg_match("#(-vignette.jpg)#",$file,$matches))
{
						$num++;
			}
		}
		closedir($dir_handle);
		return $num;
} 

if (isset($_REQUEST["fichier"])){
	$file = $_REQUEST["fichier"];
	$url = wp_get_attachment_url( $file );
}

//pdf path
$uploadUrl = wp_upload_dir();
$path = $uploadUrl['path'];
$urlinfo = pathinfo($url);
$path = $path . '/' . $urlinfo['filename'] . '.' . $urlinfo['extension'];

//image path and url
$dossier = 'image_pdf';
$PDFImageUrl = $uploadUrl['url']  . '/'. $dossier .'/'. $urlinfo['filename'];
$PDFImageDir = $uploadUrl['path'] . '/'. $dossier .'/'. $urlinfo['filename'] ;

//nombre de page
$numberPage = count_files($PDFImageDir);

//dimension image
$size = getimagesize($PDFImageDir.'/0.jpg');
$width 		= $size[0];
$height 	= $size[1];

//dimension vignette
$vignette_height = 88;
$ratio = $width/$height;
$vignette_width = ceil($vignette_height*$ratio);

$ignorePage = 3;
$supplPage = 4;
if ($height>$width){
	$format = 'double';
}else{
	$format = 'single';
}

if($numberPage%2==0){
	$numberPage+=$supplPage;
	$numberPagePair = 1;
}else{
	$numberPage+=$supplPage+1;
	$numberPagePair = 0;
}
?>
<title><?php echo $pdfflip->filename;?></title>
<script type="text/javascript">
	var pluginURL = '<?php echo plugin_dir_url(__FILE__);?>';
	var isMobile = '<?php echo intval(commonTools::isMobile());?>';
	var isMobileAndIpad = '<?php echo intval(commonTools::isMobile(true));?>';
	var pdfURL = '<?php echo $url?>';
	var pdfImageDir = '<?php echo $PDFImageUrl;?>';
	var numberPage = <?php echo $numberPage;?>;
	var titlePdf = '<?php echo $urlinfo['filename'];?>';
	var ignorePage = <?php echo $ignorePage;?>;
	var pdfFormat = '<?php echo $format;?>';
	var numberPagePair = <?php echo $numberPagePair;?>;
	var vignetteWidth = <?php echo $vignette_width;?>;
	var vignetteHeight = <?php echo $vignette_height;?>;
</script>
    
<?php 
if (function_exists('googleanalytics')){
	googleanalytics(); 
}?>   
<script type="text/javascript">
    var oan_tracked = <?php echo intval(is_plugin_active("googleanalytics/googleanalytics.php")); ?>;
	oan_pdf_tracking("<?php echo $url;?>",1);
	function oan_pdf_tracking($pdfname, $action){
		if(oan_tracked == 1){
			if ($action == 1){
				_gaq.push(['_trackEvent', 'PDF', 'Consultation', $pdfname]); 
				//console.log('pdf vue ' + $pdfname);
			}else{
				_gaq.push(['_trackEvent', 'PDF', 'Telechargement', $pdfname]); 
				//console.log('pdf down ' + $pdfname);
			}
		}
	}
</script>
</head>
<body>
<div class="bar">
	<div class="share">
		<a href="<?php echo get_option('siteurl');?>/download.php?file=<?php echo $file;?>" class="icon download" title="T&eacute;l&eacute;charger">T&eacute;l&eacute;charger</a>
	</div>
	<a class="icon quit"></a>
</div>
<div id="canvas" class="canvas-<?php echo $format;?>">
	<div id="book-zoom">
		<div class="sj-book <?php echo $format;?>">
			<div depth="5" class="hard"> <div class="side"></div> </div>
			<div depth="5" class="hard front-side"> <div class="depth"></div> </div>
			<div class="hard fixed back-side p<?php echo $numberPage-1;?> before-last"> <div class="depth"></div> </div>
			<div class="hard p<?php echo $numberPage;?> last"></div>
		</div>
	</div>
	<div id="slider-bar" class="turnjs-slider">
		<div id="slider"></div>
	</div>
</div>


<script type="text/javascript">

function loadApp() {
	
	var flipbook = $('.sj-book');

	// Check if the CSS was already loaded
	
	if (flipbook.width()==0 || flipbook.height()==0) {
		setTimeout(loadApp, 10);
		return;
	}

	// Mousewheel

	$('#book-zoom').mousewheel(function(event, delta, deltaX, deltaY) {

		var data = $(this).data(),
			step = 30,
			flipbook = $('.sj-book'),
			actualPos = $('#slider').slider('value')*step;

		if (typeof(data.scrollX)==='undefined') {
			data.scrollX = actualPos;
			data.scrollPage = flipbook.turn('page');
		}

		data.scrollX = Math.min($( "#slider" ).slider('option', 'max')*step,
			Math.max(0, data.scrollX + deltaX));

		var actualView = Math.round(data.scrollX/step),
			page = Math.min(flipbook.turn('pages'), Math.max(1, actualView*2 - 2));

		if ($.inArray(data.scrollPage, flipbook.turn('view', page))==-1) {
			data.scrollPage = page;
			flipbook.turn('page', page);
		}

		if (data.scrollTimer)
			clearInterval(data.scrollTimer);
		
		data.scrollTimer = setTimeout(function(){
			data.scrollX = undefined;
			data.scrollPage = undefined;
			data.scrollTimer = undefined;
		}, 1000);

	});

	// Slider

	$( "#slider" ).slider({
		min: 1,
		max: 100,

		start: function(event, ui) {

			if (!window._thumbPreview) {
				_thumbPreview = $('<div />', {'class': 'thumbnail'}).html('<div></div>');
				setPreview(ui.value);
				_thumbPreview.appendTo($(ui.handle));
			} else
				setPreview(ui.value);

			moveBar(false);

		},

		slide: function(event, ui) {

			setPreview(ui.value);

		},

		stop: function() {

			if (window._thumbPreview)
				_thumbPreview.removeClass('show');
			$('.sj-book').turn('page', Math.max(1, $(this).slider('value')*2 - 2));

		}
	});


	// URIs
	
	Hash.on('^page\/([0-9]*)$', {
		yep: function(path, parts) {

			var page = parts[1];

			if (page!==undefined) {
				if ($('.sj-book').turn('is'))
					$('.sj-book').turn('page', page);
			}

		},
		nop: function(path) {

			if ($('.sj-book').turn('is'))
				$('.sj-book').turn('page', 3);
		}
	});

	// Arrows

	$(document).keydown(function(e){

		var previous = 37, next = 39;

		switch (e.keyCode) {
			case previous:

				$('.sj-book').turn('previous');

			break;
			case next:
				
				$('.sj-book').turn('next');

			break;
		}

	});


	// Flipbook

	flipbook.bind(($.isTouch) ? 'touchend' : 'click', zoomHandle);

	flipbook.turn({
		elevation: 50,
		acceleration: !isChrome(),
		autoCenter: true,
		gradients: true,
		duration: 1000,
		pages: <?php echo $numberPage;?>,
		when: {
			turning: function(e, page, view) {
				
				var book = $(this),
					currentPage = book.turn('page'),
					pages = book.turn('pages');

				if (currentPage>3 && currentPage<pages-3) {
				
					if (page==1) {
						book.turn('page', 2).turn('stop').turn('page', page);
						e.preventDefault();
						return;
					} else if (page==pages) {
						book.turn('page', pages-1).turn('stop').turn('page', page);
						e.preventDefault();
						return;
					}
				} else if (page>3 && page<pages-3) {
					if (currentPage==1) {
						book.turn('page', 2).turn('stop').turn('page', page);
						e.preventDefault();
						return;
					} else if (currentPage==pages) {
						book.turn('page', pages-1).turn('stop').turn('page', page);
						e.preventDefault();
						return;
					}
				}

				updateDepth(book, page);
				
				if (page>=2)
					$('.sj-book .p2').addClass('fixed');
				else
					$('.sj-book .p2').removeClass('fixed');

				if (page<book.turn('pages'))
					$('.sj-book .p<?php echo $numberPage-1;?>').addClass('fixed');
				else
					$('.sj-book .p<?php echo $numberPage-1;?>').removeClass('fixed');

				Hash.go('page/'+page).update();
					
			},

			turned: function(e, page, view) {

				var book = $(this);

				if (page==2 || page==3) {
					book.turn('peel', 'br');
				}

				updateDepth(book);
				
				$('#slider').slider('value', getViewNumber(book, page));

				book.turn('center');

			},

			start: function(e, pageObj) {
		
				moveBar(true);

			},

			end: function(e, pageObj) {
			
				var book = $(this);

				updateDepth(book);

				setTimeout(function() {
					
					$('#slider').slider('value', getViewNumber(book));

				}, 1);

				moveBar(false);

			},

			missing: function (e, pages) {

				for (var i = 0; i < pages.length; i++) {
					addPage(pages[i], $(this));
				}

			}
		}
	});


	$('#slider').slider('option', 'max', numberOfViews(flipbook));

	flipbook.addClass('animated');

	// Show canvas

	$('#canvas').css({visibility: ''});
}

// Hide canvas

$('#canvas').css({visibility: 'hidden'});

// Load turn.js

yepnope({
	test : Modernizr.csstransforms,
	yep: ['<?php echo plugin_dir_url(__FILE__);?>js/turn.min.js'],
	nope: ['<?php echo plugin_dir_url(__FILE__);?>js/turn.html4.min.js', '<?php echo plugin_dir_url(__FILE__);?>css/jquery.ui.html4.css', 'css/steve-jobs-html4.css'],
	both: ['<?php echo plugin_dir_url(__FILE__);?>js/steve-jobs.js', '<?php echo plugin_dir_url(__FILE__);?>css/jquery.ui.css', '<?php echo plugin_dir_url(__FILE__);?>css/steve-jobs.css'],
	complete: loadApp
});
//$('title').text(titlePdf+ '.pdf');
</script>

</body>
</html>