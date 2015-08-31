<?php

/**
 * @file
 * Twitter feed bean.
 */

class BeanTwitter extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();
    $values += array(
      'widgets' => array(),
    );
    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    if (!isset($form_state['widget_count'])) {
      $form_state['widget_count'] = count($bean->widgets) + 1;
    }

    if (isset($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'add') {
      $form_state['widget_count']++;
    }

    $form['widgets'] = array(
      '#type' => 'fieldset',
      '#title' => 'Twitter widget',
      '#description' => t('The title(s) and twitter widget(s). If multiple widgets are added, then a select box will be presented to the site visitor allowing them to change which twitter feed is displayed.'),
      '#prefix' => '<div id="widgets-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    );

    for ($i = 0; $i < $form_state['widget_count']; $i++) {
      $form['widgets'][$i]['title'] = array(
        '#prefix' => '<div style="float: left; clear: both; padding-right: 10px;">',
        '#suffix' => '</div>',
        '#type' => 'textfield',
        '#title' => t('Widget !count - Title', array('!count' => ($i + 1))),
        '#required' => $i == 0 ? TRUE : FALSE,
        '#default_value' => isset($bean->widgets[$i]['title']) ? $bean->widgets[$i]['title'] : '',
      );
      $form['widgets'][$i]['widget'] = array(
        '#prefix' => '<div style="float: left;">',
        '#suffix' => '</div>',
        '#type' => 'textarea',
        '#title' => t('Twitter Widget Code'),
        '#description' => t('Enter the code for the custom twitter widget.  You can create a new one <a href="https://twitter.com/settings/widgets">here</a>.  For more help creating a custom widget, visit the <a href="https://dev.twitter.com/docs/embedded-timelines#customization">Twitter documentation</a>'),
        '#required' => $i == 0 ? TRUE : FALSE,
        '#default_value' => isset($bean->widgets[$i]['widget']) ? $bean->widgets[$i]['widget'] : '',
      );
    }

    $form['add'] = array(
      '#prefix' => '<div style="clear: both">',
      '#suffix' => '</div>',
      '#type' => 'button',
      '#name' => 'add',
      '#value' => t('Add another widget'),
      '#ajax' => array(
        'callback' => 'bean_twitter_bean_ajax_callback',
        'wrapper' => 'widgets-wrapper',
      ),
      '#limit_validation_errors' => array(),
    );

    return $form;
  }

  /**
   * Implements parent::validate().
   */
  public function validate($values, &$form_state) {
    // Unset empty widget values.
    foreach ($form_state['values']['widgets'] as $key => $value) {
      if (empty($value['widget'])) {
        unset($form_state['values']['widgets'][$key]);
      }
    }
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $content['bean'][$bean->delta]['form'] = drupal_get_form('bean_twitter_bean_select_form', $bean);

    $content['bean'][$bean->delta]['widget'] = array(
      '#prefix' => '<div id="twitter-widget-wrapper-' . $bean->delta . '">',
      '#suffix' => '</div>',
      '#markup' => $bean->widgets[0]['widget'],
      '#attached' => array(
        'js' => array(
          drupal_get_path('module', 'bean_twitter') . '/js/bean-twitter.drupal.js',
        ),
      ),
    );

    return $content;
  }
}
