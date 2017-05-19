<?php

/**
 * @file
 * Basic carousel style.
 */

class BeanStyleBasicCarousel extends CarouselBeanStyle {
  protected $type = 'callout_carousel';

  /**
   * Implements parent::prepareView().
   */
  public function prepareView($build, $bean) {
    $build = parent::prepareView($build, $bean);

    $type = $this->bean->type;
    switch ($type) {
      case 'bean_callout':
        $build['field_callout_item'] = array(
          '#theme' => $this->theme_function,
          '#items' => $this->items,
          '#type' => $this->type,
        );
        unset($build['nodes']);

        $item_count = count($build['field_callout_item']['#items']);

        // Build callout field collection items into bootstrap rows.
        $build['field_callout_item']['#prefix'] = '<div class="callout-basic-grid" data-item-count="' . $item_count . '">';
        $build['field_callout_item']['#suffix'] = '</div>';
        break;
    }

    return $build;
  }

  /**
   * Implements parent::prepareItems().
   */
  public function prepareItems($build, $type) {
    parent::prepareItems($build, $type);

    // Build items differently depending on bean type.
    switch ($type) {
      case 'bean_callout':
        $this->prepareFieldCollectionItems($build);
        break;
    }
  }

  /**
   * Prepare items from a field collection for rendering in a slideshow.
   */
  protected function prepareFieldCollectionItems($build) {
    foreach (element_children($build['field_callout_item']) as $delta) {
      $this->items[] = $build['field_callout_item'][$delta];
    }
  }
}
