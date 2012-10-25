<?php
/**
 * @file
 * FeaturedBean
 */

/**
 * Featured Content Bean.
 *
 * Placeholder class.  The link field is applied to the bean via features.
 */
class FeaturedBean extends BeanPlugin {

  /**
   * Implements the values method for this class
   */
  public function values() {
    $values = parent::values();
    $values += array(
      'content_display_mode' => 'teaser',
    );
    return $values;
  }

  /**
   * Implements the form method for this class
   */
  public function form($bean, $form, &$form_state) {

    // Pull in all node view types.
    $content_display_mode_options = array();
    $entity_info = entity_get_info('node');
    foreach ($entity_info['view modes'] as $machine_name => $info) {
      if ($info['custom settings'] === TRUE) {
        $content_display_mode_options[$machine_name] = $info['label'];
      }
    }
    $form['content_display_mode'] = array(
      '#title' => t('Display content as'),
      '#type' => 'select',
      '#options' => $content_display_mode_options,
      '#description' => 'Select how you would like the content to be displayed',
      '#default_value' => $bean->content_display_mode,
    );

    return $form;
  }

  /**
   * Implements the view method for this class
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $node_view_mode = $bean->content_display_mode;
    foreach (element_children($content['bean'][$bean->bid]['field_featured_content']) as $i) {
      $node = $content['bean'][$bean->bid]['field_featured_content'][$i]['#options']['entity'];
      $content['bean'][$bean->bid]['field_featured_content'][$i] = node_view($node, $node_view_mode);
    }
    return $content;
  }

}
