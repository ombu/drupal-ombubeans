<?php
/**
 * @file
 * bean_featured.fields.inc
 */

function bean_featured_get_default_fields() {
  $fields = array();

  // Exported field: 'bean-bean_featured-field_featured_content'.
  $fields['bean-bean_featured-field_featured_content'] = array(
    'field_config' => array(
      'active' => 1,
      'cardinality' => -1,
      'deleted' => 0,
      'entity_types' => array(),
      'field_name' => 'field_featured_content',
      'foreign keys' => array(
        'node' => array(
          'columns' => array(
            'target_id' => 'nid',
          ),
          'table' => 'node',
        ),
      ),
      'indexes' => array(
        'target_id' => array(
          0 => 'target_id',
        ),
      ),
      'locked' => 0,
      'module' => 'entityreference',
      'settings' => array(
        'handler' => 'base',
        'handler_settings' => array(
          'behaviors' => array(
            'views-select-list' => array(
              'status' => 0,
            ),
          ),
          'sort' => array(
            'direction' => 'ASC',
            'property' => 'title',
            'type' => 'property',
          ),
          'target_bundles' => array(),
        ),
        'target_type' => 'node',
      ),
      'translatable' => 0,
      'type' => 'entityreference',

    ),
    'field_instance' => array(
      'bundle' => 'bean_featured',
      'default_value' => NULL,
      'deleted' => 0,
      'description' => 'Select one or more pieces of content to feature.  Start typing the title of the content and it will be listed in the autocomplete field.',
      'display' => array(
        'default' => array(
          'label' => 'hidden',
          'module' => 'entityreference',
          'settings' => array(
            'link' => 1,
          ),
          'type' => 'entityreference_label',
          'weight' => 0,
        ),
      ),
      'entity_type' => 'bean',
      'field_name' => 'field_featured_content',
      'label' => 'Featured Content',
      'required' => 0,
      'settings' => array(
        'user_register_form' => FALSE,
      ),
      'widget' => array(
        'active' => 1,
        'module' => 'entityreference',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'path' => '',
          'size' => 60,
        ),
        'type' => 'entityreference_autocomplete',
        'weight' => 2,
      ),
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('Featured Content');

  return $fields;
}
