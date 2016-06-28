<?php
/**
 * configuration
 */

global $jpress_jm_offre_fields, $jpress_jm_candidature_fields, $jpress_jm_societe_fields, $jpress_jm_capabilities;

//champs offres
$jpress_jm_offre_fields = array(
    array(
        'attribut'  => 'salaire',
        'metakey'   => 'salaire',
        'label'     => 'Salaire',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'expire',
        'metakey'   => 'expire',
        'label'     => 'Date d\'expiration',
        'type'      => 'date',
        'admin_column'  => true,
        'enable'        => true,
    ),
);

$jpress_jm_candidature_fields = array(
    array(
        'attribut'  => 'civilite',
        'metakey'   => 'civilite',
        'label'     => 'Civilité',
        'type'      => 'select',
        'options'   => array(
            'Mr' => 'Monsieur',
            'Mme' => 'Madame',
            'Mlle' => 'Mademoiselle',
        ),
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'nom',
        'metakey'   => 'nom',
        'label'     => 'Nom',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'prenom',
        'metakey'   => 'prenom',
        'label'     => 'Prénom',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'email',
        'metakey'   => 'email',
        'label'     => 'Email',
        'type'      => 'mail',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'telephone',
        'metakey'   => 'telephone',
        'label'     => 'Téléphone',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'mobile',
        'metakey'   => 'mobile',
        'label'     => 'Mobile',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'adresse',
        'metakey'   => 'adresse',
        'label'     => 'Adresse',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'ville',
        'metakey'   => 'ville',
        'label'     => 'Ville',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'code_postal',
        'metakey'   => 'code_postal',
        'label'     => 'Code Postal',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'pays',
        'metakey'   => 'pays',
        'label'     => 'Pays',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'lettre_motivation',
        'metakey'   => 'lettre-motivation',
        'label'     => 'Lettre de motivation',
        'type'      => 'file',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'cv',
        'metakey'   => 'cv',
        'label'     => 'CV',
        'type'      => 'file',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'description',
        'metakey'   => 'description',
        'label'     => 'Description',
        'type'      => 'wysiwyg',
        'admin_column'  => false,
        'enable'        => true,
    ),
);


//champs societe
$jpress_jm_societe_fields = array(
    array(
        'attribut'  => 'adresse',
        'metakey'   => 'adresse',
        'label'     => 'Adresse',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'ville',
        'metakey'   => 'ville',
        'label'     => 'Ville',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'code_postal',
        'metakey'   => 'code-postal',
        'label'     => 'Code postal',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'pays',
        'metakey'   => 'pays',
        'label'     => 'Pays',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'responsable',
        'metakey'   => 'responsable',
        'label'     => 'Responsable',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'activites',
        'metakey'   => 'activites',
        'label'     => 'Activités',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'effectif',
        'metakey'   => 'effectif',
        'label'     => 'Effectif',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'contact',
        'metakey'   => 'contact',
        'label'     => 'Contact',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'telephone',
        'metakey'   => 'telephone',
        'label'     => 'Téléphone',
        'type'      => 'text',
        'admin_column'  => true,
        'enable'        => true,
    ),
    array(
        'attribut'  => 'email',
        'metakey'   => 'email',
        'label'     => 'Email',
        'type'      => 'mail',
        'admin_column'  => true,
        'enable'        => true,
    ),
);


//capabilities
$jpress_jm_capabilities  = array(
    "read_%s",
    "read_private_%ss",
    "edit_%s",
    "edit_%ss",
    "edit_others_%ss",
    "edit_private_%ss",
    "edit_published_%ss",
    "publish_%ss",
    "delete_%s",
    "delete_%ss",
    "delete_private_%ss",
    "delete_published_%ss",
    "delete_others_%ss",
    "manage_term_%s"
);