<?php

/**
 * @file
 * Iframe bean style.
 */

class BeanStyleIframe extends BeanStyle {
  public function prepareView($build, $bean) {
    $build = parent::prepareView($build, $bean);
    if (isset($bean->url) && !empty($bean->url)) {
      $build[$bean->delta]['iframe'] = array(
        '#markup' => t('<iframe id="!id" frameborder="0" src="!src" width="!width" height="!height"></iframe>', array(
          '!src' => $bean->url,
          '!width' => isset($bean->width) ? $bean->width : '100%',
          '!height' => isset($bean->height) ? $bean->height : '100%',
          '!id' => $bean->delta . '-iframe',
        )),
      );
    }

    return $build;
  }
}
