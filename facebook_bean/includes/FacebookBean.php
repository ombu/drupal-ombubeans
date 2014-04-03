<?php

/**
 * @file
 * Facebook bean.
 */

class FacebookBean extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();
    $values += array(
      'urls' => array(),
      'width' => '300',
      'height' => '',
      'header' => TRUE,
      'stream' => TRUE,
      'faces' => FALSE,
      'border' => FALSE,
    );
    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    if (!variable_get('facebook_api_key', FALSE)) {
      drupal_set_message(t('You must <a href="!url">set a valid Facebook API key (in the services group)</a> before the Facebook block will function.', array(
        '!url' => '/admin/siteconfig',
      )));
      return $form;
    }

    if (!isset($form_state['url_count'])) {
      $form_state['url_count'] = count($bean->urls) + 1;
    }

    if (isset($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'add') {
      $form_state['url_count']++;
    }

    $form['urls'] = array(
      '#type' => 'fieldset',
      '#title' => 'Facebook Pages',
      '#description' => t('The title(s) and URL(s) to Facebook pages. If multiple URLs are added, then a select box will be presented to the site visitor allowing them to change which page is displayed.'),
      '#prefix' => '<div id="urls-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    );

    for ($i = 0; $i < $form_state['url_count']; $i++) {
      $form['urls'][$i]['title'] = array(
        '#prefix' => '<div style="float: left; clear: both; padding-right: 10px;">',
        '#suffix' => '</div>',
        '#type' => 'textfield',
        '#title' => t('Page !count - Title', array('!count' => ($i + 1))),
        '#required' => $i == 0 ? TRUE : FALSE,
        '#default_value' => isset($bean->urls[$i]['title']) ? $bean->urls[$i]['title'] : '',
      );
      $form['urls'][$i]['url'] = array(
        '#prefix' => '<div style="float: left;">',
        '#suffix' => '</div>',
        '#type' => 'textfield',
        '#title' => t('URL'),
        '#description' => t('The absolute URL to the facebook page'),
        '#required' => $i == 0 ? TRUE : FALSE,
        '#default_value' => isset($bean->urls[$i]['url']) ? $bean->urls[$i]['url'] : '',
      );
    }

    $form['add'] = array(
      '#prefix' => '<div style="clear: both">',
      '#suffix' => '</div>',
      '#type' => 'button',
      '#name' => 'add',
      '#value' => t('Add another page'),
      '#ajax' => array(
        'callback' => 'facebook_bean_ajax_callback',
        'wrapper' => 'urls-wrapper',
      ),
      '#limit_validation_errors' => array(),
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

  /**
   * Implements parent::validate().
   */
  public function validate($values, &$form_state) {
    // Unset empty url values.
    foreach ($form_state['values']['urls'] as $key => $value) {
      if (empty($value['url'])) {
        unset($form_state['values']['urls'][$key]);
      }
    }
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    // Trigger inclusion of FB code.
    global $conf;
    $conf['facebook_code'] = TRUE;

    $content['bean'][$bean->delta]['form'] = drupal_get_form('facebook_bean_select_form', $bean);

    $content['bean'][$bean->delta]['like-box'] = FacebookBean::getLikeBox($bean->urls[0]['url'], $bean);

    return $content;
  }

  /**
   * Generate build array for facebook like box.
   */
  static public function getLikeBox($url, $bean) {
    return array(
      '#prefix' => '<div class="facebook-bean-wrapper" id="facebook-bean-wrapper-' . $bean->delta . '">',
      '#suffix' => '</div>',
      '#markup' => t('<div class="fb-like-box" data-href="!url" data-width="!width" !height data-show-faces="!faces" data-header="!header" data-stream="!stream" data-show-border="!border"></div>', array(
        '!url' => $url,
        '!width' => $bean->width,
        '!height' => $bean->height ? 'data-height="' . $bean->height . '"' : '',
        '!faces' => $bean->faces ? 'true' : 'false',
        '!header' => $bean->header ? 'true' : 'false',
        '!stream' => $bean->stream ? 'true' : 'false',
        '!border' => $bean->border ? 'true' : 'false',
      )),
    );
  }
}
