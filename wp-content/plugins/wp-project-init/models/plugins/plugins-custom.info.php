<?php
/**
 * Configurer ici la liste des plugins custom
 */

global $wppi_plugins_custom;
define ("WPPI_PLUGIN_CUSTOM_BASE_URL", 'http://netapsys:N6ct2K3x@netapsys-press.netapsys.fr/wp-content/plugins/');

$wppi_plugins_custom = array(
  'Structure et Champs personnalisés' => array(
    'advanced-custom-fields-url-field-add-on',
    'advanced-custom-fields-widget-field-add-on',
    'wp-repeater-widget',
  ),
  'Miscelaneous' => array(
    'apply-script',
    'jpress-archive',
    'jpress-admin-column-search',
    'acf-repeater-collapser-admin',
  ),
  'Performance & Security' => array(
    'gestion-de-cache',
    'jpress-create-post-table',
    'jpress-encrypt-plugins-and-themes',
    'jpress-zone-cache',
  ),
  'Media' => array(
    'image-classes-select',
    'jpress-gallery',

  ),
  'Utilitaires' => array(
    'jenkins-press',
    'jpress-video-thumbnail-generator',
    'pdf-flipbook',
    'pdf-flipbook-imagemagik',
    'wp-ajax-upload',
    'wp-autocomplete-action',
    'wp-infinite-loading',
    'wp-pagination-loading',
    'wp-progress-action',
    'wp-project-init',
  ),
  'Import, Export & Migration' => array(
    'jpress-import-excel',
    'wp-export-posts',
  ),
);




