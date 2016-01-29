<?php

/**
 * @file
 * Call To Action Bean.
 */

class BeanCTA extends BeanPlugin {
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
