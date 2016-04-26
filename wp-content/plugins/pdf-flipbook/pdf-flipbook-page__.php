<!DOCTYPE html>
<html dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Lecture PDF</title>
    <?php 
    $minify = false;
    ?>
    <?php if(!$minify):?>
    	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/viewer.css"/>
    	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/book.css"/>
    	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/magazine.css"/>
    	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/jquery.ui.css"/>
    	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/steve-jobs.css"/>
    <?php else :?>
    	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/pdf-flipbook.min.css"/>
    <?php endif;?>
    
    <?php 
	if (isset($_REQUEST["fichier"])){
		$file = $_REQUEST["fichier"];
		$url = wp_get_attachment_url( $file );
	}
	?>
    <script type="text/javascript">
    	var kDefaultURL = '<?php echo $url?>';
    	var isMobile = '<?php echo intval(commonTools::isMobile());?>';
    </script>
    <?php if(!$minify):?>
		<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/turn.js"></script>
	    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/compatibility.js"></script>
	    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/110n.js"></script>
	    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/pdf.js"></script>
	    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/debugger.js"></script>
	    <script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__);?>js/viewer.js"></script>
	<?php else :?>
    	<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>css/pdf-flipbook.min.css"/>
    <?php endif;?>    
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
    <div id="outerContainer">
      <div id="sidebarContainer">
        <div id="toolbarSidebar">
          <div class="splitToolbarButton toggled">
            <button id="viewThumbnail" class="toolbarButton toggled" title="Afficher les vignettes" onclick="PDFView.switchSidebarView('thumbs')" tabindex="1" data-l10n-id="thumbs">
               <span data-l10n-id="thumbs_label">Vignettes</span>
            </button>
            <button id="viewOutline" class="toolbarButton" title="Voir Structure du document" onclick="PDFView.switchSidebarView('outline')" tabindex="2" data-l10n-id="outline">
               <span data-l10n-id="outline_label">Structure du document</span>
            </button>
          </div>
        </div>
        <div id="sidebarContent">
          <div id="thumbnailView">
          </div>
          <div id="outlineView" class="hidden">
          </div>
        </div>
      </div>  <!-- sidebarContainer -->

      <div id="mainContainer">
        <div class="toolbar">
          <div id="toolbarContainer">

            <div id="toolbarViewer">
              <div id="toolbarViewerLeft">
                <button id="sidebarToggle" class="toolbarButton" title="Basculer la barre lat&eacute;rale" tabindex="3" data-l10n-id="toggle_slider">
                  <span data-l10n-id="toggle_slider_label">Basculer la barre lat&eacute;rale</span>
                </button>
                <div class="toolbarButtonSpacer"></div>
                <div class="splitToolbarButton">
                  <button class="toolbarButton pageUp" title="Page pr&eacute;c&eacute;dente" onclick="PDFView.page--" id="previous" tabindex="4" data-l10n-id="previous">
                    <span data-l10n-id="previous_label">Pr&eacute;c&eacute;dente</span>
                  </button>
                  <div class="splitToolbarButtonSeparator"></div>
                  <button class="toolbarButton pageDown" title="Page suivante" onclick="PDFView.page++" id="next" tabindex="5" data-l10n-id="next">
                    <span data-l10n-id="next_label">Suivante</span>
                  </button>
                </div>
                <label id="pageNumberLabel" class="toolbarLabel" for="pageNumber" data-l10n-id="page_label">Page: </label>
                <input type="number" id="pageNumber" class="toolbarField pageNumber" onchange="PDFView.page = this.value;" value="1" size="4" min="1" tabindex="6"/>
                <span id="numPages" class="toolbarLabel"></span>
              </div>
              <div id="toolbarViewerRight">
                <input id="fileInput" class="fileInput" type="file" oncontextmenu="return false;" style="visibility: hidden; position: fixed; right: 0; top: 0" />
                <a href="<?php echo get_option('siteurl');?>/download.php?file=<?php echo $file;?>">
                <button id="download" class="toolbarButton download" title="T&eacute;l&eacute;charger le fichier" tabindex="12" data-l10n-id="download" onclick="oan_pdf_tracking('<?php echo $url;?>',2)">
                  <span data-l10n-id="download_label">T&eacute;l&eacute;charger</span>
                </button>
                </a>
                
                <a href="#" id="viewBookmark" class="toolbarButton bookmark" title="Vue actuelle (copie ou ouvre dans une nouvelle fen&ecir;tre)" tabindex="13" data-l10n-id="bookmark" style="display:none;"><span data-l10n-id="bookmark_label">Vue actuelle</span></a>
              </div>
              <div class="outerCenter">
                <div class="innerCenter" id="toolbarViewerMiddle">
                  <div class="splitToolbarButton">
                    <button class="toolbarButton zoomOut" title="R&eacute;tr&eacute;cir" onclick="PDFView.zoomOut();" tabindex="7" data-l10n-id="zoom_out">
                      <span data-l10n-id="zoom_out_label">R&eacute;tr&eacute;cir</span>
                    </button>
                    <div class="splitToolbarButtonSeparator"></div>
                    <button class="toolbarButton zoomIn" title="Agrandir" onclick="PDFView.zoomIn();" tabindex="8" data-l10n-id="zoom_in">
                      <span data-l10n-id="zoom_in_label">Agrandir</span>
                     </button>
                  </div>
                  <span id="scaleSelectContainer" class="dropdownToolbarButton">
                     <select id="scaleSelect" onchange="PDFView.parseScale(this.value);" title="Zoom" oncontextmenu="return false;" tabindex="9" data-l10n-id="zoom">
                      <option id="pageAutoOption" value="auto" selected="selected" data-l10n-id="page_scale_auto">Zoom automatique</option>
                      <option id="pageActualOption" value="page-actual" data-l10n-id="page_scale_actual">Taille r&eacute;elle</option>
                      <option id="pageFitOption" value="page-fit" data-l10n-id="page_scale_fit">Ajuster &agrave; la page</option>
                      <option id="pageWidthOption" value="page-width" data-l10n-id="page_scale_width">Pleine largeur</option>
                      <option id="customScaleOption" value="custom"></option>
                      <option value="0.5">50%</option>
                      <option value="0.75">75%</option>
                      <option value="1">100%</option>
                      <option value="1.25">125%</option>
                      <option value="1.5">150%</option>
                      <option value="2">200%</option>
                    </select>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="viewerContainer" class= "magazine-viewport">
          <div id="viewerCouverture" class="sj-book">
			<div class="pageRest front-side"> <div class="depth"></div> </div>
          	<div id="viewer"></div>
          	<div class="pageRest back-side"> <div class="depth"></div> </div>
          </div>
        </div>

        <div id="loadingBox">
            <div id="loading" data-l10n-id="loading" data-l10n-args='{"percent": 0}'>Chargement... 0%</div>
            <div id="loadingBar"><div class="progress"></div></div>
        </div>

        <div id="errorWrapper" hidden='true'>
          <div id="errorMessageLeft">
            <span id="errorMessage"></span>
            <button id="errorShowMore" onclick="" oncontextmenu="return false;" data-l10n-id="error_more_info">
              Plus d'information
            </button>
            <button id="errorShowLess" onclick="" oncontextmenu="return false;" data-l10n-id="error_less_info" hidden='true'>
              Moins d'information
            </button>
          </div>
          <div id="errorMessageRight">
            <button id="errorClose" oncontextmenu="return false;" data-l10n-id="error_close">
              Fermer
            </button>
          </div>
          <div class="clearBoth"></div>
          <textarea id="errorMoreInfo" hidden='true' readonly="readonly"></textarea>
        </div>
      </div> <!-- mainContainer -->

    </div> <!-- outerContainer -->
  </body>
</html>