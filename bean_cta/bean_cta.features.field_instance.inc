<?php
/**
 * @file
 * bean_cta.features.field_instance.inc
 */

/**
 * Implements hook_field_default_field_instances().
 */
function bean_cta_field_default_field_instances() {
  $field_instances = array();

  // Exported field_instance: 'bean-bean_cta-field_cta_image'
  $field_instances['bean-bean_cta-field_cta_image'] = array(
    'bundle' => 'bean_cta',
    'deleted' => 0,
    'description' => 'Optional background image when displaying this CTA block in stinger style.',
    'display' => array(
      'default' => array(
        'label' => 'above',
        'settings' => array(),
        'type' => 'hidden',
        'weight' => 2,
      ),
    ),
    'entity_type' => 'bean',
    'field_name' => 'field_cta_image',
    'label' => 'Background image',
    'required' => 0,
    'settings' => array(
      'alt_field' => 0,
      'default_image' => 0,
      'file_directory' => '',
      'file_extensions' => 'png gif jpg jpeg',
      'max_filesize' => '',
      'max_resolution' => '',
      'min_resolution' => '',
      'title_field' => 0,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'ombumedia',
      'settings' => array(
        'allowed_schemes' => array(
          'oembed' => 0,
          'public' => 'public',
        ),
        'allowed_types' => array(
          'audio' => 0,
          'document' => 0,
          'image' => 'image',
          'video' => 0,
        ),
        'allowed_view_modes' => array(
          'audio' => array(
            0 => 'full',
          ),
          'document' => array(
            0 => 'full',
          ),
          'image' => array(
            0 => 'full',
          ),
          'video' => array(
            0 => 'full',
          ),
        ),
      ),
      'type' => 'ombumedia',
      'weight' => 1,
    ),
  );

  // Exported field_instance: 'bean-bean_cta-field_cta_link'
  $field_instances['bean-bean_cta-field_cta_link'] = array(
    'bundle' => 'bean_cta',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => '',
    'display' => array(
      'default' => array(
        'label' => 'hidden',
        'module' => 'link',
        'settings' => array(),
        'type' => 'link_default',
        'weight' => 1,
      ),
    ),
    'entity_type' => 'bean',
    'field_name' => 'field_cta_link',
    'label' => 'Link',
    'required' => 0,
    'settings' => array(
      'absolute_url' => 1,
      'attributes' => array(
        'class' => '',
        'configurable_class' => 0,
        'configurable_title' => 0,
        'rel' => '',
        'target' => 'default',
        'title' => '',
      ),
      'display' => array(
        'url_cutoff' => 80,
      ),
      'enable_tokens' => 1,
      'rel_remove' => 'default',
      'title' => 'optional',
      'title_label_use_field_label' => 0,
      'title_maxlength' => 128,
      'title_value' => '',
      'url' => 0,
      'user_register_form' => FALSE,
      'validate_url' => 1,
    ),
    'widget' => array(
      'active' => 0,
      'module' => 'link',
      'settings' => array(),
      'type' => 'link_field',
      'weight' => 4,
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('Image');
  t('Link');
  t('Optional background image when displaying this CTA block in stinger style.');

  return $field_instances;
}
