<?php
/**
 * @file
 * ombubeans.bean.inc
 */

/**
 * Base bean class for all stand beans.
 */
class ombubeans_base extends bean_plugin {
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    return $content;
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
    $content['bean'][$bean->bid][] = array(
        '#markup' => sprintf('<a class="thumbnail" href="#">%d</a>', $bean->width),
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
  <h1>%s</h1>
  <p>%s</p>
</div>
EOF
    , $bean->body, $bean->body),
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
      'handle' => 'ladygaga',
    );
    return $values;
  }

  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $form['handle'] = array(
      '#title' => t('Twitter Handle'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $bean->handle,
      '#description' => t('Enter the twitter handle for this block.'),
    );
    return $form;
  }

  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $handle = $bean->handle;

    $twitter_js = <<<EOD
      <script src="http://widgets.twimg.com/j/2/widget.js"></script>
      <script type="text/javascript">
      new TWTR.Widget({
        version: 2,
        type: 'profile',
        rpp: 10,
        interval: 30000,
        theme: {
          shell: {
            background: '#ffffff',
            color: '#ffffff'
          },
          tweets: {
            background: '#ffffff',
          }
        },
        features: {
          scrollbar: true,
          loop: false,
          live: false,
          behavior: 'all'
        }
      }).render().setUser('$handle').start();
      </script>
EOD;

    $content['twitter_feed'] = array(
      '#markup' => $twitter_js,
      '#attached' => array(
        'js' => array(
          'http://widgets.twimg.com/j/2/widget.js' => array('type' => 'external'),
        ),
      ),
    );
    return $content;
  }
}

/**
 * Social Media Links bean.
 */
class ombubeans_sociallinks extends ombubeans_base {

  public $services = array('Facebook', 'Twitter', 'Pinterest', 'YouTube', 'RSS');

  public function values() {
    $values = parent::values();
    foreach ($this->services as $service) {
      $values[$service] = '';
    };
    return $values;
  }

  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    foreach ($this->services as $service) {
      $form[$service] = array(
        '#title' => t('@service Handle', array('@service' => $service)),
        '#type' => 'textfield',
        '#default_value' => empty($bean->$service) ? '' : $bean->$service,
        '#description' => t('The @service handle for this block.',
          array('@service' => $service)),
      );
    };
    return $form;
  }

  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    foreach ($this->services as $service) {
      if(empty($bean->$service)) continue;
      $content['bean'][$bean->bid][$service] = array(
        '#theme' => 'link',
        '#text' => $service,
        '#path' => $bean->$service,
        '#options' => array(
          'attributes' => array(
            'class' => strtolower($service),
          ),
          'html' => true,
        ),
      );
    }
    return $content;
  }
}
