<?php

/**
 * @file
 * Callout Bean.
 */

class BeanCallout extends BeanPlugin {
  /**
   * Implements parent::submit().
   */
  public function submit(Bean $bean) {
    // Alter bean style based on selected smartphone behavior.
    if ($bean->bean_style == 'basic') {
      $behavior = field_get_items('bean', $bean, 'field_smartphone_behavior');
      if (!empty($behavior[0]['value']) && $behavior[0]['value'] != 'stacked') {
        $bean->bean_style .= '_' . $behavior[0]['value'];
      }
    }
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $content = parent::view($bean, $content, $view_mode, $langcode);

    // Let any bean styles alter content.
    if (module_exists('bean_style')) {
      bean_style_view_alter($content, $bean);
    }

    return $content;
  }
}
