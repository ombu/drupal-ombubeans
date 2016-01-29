<?php

/**
 * @file
 * Basic bean style.
 */

class BeanStyleBasic extends BeanStyle {
  public function prepareView($build, $bean) {
    //parent::prepareView($build, $bean);
    $build = parent::prepareView($build, $bean);

    // Get a count of callout items.
    $item_count = isset($build['field_callout_item']) ? count(element_children($build['field_callout_item'])) : 0;

    // Wrap callouts in grid wrapper
    $build['field_callout_item']['#prefix'] = '<div class="callout-basic-grid" data-item-count="' . $item_count . '">';
    $build['field_callout_item']['#suffix'] = '</div>';

    if (isset($build['field_image'])) {
      $build['field_image']['#access'] = FALSE;
    }



    return $build;
  }
}
