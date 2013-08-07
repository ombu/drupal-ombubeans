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
  public function values() {
    $values = parent::values();
    $values += array(
      'widget' => '',
    );
    return $values;
  }

  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $form['widget'] = array(
      '#title' => t('Twitter widget'),
      '#type' => 'textarea',
      '#required' => TRUE,
      '#default_value' => $bean->widget,
      '#description' => t('Enter the code for the custom twitter widget.  You can create a new one <a href="https://twitter.com/settings/widgets">here</a>.  For more help creating a custom widget, visit the <a href="https://dev.twitter.com/docs/embedded-timelines#customization">Twitter documentation</a>'),
    );

    return $form;
  }

  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $content['bean'][$bean->delta]['widget'] = array(
      '#markup' => $bean->widget,
    );

    return $content;
  }
}
