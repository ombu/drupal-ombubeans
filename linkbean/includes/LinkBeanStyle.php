<?php

/**
 * @file
 * Link Bean style.
 */

class LinkBeanStyle extends BeanStyle {
  /**
   * Implements parent::prepareView().
   */
  public function prepareView($build, $bean) {
    parent::prepareView($build, $bean);

    $build['field_linkbean_links'] = array(
      '#theme' => 'item_list',
      '#items' => $this->items,
      '#attributes' => array(
        'class' => array(drupal_html_class(get_class($this))),
      ),
    );

    return $build;
  }

  /**
   * Implements parent::prepareItems().
   */
  protected function prepareItems($build, $type) {
    foreach (element_children($build['field_linkbean_links']) as $child) {
      $this->items[] = drupal_render($build['field_linkbean_links'][$child]);
    }
  }
}
