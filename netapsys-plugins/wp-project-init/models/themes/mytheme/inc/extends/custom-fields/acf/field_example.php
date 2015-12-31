<?php
/**
 * Enregistrez des groupes de champs
 * La fonction register_field_group accepte 1 tableau qui contient les données nécessaire à l‘enregistrement d'un groupe de champs
 * Vous pouvez modifier ce tableau selon vos besoins. Cela peut toutefois provoquer des erreurs dans les cas où le tableau ne serait plus compatible avec ACF
 * Ce code doit être traité à chaque accès au fichier functions.php
 */

if(function_exists("register_field_group"))
{
  register_field_group(array (
    'id' => '528c75de0d90b',
    'title' => 'Club',
    'fields' =>
    array (
      0 =>
      array (
        'key' => 'field_10',
        'label' => 'Club',
        'name' => 'club',
        'type' => 'post_object',
        'order_no' => 0,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'post_type' =>
        array (
          0 => 'club',
        ),
        'taxonomy' =>
        array (
          0 => 'all',
        ),
        'allow_null' => 1,
        'multiple' => 0,
      ),
    ),
    'location' =>
    array (
      'rules' =>
      array (
        0 =>
        array (
          'param' => 'ef_user',
          'operator' => '==',
          'value' => 'licencie',
          'order_no' => 0,
        ),
        1 =>
        array (
          'param' => 'ef_user',
          'operator' => '==',
          'value' => 'licencie_archive',
          'order_no' => 1,
        ),
        2 =>
        array (
          'param' => 'ef_user',
          'operator' => '==',
          'value' => 'administrateur_club',
          'order_no' => 2,
        ),
      ),
      'allorany' => 'any',
    ),
    'options' =>
    array (
      'position' => 'normal',
      'layout' => 'default',
      'hide_on_screen' =>
      array (
      ),
    ),
    'menu_order' => 0,
  ));
  register_field_group(array (
    'id' => '528c75de0ef23',
    'title' => 'Comité Directeur',
    'fields' =>
    array (
      0 =>
      array (
        'key' => 'field_24',
        'label' => 'Président',
        'name' => 'president_club',
        'type' => 'repeater',
        'order_no' => 0,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
            ),
          ),
          'allorany' => 'all',
        ),
        'sub_fields' =>
        array (
          'field_36' =>
          array (
            'key' => 'field_36',
            'label' => 'Nom',
            'name' => 'nom',
            'type' => 'text',
            'order_no' => 0,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' =>
            array (
              'status' => 0,
              'allorany' => 'all',
              'rules' => 0,
            ),
            'column_width' => '',
            'default_value' => '',
            'formatting' => 'html',
          ),
          'field_37' =>
          array (
            'key' => 'field_37',
            'label' => 'Prénom',
            'name' => 'prenom',
            'type' => 'text',
            'order_no' => 1,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' =>
            array (
              'status' => 0,
              'allorany' => 'all',
              'rules' => 0,
            ),
            'column_width' => '',
            'default_value' => '',
            'formatting' => 'html',
          ),
        ),
        'row_min' => 1,
        'row_limit' => 1,
        'layout' => 'table',
        'button_label' => 'Ajouter',
      ),
      1 =>
      array (
        'key' => 'field_25',
        'label' => 'Secrétaire',
        'name' => 'secretaire_club',
        'type' => 'repeater',
        'order_no' => 1,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
            ),
          ),
          'allorany' => 'all',
        ),
        'sub_fields' =>
        array (
          'field_38' =>
          array (
            'key' => 'field_38',
            'label' => 'Nom',
            'name' => 'nom',
            'type' => 'text',
            'order_no' => 0,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' =>
            array (
              'status' => 0,
              'allorany' => 'all',
              'rules' => 0,
            ),
            'column_width' => '',
            'default_value' => '',
            'formatting' => 'html',
          ),
          'field_39' =>
          array (
            'key' => 'field_39',
            'label' => 'Prénom',
            'name' => 'prenom',
            'type' => 'text',
            'order_no' => 1,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' =>
            array (
              'status' => 0,
              'allorany' => 'all',
              'rules' => 0,
            ),
            'column_width' => '',
            'default_value' => '',
            'formatting' => 'html',
          ),
        ),
        'row_min' => 1,
        'row_limit' => 1,
        'layout' => 'table',
        'button_label' => '+ Ajouter un rang',
      ),
      2 =>
      array (
        'key' => 'field_26',
        'label' => 'Trésorier',
        'name' => 'tresorier_club',
        'type' => 'repeater',
        'order_no' => 2,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
            ),
          ),
          'allorany' => 'all',
        ),
        'sub_fields' =>
        array (
          'field_40' =>
          array (
            'key' => 'field_40',
            'label' => 'Nom',
            'name' => 'nom',
            'type' => 'text',
            'order_no' => 0,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' =>
            array (
              'status' => 0,
              'allorany' => 'all',
              'rules' => 0,
            ),
            'column_width' => '',
            'default_value' => '',
            'formatting' => 'html',
          ),
          'field_41' =>
          array (
            'key' => 'field_41',
            'label' => 'Prénom',
            'name' => 'prenom',
            'type' => 'text',
            'order_no' => 1,
            'instructions' => '',
            'required' => 0,
            'conditional_logic' =>
            array (
              'status' => 0,
              'allorany' => 'all',
              'rules' => 0,
            ),
            'column_width' => '',
            'default_value' => '',
            'formatting' => 'html',
          ),
        ),
        'row_min' => 1,
        'row_limit' => 1,
        'layout' => 'table',
        'button_label' => '+ Ajouter un rang',
      ),
    ),
    'location' =>
    array (
      'rules' =>
      array (
        0 =>
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'club',
          'order_no' => 0,
        ),
      ),
      'allorany' => 'all',
    ),
    'options' =>
    array (
      'position' => 'normal',
      'layout' => 'default',
      'hide_on_screen' =>
      array (
      ),
    ),
    'menu_order' => 0,
  ));
  register_field_group(array (
    'id' => '528c75de14804',
    'title' => 'Coordonnées',
    'fields' =>
    array (
      0 =>
      array (
        'key' => 'field_43',
        'label' => 'Adresse 1',
        'name' => 'adresse_1_club',
        'type' => 'text',
        'order_no' => 0,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      1 =>
      array (
        'key' => 'field_44',
        'label' => 'Adresse 2',
        'name' => 'adresse_2_club',
        'type' => 'text',
        'order_no' => 1,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      2 =>
      array (
        'key' => 'field_45',
        'label' => 'Adresse 3',
        'name' => 'adresse_3_club',
        'type' => 'text',
        'order_no' => 2,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      3 =>
      array (
        'key' => 'field_33',
        'label' => 'Code Postal',
        'name' => 'code_postal_club',
        'type' => 'text',
        'order_no' => 3,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      4 =>
      array (
        'key' => 'field_34',
        'label' => 'Ville ',
        'name' => 'ville_club',
        'type' => 'text',
        'order_no' => 4,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      5 =>
      array (
        'key' => 'field_35',
        'label' => 'Téléphone',
        'name' => 'telephone_club',
        'type' => 'text',
        'order_no' => 5,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      6 =>
      array (
        'key' => 'field_36',
        'label' => 'Mobile',
        'name' => 'mobile_club',
        'type' => 'text',
        'order_no' => 6,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      7 =>
      array (
        'key' => 'field_37',
        'label' => 'Fax',
        'name' => 'fax_club',
        'type' => 'text',
        'order_no' => 7,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      8 =>
      array (
        'key' => 'field_38',
        'label' => 'Site Internet',
        'name' => 'site_internet_club',
        'type' => 'text',
        'order_no' => 8,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      9 =>
      array (
        'key' => 'field_39',
        'label' => 'Email',
        'name' => 'email_club',
        'type' => 'text',
        'order_no' => 9,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      10 =>
      array (
        'key' => 'field_40',
        'label' => 'Ligue',
        'name' => 'ligue_club',
        'type' => 'select',
        'order_no' => 10,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'choices' =>
        array (
        ),
        'default_value' => '',
        'allow_null' => 0,
        'multiple' => 0,
      ),
      11 =>
      array (
        'key' => 'field_41',
        'label' => 'Comité Dép',
        'name' => 'comite_dep_club',
        'type' => 'text',
        'order_no' => 11,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      12 =>
      array (
        'key' => 'field_42',
        'label' => 'Club Labelisé',
        'name' => 'club_labelise_club',
        'type' => 'true_false',
        'order_no' => 12,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_55',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'message' => '',
        'default_value' => 0,
      ),
    ),
    'location' =>
    array (
      'rules' =>
      array (
        0 =>
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'club',
          'order_no' => 0,
        ),
      ),
      'allorany' => 'all',
    ),
    'options' =>
    array (
      'position' => 'normal',
      'layout' => 'default',
      'hide_on_screen' =>
      array (
      ),
    ),
    'menu_order' => 0,
  ));
  register_field_group(array (
    'id' => '528c75de1e90b',
    'title' => 'Informations « licencié »',
    'fields' =>
    array (
      0 =>
      array (
        'key' => 'field_47',
        'label' => 'Nom',
        'name' => 'nom',
        'type' => 'text',
        'order_no' => 0,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_14',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      1 =>
      array (
        'key' => 'field_48',
        'label' => 'Prénom',
        'name' => 'prenom',
        'type' => 'text',
        'order_no' => 1,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_14',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      2 =>
      array (
        'key' => 'field_13',
        'label' => 'Date de naissance',
        'name' => 'date_de_naissance_licencie',
        'type' => 'date_picker',
        'order_no' => 2,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'date_format' => 'yymmdd',
        'display_format' => 'dd/mm/yy',
      ),
      3 =>
      array (
        'key' => 'field_14',
        'label' => 'Sexe',
        'name' => 'sexe_licencie',
        'type' => 'select',
        'order_no' => 3,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'choices' =>
        array (
          'f' => 'F',
          'm' => 'M',
        ),
        'default_value' => '',
        'allow_null' => 0,
        'multiple' => 0,
      ),
      4 =>
      array (
        'key' => 'field_15',
        'label' => 'Adresse 1',
        'name' => 'adresse_1_licencie',
        'type' => 'textarea',
        'order_no' => 4,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'br',
      ),
      5 =>
      array (
        'key' => 'field_16',
        'label' => 'Adresse 2',
        'name' => 'adresse_2_licencie',
        'type' => 'textarea',
        'order_no' => 5,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'br',
      ),
      6 =>
      array (
        'key' => 'field_17',
        'label' => 'Adresse 3',
        'name' => 'adresse_3_licencie',
        'type' => 'textarea',
        'order_no' => 6,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'br',
      ),
      7 =>
      array (
        'key' => 'field_18',
        'label' => 'Code postal',
        'name' => 'code_postal_licencie',
        'type' => 'text',
        'order_no' => 7,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      8 =>
      array (
        'key' => 'field_19',
        'label' => 'Code INSEE',
        'name' => 'code_insee_licencie',
        'type' => 'text',
        'order_no' => 8,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      9 =>
      array (
        'key' => 'field_20',
        'label' => 'Ville',
        'name' => 'ville_licencie',
        'type' => 'text',
        'order_no' => 9,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      10 =>
      array (
        'key' => 'field_21',
        'label' => 'Pays',
        'name' => 'pays_licencie',
        'type' => 'text',
        'order_no' => 10,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      11 =>
      array (
        'key' => 'field_23',
        'label' => 'Telephone',
        'name' => 'telephone_licencie',
        'type' => 'text',
        'order_no' => 11,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_15',
              'operator' => '==',
              'value' => 'f',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
    ),
    'location' =>
    array (
      'rules' =>
      array (
        0 =>
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'licencie',
          'order_no' => 0,
        ),
      ),
      'allorany' => 'any',
    ),
    'options' =>
    array (
      'position' => 'normal',
      'layout' => 'default',
      'hide_on_screen' =>
      array (
      ),
    ),
    'menu_order' => 0,
  ));
  register_field_group(array (
    'id' => '528c75de2859c',
    'title' => 'Media Actualité',
    'fields' =>
    array (
      0 =>
      array (
        'key' => 'field_8',
        'label' => 'Video',
        'name' => 'video_actualite',
        'type' => 'file',
        'order_no' => 0,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'save_format' => 'id',
      ),
      1 =>
      array (
        'key' => 'field_9',
        'label' => 'Photo',
        'name' => 'photo_actualite',
        'type' => 'image',
        'order_no' => 1,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'save_format' => 'id',
        'preview_size' => 'thumbnail',
      ),
    ),
    'location' =>
    array (
      'rules' =>
      array (
        0 =>
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'actualite',
          'order_no' => 0,
        ),
      ),
      'allorany' => 'all',
    ),
    'options' =>
    array (
      'position' => 'normal',
      'layout' => 'default',
      'hide_on_screen' =>
      array (
      ),
    ),
    'menu_order' => 0,
  ));
  register_field_group(array (
    'id' => '528c75de2abd3',
    'title' => 'Tarifs',
    'fields' =>
    array (
      0 =>
      array (
        'key' => 'field_7',
        'label' => 'Tarifs',
        'name' => 'tarifs',
        'type' => 'number',
        'order_no' => 0,
        'instructions' => '',
        'required' => 1,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
      ),
    ),
    'location' =>
    array (
      'rules' =>
      array (
        0 =>
        array (
          'param' => 'ef_taxonomy',
          'operator' => '==',
          'value' => 'type_licence',
          'order_no' => 0,
        ),
      ),
      'allorany' => 'all',
    ),
    'options' =>
    array (
      'position' => 'normal',
      'layout' => 'no_box',
      'hide_on_screen' =>
      array (
      ),
    ),
    'menu_order' => 0,
  ));
  register_field_group(array (
    'id' => '528c75de2c6c4',
    'title' => 'WebContact',
    'fields' =>
    array (
      0 =>
      array (
        'key' => 'field_27',
        'label' => 'Nom',
        'name' => 'nom_webcontact',
        'type' => 'text',
        'order_no' => 0,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      1 =>
      array (
        'key' => 'field_32',
        'label' => 'Club Employeur',
        'name' => 'club_employeur_webcontact',
        'type' => 'select',
        'order_no' => 1,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'field_47',
              'operator' => '==',
              'value' => 1,
            ),
          ),
          'allorany' => 'all',
        ),
        'choices' =>
        array (
          0 => 'non',
          1 => 'oui',
        ),
        'default_value' => '',
        'allow_null' => 0,
        'multiple' => 0,
      ),
      2 =>
      array (
        'key' => 'field_28',
        'label' => 'Prénom',
        'name' => 'prenom_webcontact',
        'type' => 'text',
        'order_no' => 2,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      3 =>
      array (
        'key' => 'field_29',
        'label' => 'Email',
        'name' => 'email_webcontact',
        'type' => 'text',
        'order_no' => 3,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      4 =>
      array (
        'key' => 'field_30',
        'label' => 'Téléphone 1',
        'name' => 'telephone_1_webcontact',
        'type' => 'text',
        'order_no' => 4,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
      5 =>
      array (
        'key' => 'field_31',
        'label' => 'Téléphone 2',
        'name' => 'telephone_2_webcontact',
        'type' => 'text',
        'order_no' => 5,
        'instructions' => '',
        'required' => 0,
        'conditional_logic' =>
        array (
          'status' => 0,
          'rules' =>
          array (
            0 =>
            array (
              'field' => 'null',
              'operator' => '==',
              'value' => '',
            ),
          ),
          'allorany' => 'all',
        ),
        'default_value' => '',
        'formatting' => 'html',
      ),
    ),
    'location' =>
    array (
      'rules' =>
      array (
        0 =>
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'club',
          'order_no' => 0,
        ),
      ),
      'allorany' => 'all',
    ),
    'options' =>
    array (
      'position' => 'normal',
      'layout' => 'default',
      'hide_on_screen' =>
      array (
      ),
    ),
    'menu_order' => 0,
  ));
}
