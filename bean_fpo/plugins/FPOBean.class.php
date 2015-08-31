<?php

/**
 * @file
 * FPO block bean.
 */


class FPOBean extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();
    $values += array(
      'height' => 100,
    );
    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $form['height'] = array(
      '#title' => t('Height'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $bean->height,
      '#description' => t('In px'),
    );

    return $form;
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $width = tiles_get_width('bean', $bean->delta);

    $content['bean'][$bean->delta][] = array(
      '#markup' => sprintf('<img style="width: 100%%; min-height:%spx" src="http://placehold.it/600x%d&text=FPO"
      class="img-polaroid">', $bean->height, $bean->height, $width),
    );
    return $content;
  }
}
