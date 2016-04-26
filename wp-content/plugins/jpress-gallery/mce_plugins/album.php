<?php
// - grab wp load, wherever it's hiding -
function include_wp_head($src)
{
    $paths = array(
        ".",
        "..",
        "../..",
        "../../..",
        "../../../..",
        "../../../../..",
        "../../../../../..",
        "../../../../../../.."
    );
   
    foreach ($paths as $path) {
        if(file_exists($path . '/' . $src)) {
            return $path . '/' . $src;
        }
    }
}

$include = include_wp_head('wp-load.php');

//if the site throws an error because a file could not be included enter the correct path to wp-load in your root directory here:
if($include == '') $include = '../../../wp-load.php';

include_once($include);

$results = jpress_gallery_getAlbumList();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Galerie d'images</title>
	<script type="text/javascript" src="tiny_mce_popup.js"></script>
	<script type="text/javascript" src="rule.js"></script>
</head>
<body> 
	<form onSubmit="jpressGalleryDialog.update();return false;" action="#">
		<div id="frmbody">
			<div class="title">Ins&eacute;rer un album</div>
			<br />
			<div class="frmRow"><label for="album_slug" title="S&eacute;l&eacute;ctionner un album">S&eacute;l&eacute;ctionner un album:</label>
			<select id="album_slug" name="album_slug" class="mceFocus">
				<?php //loop for results
				foreach($results as $form){
					?>
					<option value='<?php echo $form['shortcode']?>'><?php echo $form['title']?></option>
					<?php
				}
				?>
			</select>
			<br />
			<span id="warning"></span>
			</div>
		</div>
		
		<div class="mceActionPanel">
			<div style="float: left">
			<br />
				<input type="submit" id="insert" name="insert" value="Ins&eacute;rer" />
			</div>

			<div style="float: right">
			<br />
				<input type="button" id="cancel" name="cancel" value="Annuler" onClick="tinyMCEPopup.close();" />
			</div>

			<br style="clear:both" />
		</div>
	</form>

</body> 
</html> 
