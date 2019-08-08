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
    $featured_home_header = field_get_items('bean', $bean, 'field_featured_home_header');
    $featured_home_content = field_get_items('bean', $bean, 'field_featured_home_content');

    if ($featured_content || $featured_home_header || $featured_home_content) {
      // Ensure that all nodes are published before printing.
      if ($featured_content) {
        foreach ($featured_content as $key => $value) {
          if (!$value['entity']->status) {
            unset($featured_content[$key]);
          }
        }
        $content['bean'][$bean->delta]['#featured_content'] = $featured_content;
      }

      if ($featured_home_header) {
        foreach ($featured_home_header as $key => $value) {
          if (!$value['entity']->status) {
            unset($featured_home_header[$key]);
          }
        }
        $content['bean'][$bean->delta]['#featured_home_header'] = $featured_home_header;
      }

      if ($featured_home_content) {
        foreach ($featured_home_content as $key => $value) {
          if (!$value['entity']->status) {
            unset($featured_home_content[$key]);
          }
        }
        $content['bean'][$bean->delta]['#featured_home_content'] = $featured_home_content;
      }

      // Allow bean styles to alter build.
      if (module_exists('bean_style')) {
        bean_style_view_alter($content, $bean);
      }

      if ($bean->bean_style == 'callouthome') {
        unset($content['bean'][$bean->delta]['#featured_content']);
        unset($content['bean'][$bean->delta]['field_featured_content']);
        $content['bean'][$bean->delta]['field_featured_home_header']['#weight'] = -10;
        $content['bean'][$bean->delta]['field_featured_home_content']['#weight'] = 10;
      }
      else {
        unset($content['bean'][$bean->delta]['#featured_home_header']);
        unset($content['bean'][$bean->delta]['field_featured_home_header']);
        unset($content['bean'][$bean->delta]['#featured_home_content']);
        unset($content['bean'][$bean->delta]['field_featured_home_content']);
      }

    }

    return $content;
  }
}
