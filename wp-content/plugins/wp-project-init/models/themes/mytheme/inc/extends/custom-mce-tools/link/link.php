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

$all_post = get_posts(array(
    "post_type" => "any",
    "orderby" => "name",
    "numberposts" => -1,
    "order" => "ASC"
));

?>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Lien vers une page</title>
        <script type="text/javascript" src="../jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="../tiny_mce_popup.js"></script>
        <script type="text/javascript" src="link_rule.js"></script>
        <script type="text/javascript">
            
            jQuery(document).ready(function(){
                jQuery('.list-post li').click(function(){
                    var _this = jQuery(this);
                    var _link = _this.find('.item-permalink').val();
                    var _title = _this.find('.item-title').text();
                    jQuery('#frmbody #url_link').val(_link);
                    jQuery('#frmbody #titre_link').val(_title);
                });
            })
            
        </script>
        <style>
        #all-post ul { 
            height: 176px;
            list-style-type: none;
            border: 1px solid gray;
            padding: 0;
            margin: 0;
        }
        #all-post ul li { clear: both;
            margin-bottom: 0;
            border-bottom: 1px solid #f1f1f1;
            color: #333;
            padding: 4px 6px 4px 10px;
            cursor: pointer;
            position: relative; 
        }
        #all-post ul li.selected {
            background: #ddd;
            color: #333;
        }
        #all-post ul li span.item-title {
            display: inline-block;
            width: 80%;
            width: -webkit-calc(100% - 68px);
            width: calc(100% - 68px);
        }
        #all-post ul li span.item-info { 
            text-transform: uppercase;
            color: #666;
            font-size: 11px;
            position: absolute;
            right: 5px;
            top: 5px;
        }
         #frmbody #insert, #frmbody #cancel, .choice_color input, #frmbody fieldset label {
            cursor: pointer;
        }
        .choice_color span {
            display: inline-block;
            width: 80%;
            height: 10px;
        }
        .choice_color .link_vert {
            background: #117c36;
            margin-left: 28px;
        }
        .choice_color .link_jaune {
            background: #ffdd00;
            margin-left: 20px;
        }
        </style>
    </head>
    <body>
        <form onSubmit="media_linkDialog.update();return false;" action="#" style="padding: 0 1em;">
            <div id="frmbody">
                <div class="title" style="text-align: center;font-size: 24px;margin-bottom: 10px;">Lien vers une page</div>
                <div class="frmRow">
                    <div style="margin-bottom: 10px;">
                        <label>Url externe:</label><input type="text" id="url_link" name="url_link" class="mceFocus" style="width: 84%;float: right;" />
                    </div>
                    <div style="clear: both;margin-bottom: 10px;">
                        <label>Texte sur le lien:</label><input type="text" id="titre_link" name="titre_link" class="mceFocus" style="width: 77%;float: right;" />
                    </div>
                    <fieldset class="choice_color" style="margin-top: 5px;clear: both;">
                        <label>Couleur de l'affichage:</label><br/>
                        <input id="link_jaune" type="radio" name="media_link" class="mceFocus" value="btnJaune noBorder clearfix" /><label for="link_jaune">Jaune</label><span class="link_jaune"></span><br/>
                        <input id="link_vert" type="radio" name="media_link" class="mceFocus" value="btnVert" /><label for="link_vert">Vert</label><span class="link_vert"></span><br/>
                    </fieldset>
                    <br />
                    <?php if ( !empty($all_post) && count($all_post) > 0 ) : ?>
                        <div id="all-post" class="all-post" tabindex="0">
                            <label>Url vers des pages internes:</label><br/>
                            <ul class="list-post" <?php if ( count($all_post) > 5 ) echo 'style="overflow: scroll;"'; ?>>
                                <?php foreach ( $all_post as $i => $post ) : ?>
                                    <li <?php if ( $i % 2 == 0 ) echo 'style="background: #f9f9f9;"'; ?>>
                                        <input type="hidden" class="item-permalink" value="<?php echo get_permalink($post->ID); ?>">
                                        <span class="item-title"><?php echo $post->post_title; ?></span>
                                        <span class="item-info"><?php echo date("d/m/Y", strtotime($post->post_date)); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <fieldset style="margin-top: 10px;">
                        <label>Option supplémentaire:</label><br/>
                        <input id="target_link" type="checkbox" name="target_link" class="mceFocus" value="_blank" /><label for="target_link">Ouvrir dans un nouvel onglet</label><br/>
                    </fieldset>
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
