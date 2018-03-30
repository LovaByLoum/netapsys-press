<?php
/**
 * api function to render fields
 */
function acf_render_field( $data, $name, $type, $args = array() ){
  if ( $type == 'repeater' && isset($args['parent']) && !empty($args['parent']) ){
    foreach ( $args['fields'] as $k => $fld ){
      $args['fields'][$k]['name'] = $name . '__' . $args['fields'][$k]['name'];
    }
  }

  switch ( $type ){
    case 'wysiwyg' : render_acf_wysiwyg( $data, $name, $args ); break;
    case 'url_object' : render_acf_url_object( $data, $name, $args ); break;
    case 'true_false' : render_acf_true_false( $data, $name ); break;
    case 'select' : render_acf_select( $data, $name, $args ); break;
    case 'image' : render_acf_image( $data, $name ); break;
    case 'text' : render_acf_text( $data, $name, $args ); break;
    case 'textarea' : render_acf_textarea( $data, $name, $args ); break;
    case 'repeater' : render_acf_repeater( $data, $name, $args ); break;
    case 'color' : render_acf_color( $data, $name, $args ); break;
  }
}

function render_acf_wysiwyg( $data, $name, $args =array() ){
    $fields = array(
        'id'           => 'editor',
        'name'		   => $name,
        '_name'		   => $name,
        'type'		   => 'wysiwyg',
        'value'		   =>  $data,
        'toolbar'      => true,
        'media_upload' => true,
    );
    ob_start();
    do_action('acf/create_field', $fields, array());
    $html = ob_get_clean();

    if ( isset($args['acfcloneindex']) && $args['acfcloneindex'] ) {
        echo preg_replace('!wysiwyg-editor-([A-Za-z0-9]+)!', 'wysiwig-editor-acfcloneindex', $html);
    } else {
        echo $html;
    }
}

function render_acf_url_object( $data, $name, $args ) {
  $link_object = json_decode($data);

  $post_types = isset($args['post_types']) ? $args['post_types'] : array( 'post', 'page');
  $taxonomies = isset($args['taxonomies']) ? $args['taxonomies'] : array( 'category', 'post_tag' );
  $fieldid = 'link' . rand(0,100000);
  $fields = array(
    'id'          => $fieldid,
    'name'				=> $name,
    '_name'				=> $name,
    'type'				=> 'url_object',
    'value'				=> array(
      'target' => $link_object->target,
      'link' => isset($link_object->link) ? $link_object->link : '',
      'label' => $link_object->label,
    ),
    'class' => 'url_object',
    'post_type' => $post_types,
    'taxonomy' => $taxonomies,
  );
  do_action('acf/create_field', $fields, array());
}

function render_acf_select ( $data, $name, $args = array() ){
  $fieldid = 'select' . rand(0,100000);
  $fields = array(
    'id'          => $fieldid,
    'name'				=> $name,
    'type'				=> 'select',
    'value'				=> $data,
    'choices'     =>  $args['choices']
  );
  do_action('acf/create_field', $fields, array());
}

function render_acf_color( $data, $name, $args = array() ){
	do_action('acf/create_field', array(
		'type'			=>	'color_picker',
		'name'			=>	$name,
		'value'			=>	 $data,
		'placeholder'	=>	'#ffffff'
	));

}

function render_acf_true_false( $data, $name ){
  $fieldid = 'truefalse' . rand(0,100000);
  $fields = array(
    'id'          => $fieldid,
    'name'				=> $name,
    'type'				=> 'true_false',
    'value'				=> $data,
  );
  do_action('acf/create_field', $fields, array());
}

function render_acf_image( $data, $name ){
  do_action('acf/create_field', array(
    'type'	=>	'image',
    'name'	=>	$name,
    'save_format' => 'id',
    'preview_size' => 'thumbnail',
    'value'	=>	$data,
  ));
}

function render_acf_text( $data, $name, $args ){
  do_action('acf/create_field', array(
    'type'	=>	'text',
    'name'	=>	$name,
    'value'	=>	$data,
    'placeholder' => isset($args['placeholder']) ? $args['placeholder'] : '',
  ));
}

function render_acf_textarea( $data, $name, $args ){
  do_action('acf/create_field', array(
    'type'	=>	'textarea',
    'name'	=>	$name,
    'value'	=>	$data,
  ));
  if ( isset($args['description']) ){
    echo '<em>' . $args['description'] . '</em>';
  }
}

function render_acf_repeater( $alldata, $name, $args ){
  if ( !isset($args['parent']) ){
    $data = get_field_acf_repeater_widget($alldata, $args);
  } else {
    $data = $alldata;
  }

  $fields = $args['fields'];
  ?>
  <div class="field_type-repeater">
    <div class="repeater">
      <table class="widefat acf-input-table row_layout">
        <tbody class="ui-sortable">
        <?php
        $count = 1;
        if ( !empty($data) ): foreach ( $data as $key => $instance) : ?>
          <tr class="row">
            <td class="order ui-sortable-handle"><?php echo $count;?></td>
            <td class="acf_input-wrap">
              <table class="widefat acf_input">
                <?php if ( !empty($fields) ): foreach ( $fields as $k => $field) : ?>
                <tr>
                  <?php if ( isset( $field['args'] ) && isset( $field['args']['label'] ) ) : ?>
                  <td class="label"><label><?php echo $field['args']['label'];?></label></td>
                  <?php endif;?>
                  <td>
                    <?php
                    if ( isset($args['parent']) ){
                      $field['args']['parent'] = $name;
                      list($parent_name, $initial_field_name) = explode('__',$field['name']);
                      $value = $instance[$initial_field_name];
                    } else {
                      $value = $instance[$field['name']];
                    }
                    acf_render_field( $value, $field['name'] . '-' . $key , $field['type'], $field['args'] );
                    ?>
                  </td>
                </tr>
                <?php
                $count ++;
                endforeach; endif;?>
              </table>
            </td>
            <td class="remove">
              <a class="acf-button-add add-row-before" href="#" style="margin-top: -358.5px;"></a>
              <a class="acf-button-remove" href="#"></a>
            </td>
          </tr>
        <?php endforeach; endif;?>

        <tr class="row-clone">
          <td class="order ui-sortable-handle">1</td>
          <td class="acf_input-wrap">
            <table class="widefat acf_input">
              <?php if ( !empty($fields) ): foreach ( $fields as $ky => $field) : ?>
              <tr>
                <?php if ( isset( $field['args'] ) && isset( $field['args']['label'] ) ) : ?>
                  <td class="label"><label><?php echo $field['args']['label'];?></label></td>
                <?php endif;?>
                <td>
                  <?php
                  if ( isset($args['parent']) ){
                    $field['args']['parent'] = $name;
                  }
                  if ( $field['type'] == "color" ) {
                      acf_render_field( '', $field['name'] . '-acfcloneindex][acfcloneindex', $field['type'], $field['args'] );
                  } elseif ( $field['type'] == "wysiwyg" ){
                      $field['args']['acfcloneindex'] = true;
	                  acf_render_field( '', $field['name'] . '-acfcloneindex][acfcloneindex', $field['type'], $field['args'] );
                  } else{
	                  acf_render_field( '', $field['name'] . '-acfcloneindex', $field['type'], $field['args'] );
                  }

                  ?>
                </td>
              </tr>
              <?php endforeach; endif;?>
            </table>
          </td>
          <td class="remove">
            <a class="acf-button-add add-row-before" href="#" style="margin-top: -358.5px;"></a>
            <a class="acf-button-remove" href="#"></a>
          </td>
        </tr>

        </tbody>
      </table>
      <ul class="hl clearfix repeater-footer">
        <li class="right">
          <a href="#" class="add-row-end acf-button button button-primary" data-event="add-row">Ajouter <?php echo $args['label'];?></a>
        </li>
      </ul>
    </div>
  </div>
  <?php
}

function get_field_acf_repeater_widget( $alldata, $args = array() ){
    if ( !empty($args) ){
        $fields_in_repeater = array_map(create_function('$a', 'return $a["name"];'), $args['fields']);
        //filter all repeater data to only matched fields of current repeater
        foreach( $alldata as $k => $dt ){
            if ( !preg_match('!(' . implode('|', $fields_in_repeater) . ')\-([0-9]+)!', $k) ){
                unset($alldata[$k]);
            }
        }
    }

  $data = array();
  foreach( $alldata as $k => $dt ){
    //multiple repeater
    if ( preg_match('!(.*?)__(.*?)$!', $k, $matches) && strpos($k, 'choice_') === false && strpos($k, 'acfcloneindex') === false ){
      $parent = $matches[1];
      $fldname = $matches[2];
      if ( preg_match('!(.*?)-([0-9]+)$!', $parent, $matches2) ) {
        $fldname2 = $matches2[1];
        $id2 = $matches2[2];

        $data2 = array();
        $data2[$fldname] = $dt;
        if ( !is_array($data[$id2]) ) $data[$id2] = array();
        if ( !is_array($data[$id2][$fldname2]) ) $data[$id2][$fldname2] = array();
        $new_data = get_field_acf_repeater_widget($data2);
        foreach ( $new_data as $kk => $arr ){
          foreach ( $arr as $kkk => $vl ){
            $data[$id2][$fldname2][$kk][$kkk] = $vl;
          }
        }
      }

    }elseif ( strpos($k, 'choice_') === false && strpos($k, 'acfcloneindex') === false ){
      if ( preg_match('!(.*?)-([0-9]+)$!', $k, $matches) ) {
        $fldname = $matches[1];
        $id = $matches[2];
        $data[$id][$fldname] = $dt;
      }
    }
  }
  return $data;
}