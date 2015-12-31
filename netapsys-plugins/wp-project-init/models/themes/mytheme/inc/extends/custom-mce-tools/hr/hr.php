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
        <title>Ligne horizontale</title>
        <script type="text/javascript" src="../jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="../tiny_mce_popup.js"></script>
        <script type="text/javascript" src="hr_rule.js"></script>
        <style>
            #frmbody #insert, #frmbody #cancel, .choice_color input, .choice_color label {
                cursor: pointer;
            }
            .choice_color span {
                display: inline-block;
                width: 68%;
                height: 5px;
            }
            .choice_color .hr_vert {
                background: #117c36;
                margin-left: 57px;
            }
            .choice_color .hr_jaune {
                background: #ffdd00;
                margin-left: 48px;
            }
            .choice_color .hr_gris_1 {
                background: #efefef;
                margin-left: 26px;
            }
            .choice_color .hr_gris_2 {
                background: #dcdcdc;
                margin-left: 18px;
            }
        </style>
    </head>
    <body>
        <form onSubmit="hr_customDialog.update();return false;" action="#" style="padding: 1em;padding-top: 0">
            <div id="frmbody">
                <div class="title" style="text-align: center;font-size: 24px;">Ligne horizontale</div>
                <div class="frmRow">
                    <label>Couleur:</label><br/>
                    <div class="choice_color" style="padding-left: 40px;margin-top: 5px;">
                        <input id="hr_gris_1" type="radio" name="hr_class" class="mceFocus" value="gris_efefef" /><label for="hr_gris_1">Gris clair</label><span class="hr_gris_1"></span><br/>
                        <input id="hr_gris_2" type="radio" name="hr_class" class="mceFocus" value="gris_dadada" /><label for="hr_gris_2">Gris foncé</label><span class="hr_gris_2"></span><br/>
                        <input id="hr_jaune" type="radio" name="hr_class" class="mceFocus" value="yellow" /><label for="hr_jaune">Jaune</label><span class="hr_jaune"></span><br/>
                        <input id="hr_vert" type="radio" name="hr_class" class="mceFocus" value="vert" /><label for="hr_vert">Vert</label><span class="hr_vert"></span><br/>
                    </div>
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
