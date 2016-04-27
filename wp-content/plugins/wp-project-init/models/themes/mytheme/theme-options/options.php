<?php
/**
 * Options du theme
 *
 * ajouter/modifier ici les options du themes
 */

global $mytheme_options_settings;

$mytheme_options_settings = array(
  'Configuration générale'  => array(
      'logo' => array(
        'label' => 'Logo du site',
        'type' => 'image',
        'description' => '',
      ),
      'favicon' => array(
        'label' => 'Favicon',
        'type' => 'image',
        'description' => '',
      ),
      'copyright' => array(
        'label' => 'Copyright',
        'type' => 'text',
        'description' => '',
      )
  ),
  'Réseaux sociaux'         => array(
      'facebook' => array(
        'label' => 'Facebook',
        'type' => 'url',
        'description' => '',
      ),
      'twitter' => array(
        'label' => 'Twitter',
        'type' => 'url',
        'description' => '',
      ),
      'googleplus' => array(
        'label' => 'Google +1',
        'type' => 'url',
        'description' => '',
      ),
      'linkedin' => array(
        'label' => 'Linked In',
        'type' => 'url',
        'description' => '',
      ),
      'pinterest' => array(
        'label' => 'Pinterest',
        'type' => 'url',
        'description' => '',
      ),
  ),
  'Page d\'accueil'         => array(
      'nombrearticle' => array(
        'label' => 'Nombre d\'articles mis en avant',
        'type' => 'select',
        'options' => array(
          5 => '5 articles',
          10 => '10 articles',
          20 => '20 articles'
        ),
        'description' => 'Séléctionnez le nombre d\'articles mis en avant sur la page d\'accueil',
      ),
  ),
  'Analytics'               => array(
      'ga' => array(
        'label' => 'Google analytics',
        'type' => 'textarea',
        'description' => 'Ajouter ici le script de google analytics',
      ),
  )
);