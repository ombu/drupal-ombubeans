<?php

/**
 * @file
 * Embed bean.
 */

class EmbedBean extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();

    $values += array(
      'url' => '',
      'height' => 500,
    );

    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {
    $form['url'] = array(
      '#type' => 'textfield',
      '#title' => t('URL'),
      '#description' => t('Enter the URL to load within this iFrame'),
      '#default_value' => isset($bean->url) ? $bean->url : '',
      '#maxlength' => 500,
    );

    $form['height'] = array(
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#description' => t('Enter the height of this iframe in pixels'),
      '#default_value' => isset($bean->height) ? $bean->height : '',
    );

    return $form;
  }

  /**
   * Implements parent::validate().
   */
  public function validate($values, &$form_state) {
    if (!empty($values['url']) && !valid_url($values['url'])) {
      form_set_error('url', t('Please enter a valid URL'));
    }
    if ($values['height'] && !is_numeric($values['height'])) {
      form_set_error('height', t('Please enter a numeric height (e.g. 500)'));
    }
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    // Allow bean styles to alter build.
    if (module_exists('bean_style')) {
      bean_style_view_alter($content, $bean);
    }

    return $content;
  }
}
