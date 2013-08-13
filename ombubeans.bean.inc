<?php
/**
 * @file
 * ombubeans.bean.inc
 */

/**
 * Base bean class for all stand beans.
 */
class ombubeans_base extends BeanPlugin {
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    return $content;
  }
}

class ombubeans_color extends BeanPlugin {

  public function values() {
    $values = parent::values();
    $values['color'] = 'pink';
    return $values;
  }

  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);
    $form['color'] = array(
      '#type' => 'select',
      '#title' => t('Color'),
      '#options' => array(
        'blue' => 'Blue',
        'light-blue' => 'Light Blue',
        'orange' => 'Orange'
      ),
      '#default_value' => !empty($bean->color) ? $bean->color : 'blue',
      '#description' => t('Select the main color for this block.'),
    );
    return $form;
  }
}


/**
 * FPO block bean.
 */
class ombubeans_fpo extends ombubeans_base {
  public function values() {
    $values = parent::values();
    $values += array(
      'height' => 100,
    );
    return $values;
  }

  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $form['height'] = array(
      '#title' => t('Height '),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $bean->height,
      '#description' => t('In px'),
    );

    return $form;
  }

  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $context = tiles_get_context('path', current_path());
    $width = tiles_get_width('bean', $bean->delta, $context->name);

    $content['bean'][$bean->bid][] = array(
      '#markup' => sprintf('<img style="width: 100%%; min-height:%spx" src="http://placehold.it/600x%d&text=FPO"
      class="img-polaroid">', $bean->height, $bean->height, $width),
    );
    return $content;
  }
}

/**
 * FPO Hero block bean.
 */
class ombubeans_fpohero extends ombubeans_base {
  public function values() {
    $values = parent::values();
    $values += array(
      'body'  => 'Hello World',
      'width'  => '12',
      'color'  => 'blue',
    );
    return $values;
  }

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

  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $content['bean'][$bean->bid][] = array(
        '#markup' => sprintf(<<<EOF
<div class="hero-unit">
  %s
</div>
EOF
    , $bean->body),
    );
    return $content;
  }
}

/**
 * Twitter feed bean.
 */
class ombubeans_twitter extends ombubeans_base {
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
        'callback' => 'ombubeans_twitter_bean_ajax_callback',
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
    $content['bean'][$bean->delta]['form'] = drupal_get_form('ombubeans_twitter_bean_select_form', $bean);

    $content['bean'][$bean->delta]['widget'] = array(
      '#prefix' => '<div id="twitter-widget-wrapper-' . $bean->delta . '">',
      '#suffix' => '</div>',
      '#markup' => $bean->widgets[0]['widget'],
    );

    return $content;
  }
}
