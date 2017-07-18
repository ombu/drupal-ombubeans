<?php

/**
 * @file
 * Media Bean.
 */

class MediaBean extends BeanPlugin {
  /**
   * Implements the view method for this class
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    if (isset($content['bean'][$bean->delta]['field_image_link']['#items'][0]['url']) && isset($content['bean'][$bean->delta]['field_image_link'])) {
      $link_target = '';
      $link_url = '';

      // Get URL.
      $url = $content['bean'][$bean->delta]['field_image_link']['#items'][0]['url'];
      $link_url = url($url, array('absolute' => TRUE));

      // Get if opening in new window.
      if (isset($content['bean'][$bean->delta]['field_image_link']['#items'][0]['attributes']['target'])) {
        $target = $content['bean'][$bean->delta]['field_image_link']['#items'][0]['attributes']['target'];
        if ($target != '') {
          $link_target = 'target="_blank"';
        }
      }

      unset($content['bean'][$bean->delta]['field_image_link']);
      $content['bean'][$bean->delta]['field_bean_media']['#prefix'] = '<a href="' . $link_url  . '" ' . $link_target . ' />';
      $content['bean'][$bean->delta]['field_bean_media']['#suffix'] = '</a>';
    }
    return $content;
  }
}
