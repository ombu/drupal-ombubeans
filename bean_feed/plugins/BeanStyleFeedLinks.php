<?php

/**
 * @file
 * Links feed bean style.
 */

class BeanStyleFeedLinks extends BeanStyle {
  /**
   * Implements parent::prepareView().
   */
  public function prepareView($build, $bean) {
    $items = array();
    foreach ($build['#items'] as $item) {
      $items[] = l($item['title'], $item['link']);
    }

    $build['feed'] = array(
      '#theme' => 'item_list',
      '#items' => $items,
      '#attributes' => array(
        'id' => 'bean-feed-wrapper-' . $bean->delta,
      ),
    );

    return $build;
  }
}
