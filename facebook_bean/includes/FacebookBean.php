<?php

/**
 * @file
 * Facebook bean.
 */

class FacebookBean extends BeanPlugin {
  public function values() {
    $values = parent::values();
    $values += array(
      'url' => '',
      'width' => '300',
      'height' => '',
      'header' => TRUE,
      'stream' => TRUE,
      'faces' => FALSE,
      'border' => FALSE,
    );
    return $values;
  }

  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $form['url'] = array(
      '#title' => t('Page URL'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $bean->url,
      '#description' => t('The absolute URL of the Facebook Page'),
    );

    $form['width'] = array(
      '#type' => 'textfield',
      '#title' => t('Width'),
      '#default_value' => $bean->width,
      '#description' => t('The width of the plugin in pixels'),
    );

    $form['height'] = array(
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#default_value' => $bean->height,
      '#description' => t('The height of the plugin in pixels'),
    );

    $form['header'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show Header'),
      '#default_value' => $bean->header,
      '#description' => t('Whether to display the Facebook header at the top of the plugin.'),
    );

    $form['stream'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show stream'),
      '#default_value' => $bean->stream,
      '#description' => t("Whether to display a stream of the latest posts from the Page's wall"),
    );

    $form['faces'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show faces'),
      '#default_value' => $bean->faces,
      '#description' => t('Whether or not to display profile photos in the plugin'),
    );

    $form['border'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show border'),
      '#default_value' => $bean->border,
      '#description' => t('Whether or not to show a border around the plugin'),
    );

    return $form;
  }

  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    // Trigger inclusion of FB code.
    global $conf;
    $conf['facebook_code'] = TRUE;

    $content['bean'][$bean->delta]['like-box'] = array(
      '#markup' => t('<div class="fb-like-box" data-href="!url" data-width="!width" !height data-show-faces="!faces" data-header="!header" data-stream="!stream" data-show-border="!border"></div>', array(
        '!url' => $bean->url,
        '!width' => $bean->width,
        '!height' => $bean->height ? 'data-height="' . $bean->height . '"' : '',
        '!faces' => $bean->faces ? 'true' : 'false',
        '!header' => $bean->header ? 'true' : 'false',
        '!stream' => $bean->stream ? 'true' : 'false',
        '!border' => $bean->border ? 'true' : 'false',
      )),
    );

    return $content;
  }
}
