<?php
/**
 * @file
 * bean_media.fields.inc
 */

function bean_media_get_default_fields() {
  $fields = array();

  // Exported field: 'bean-bean_media-field_featured_content'.
  $fields['bean-bean_media-field_bean_media'] = array(
    'field_config' => array(
      'active' => 1,
      'cardinality' => 1,
      'deleted' => 0,
      'entity_types' => array(),
      'field_name' => 'field_bean_media',
      'indexes' => array(
        'fid' => array(
          0 => 'fid',
        ),
      ),
      'locked' => 0,
      'module' => 'ombumedia',
      'settings' => array(),
      'translatable' => 0,
      'type' => 'ombumedia',
    ),
    'field_instance' => array(
      'bundle' => 'bean_media',
      'deleted' => 0,
      'description' => '',
      'display' => array(
        'default' => array(
          'label' => 'hidden',
          'module' => 'ombumedia',
          'settings' => array(),
          'type' => 'ombumedia_render',
          'weight' => 0,
        ),
      ),
      'entity_type' => 'bean',
      'field_name' => 'field_bean_media',
      'label' => 'Media',
      'required' => 0,
      'settings' => array(
        'file_extensions' => 'jpg jpeg gif png txt doc docx xls xlsx pdf ppt pptx pps ppsx odt ods odp mp3 mov mp4 m4a m4v mpeg avi ogg oga ogv wmv ico',
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
            'document' => 'document',
            'image' => 'image',
            'video' => 'video',
          ),
          'allowed_view_modes' => array(
            'audio' => array(
              'default' => 'default',
            ),
            'document' => array(
              'default' => 'default',
            ),
            'image' => array(
              'default' => 'default',
            ),
            'video' => array(
              'default' => 'default',
            ),
          ),
        ),
        'type' => 'ombumedia',
        'weight' => 31,
      ),
    ),
  );

  return $fields;
}
