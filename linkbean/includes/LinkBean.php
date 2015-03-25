<?php
/**
 * @file
 * LinkBean
 */

/**
 * Links bean.
 *
 * Placeholder class.  The link field is applied to the bean via features.
 */
class LinkBean extends BeanPlugin {

  /**
   * Implements the values method for this class
   */
  public function values() {
    $values = parent::values();
    $values['results_view_mode'] = 'default';
    return $values;
  }

  /**
   * Implements the form method for this class
   */
  public function form($bean, $form, &$form_state) {
    $form['results_view_mode'] = array(
      '#title' => t('Links View Mode'),
      '#type' => 'select',
      '#options' => array(
        'default' => 'Inline',
        'block' => 'List',
        'ul' => 'Bullet List',
      ),
      '#default_value' => !empty($bean->results_view_mode) ? $bean->results_view_mode : 'default',
      '#description' => 'Select how you would like the links to be displayed.',
    );
    return $form;
  }

  /**
   * Implements the view method for this class
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {

    foreach ($content['bean'] as $entity_id => $array) {

      if ($bean->results_view_mode === 'ul') {
        $links = array();
        foreach (element_children($array['field_linkbean_links']) as $i) {
          $link = $array['field_linkbean_links'][$i];
          $links[] = drupal_render($link);
        }
        $content['bean'][$entity_id]['field_linkbean_links'] = array(
          '#theme' => 'item_list',
          '#items' => $links,
        );
      }
      elseif ($bean->results_view_mode === 'block') {
        foreach (element_children($array['field_linkbean_links']) as $i) {
          $content['bean'][$entity_id]['field_linkbean_links'][$i]['#prefix'] = '<div>';
          $content['bean'][$entity_id]['field_linkbean_links'][$i]['#suffix'] = '</div>';
        }
      }
    }

    return $content;
  }

}
