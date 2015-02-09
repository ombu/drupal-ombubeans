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
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $featured_content = field_get_items('bean', $bean, 'field_featured_content');

    if ($featured_content) {
      // Ensure that all nodes are published before printing.
      foreach ($featured_content as $key => $value) {
        if (!$value['entity']->status) {
          unset($featured_content[$key]);
        }
      }

      $content['bean'][$bean->delta]['#featured_content'] = $featured_content;

      // Allow bean styles to alter build.
      if (module_exists('bean_style')) {
        bean_style_view_alter($content, $bean);
      }
    }

    return $content;
  }
}
