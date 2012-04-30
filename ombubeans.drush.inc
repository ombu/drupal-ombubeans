<?php

/**
 * @file
 * Drush support for beans.
 */

/**
 * Implements hook_drush_command().
 */
function ombubeans_drush_command() {
  $items['bean-export'] = array(
    'description' => dt('Export whole bean objects.'),
    'arguments' => array(
      'delta' => dt('The bean delta to export.'),
    ),
    'aliases' => array('be'),
  );
  return $items;
}

/**
 * Export whole bean objects.
 */
function drush_ombubeans_bean_export($delta) {
  $bean = bean_load_delta($delta);
  $output = '$bean = bean_create(array(\'type\' => \'' . $bean->type . '\'));' . "\n";

  foreach (array('label', 'title', 'delta') as $property) {
    $output .= '$bean->' . $property . ' = \'' . $bean->$property . '\'' . ";\n";
  }

  $output .= '$bean->setValues(' . var_export($bean->data, TRUE) . ')' . ";\n";

  // Export fields.
  $fields = field_info_instances('bean', $bean->type);
  foreach ($fields as $key => $info) {
    switch ($info['widget']['type']) {
      // @todo: handle images and files.
      // @todo: handle field collections.
      default:
        $output .= '$bean->' . $key . ' = ' . var_export($bean->$key, TRUE) . ";\n";
        break;
    }
  }
  $output .= 'bean_save($bean);';

  drush_print($output);
}
