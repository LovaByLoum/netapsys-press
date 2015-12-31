<?php

// - grab wp load, wherever it's hiding -
function include_wp_head($src) {
  $path = dirname(__FILE__);
  while ( is_file( realpath( $path ) . DIRECTORY_SEPARATOR . $src ) == false ){
    $path .= '/..';
  }
  return realpath($path) . DIRECTORY_SEPARATOR . $src;
}
$include = include_wp_head('wp-load.php');

include_once($include);

?>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>video</title>
        <script type="text/javascript" src="../tiny_mce_popup.js"></script>
        <script type="text/javascript" src="video_rule.js"></script>
    </head>
    <body>
        <form onSubmit="videoDialog.update();
                return false;" action="#">
            <div id="frmbody">
                <div class="title">Insérer un lien youtube:</div>
                <div class="frmRow">
                    <label>URL:</label>
                    <input id="url_video" type="text" name="url_video" class="mceFocus" style="width: 88%" />
                    <br />
                    <span id="warning"></span>
                </div>
                <div class="mceActionPanel">
                    <div style="float: left">
                        <br />
                        <input type="submit" id="insert" name="insert" value="Insérer" />
                    </div>

                    <div style="float: right">
                        <br />
                        <input type="button" id="cancel" name="cancel" value="Annuler" onClick="tinyMCEPopup.close();" />
                    </div>

                    <br style="clear:both" />
                </div>
            </div>
        </form>

    </body>
</html>
