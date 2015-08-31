<?php

/**
 * @file
 * FPO Hero block bean.
 */

class FPOHeroBean extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();
    $values += array(
      'body'  => 'Hello World',
      'width'  => '12',
      'color'  => 'blue',
    );
    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $form['body'] = array(
      '#title' => t('Body'),
      '#type' => 'textarea',
      '#required' => TRUE,
      '#default_value' => $bean->body,
      '#description' => t('In px'),
    );

    return $form;
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $content['bean'][$bean->delta][] = array(
        '#markup' => sprintf(<<<EOF
<div class="jumbotron">
  %s
</div>
EOF
    , $bean->body),
    );
    return $content;
  }
}
