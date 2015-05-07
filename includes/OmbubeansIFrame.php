<?php

/**
 * @file
 * iFrame Bean.
 */

class OmbubeansIFrame extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();

    $values += array(
      'url' => '',
      'width' => 500,
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
      '#required' => TRUE,
    );

    $form['width'] = array(
      '#type' => 'textfield',
      '#title' => t('Width'),
      '#description' => t('Enter the width of this iframe in pixels'),
      '#default_value' => isset($bean->width) ? $bean->width : '',
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
    if (!valid_url($values['url'])) {
      form_set_error('url', t('Please enter a valid URL'));
    }
    if ($values['width'] && !is_numeric($values['width'])) {
      form_set_error('width', t('Please enter a numeric width (e.g. 500)'));
    }
    if ($values['height'] && !is_numeric($values['height'])) {
      form_set_error('height', t('Please enter a numeric height (e.g. 500)'));
    }
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    if (isset($bean->url)) {
      $content['bean'][$bean->delta]['iframe'] = array(
        '#markup' => t('<iframe frameborder="0" src="!src" width="!width" height="!height"></iframe>', array(
          '!src' => $bean->url,
          '!width' => isset($bean->width) ? $bean->width : '100%',
          '!height' => isset($bean->height) ? $bean->height : '100%',
        )),
      );
    }

    return $content;
  }
}
