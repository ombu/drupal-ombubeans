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
    //kpr($bean); exit;
    $content['bean'][$bean->bid][] = array(
        '#markup' => sprintf('<a class="thumbnail" style="height:%dpx; line-height:%dpx;" href="#">%d</a>', $bean->height, $bean->height, $bean->width),
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
        width: 'auto',
        theme: {
          shell: {
            background: 'transparent',
            color: '#333333'
          },
          tweets: {
            background: 'transparent'
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
class ombubeans_sociallinks extends ombubeans_base {}

/**
 * Solr Query bean.
 */
class OmbubeansSolrQuery extends ombubeans_base {

  public function values() {
    $values = parent::values();
    $values['params'] = '';
    $values['results_view_mode'] = 'teaser';
    $values['use_pager'] = FALSE;
    return $values;
  }

  public function form($bean, $form, &$form_state) {

    $form['solr'] = array(
      '#title' => t('Solr Query'),
      '#type' => 'fieldset',
      '#collapsible' => FALSE,
    );

    $default_ini = <<<HEREDOC
rows = 10
fq = 'bundle:article'
sort = 'bs_sticky desc, ds_created desc'
HEREDOC;

    $form['solr']['params'] = array(
      '#title' => t('Params'),
      '#type' => 'textarea',
      '#default_value' => !empty($bean->params) ? $bean->params : $default_ini,
      '#description' => "Enter your solr params in ini format.",
    );

    $entity_info = entity_get_info('node');
    $view_mode_options = array();
    foreach ($entity_info['view modes'] as $machine_name => $info) {
      if ($info['custom settings'] === TRUE) {
        $view_mode_options[$machine_name] = $info['label'];
      }
    }
    $form['solr']['results_view_mode'] = array(
      '#title' => t('Results View Mode'),
      '#type' => 'select',
      '#options' => $view_mode_options,
      '#description' => 'Select how you would like the node results to be displayed',
    );

    $form['solr']['use_pager'] = array(
      '#title' => t('Use Pager'),
      '#type' => 'checkbox',
      '#default_value' => !empty($bean->use_pager) ? $bean->use_pager : FALSE,
    );

    return $form;
  }

  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {

    $params = parse_ini_string($bean->params);
    $results = apachesolr_search_run('apachesolr', $params, '', '', pager_find_page());

    $content['bean'][$bean->bid]['solr'] = array();
    foreach ($results as $array) {
      $node = node_load($array['node']->entity_id);
      $content['bean'][$bean->bid]['solr'][] = node_view($node, $bean->results_view_mode);
    }

    if ($bean->use_pager) {
      $content['bean'][$bean->bid]['pager'] = array(
        '#theme' => 'pager',
        'tags' => NULL,
      );
    }

    return $content;
  }

}
