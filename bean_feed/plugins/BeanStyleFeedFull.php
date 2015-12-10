<?php

/**
 * @file
 * Full feed bean style.
 */

class BeanStyleFeedFull extends BeanStyle {

  /**
   * Implements parent::prepareView().
   */
  public function prepareView($build, $bean) {
    $items = $build['#items'];

    $rendered_items = array();
    foreach ($items as $item) {
      $rendered_items[] = theme('bean_feed_item', $item);
    }

    // Remove items so bean can be properly cached, since XML object can't be
    // serialized.
    unset($build['#items']);

    $build['feed'] = array(
      '#theme' => 'item_list',
      '#items' => $rendered_items,
      '#attributes' => array(
        'id' => 'bean-feed-wrapper-' . $bean->delta,
      ),
    );

    return $build;
  }
}
