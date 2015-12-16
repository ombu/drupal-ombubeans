<?php

/**
 * @file
 * Bean plugin object for bean feed.
 */

class BeanFeed extends BeanPlugin {
  /**
   * Implements BeanPlugin::values().
   */
  public function values() {
    $values = parent::values();

    $values += array(
      'feeds' => array(),
      'limit' => 10,
    );
    return $values;
  }

  /**
   * Implements BeanPlugin::form().
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    if (!isset($form_state['feed_count'])) {
      $form_state['feed_count'] = count($bean->feeds) + 1;
    }

    if (isset($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'add') {
      $form_state['feed_count']++;
    }

    $form['feeds'] = array(
      '#type' => 'fieldset',
      '#title' => 'Feeds',
      '#description' => t('The title(s) and URL(s) to the rss feed. If multiple URLs are added, then a select box will be presented to the site visitor allowing them to change which feed is displayed.'),
      '#prefix' => '<div id="feeds-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
    );

    for ($i = 0; $i < $form_state['feed_count']; $i++) {
      $form['feeds'][$i]['title'] = array(
        '#prefix' => '<div style="float: left; clear: both; padding-right: 10px;">',
        '#suffix' => '</div>',
        '#type' => 'textfield',
        '#title' => t('Feed !count - Title', array('!count' => ($i + 1))),
        '#required' => $i == 0 ? TRUE : FALSE,
        '#default_value' => isset($bean->feeds[$i]['title']) ? $bean->feeds[$i]['title'] : '',
      );
      $form['feeds'][$i]['url'] = array(
        '#prefix' => '<div style="float: left;">',
        '#suffix' => '</div>',
        '#type' => 'textfield',
        '#title' => t('URL'),
        '#required' => $i == 0 ? TRUE : FALSE,
        '#default_value' => isset($bean->feeds[$i]['url']) ? $bean->feeds[$i]['url'] : '',
      );
    }

    $form['add'] = array(
      '#prefix' => '<div style="clear: both">',
      '#suffix' => '</div>',
      '#type' => 'button',
      '#name' => 'add',
      '#value' => t('Add another feed'),
      '#ajax' => array(
        'callback' => 'bean_feed_ajax_callback',
        'wrapper' => 'feeds-wrapper',
      ),
      '#limit_validation_errors' => array(),
    );

    $form['limit'] = array(
      '#type' => 'select',
      '#title' => t('Item count'),
      '#description' => t('The number of items to display from the feed'),
      '#options' => drupal_map_assoc(range(1, 10), range(1, 10)),
      '#default_value' => $bean->limit,
    );

    return $form;
  }

  /**
   * Implements parent::validate().
   */
  public function validate($values, &$form_state) {
    // Unset empty url values.
    foreach ($form_state['values']['feeds'] as $key => $value) {
      if (empty($value['url'])) {
        unset($form_state['values']['feeds'][$key]);
      }
    }
  }

  /**
   * Implements BeanPlugin::submit().
   */
  function submit(Bean $bean) {
    if (isset($bean->bid)) {
      foreach ($bean->feeds as $feed) {
        cache_clear_all(md5('bean_feed_' . $bean->bid . '_' . $feed['url']), 'cache');
      }
    }
  }

  /**
   * Implements BeanPlugin::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {

    // Default to first feed.
    $feed = $bean->feeds[0];

    $items = BeanFeed::getFeedItems($bean, $feed['url']);

    if (!empty($items)) {

      $content['bean'][$bean->delta]['form'] = drupal_get_form('bean_feed_select_form', $bean);

      $content['bean'][$bean->delta]['#items'] = $items;

      // Allow bean styles to alter build.
      if (module_exists('bean_style')) {
        bean_style_view_alter($content, $bean);
      }
    }

    return $content;
  }

  /**
   * Returns feed items given an URL.
   *
   * @param BeanFeed $bean
   *   Bean object.
   * @param string $url
   *   The feed URL to query.
   *
   * @return array
   *   Array of loaded feed items.
   */
  static public function getFeedItems($bean, $url) {
    $items = array();

    // Load up the feed using Zend_Feed, which requires Zend_Loader_Autoloader.
    require_once drupal_get_path('module', 'bean_feed') . '/vendor/autoload.php';

    // Get cache values for this feed/count combo
    $cache_id = 'beanfeed:' . md5($url . '-count' . $bean->limit);
    if ($data = cache_get($cache_id)) {
      $data = $data->data;

      $items = array();
      foreach ($data['items'] as $item) {
        $item['item'] = json_decode($item['item']);
        $items[] = $item;
      }

      return $items;
    }

    try {
      $reader = new \PicoFeed\Reader\Reader();
      $resource = $reader->download($url);

      $parser = $reader->getParser(
        $resource->getUrl(),
        $resource->getContent(),
        $resource->getEncoding()
      );

      $feed = $parser->execute();

      $key = 0;
      while (isset($feed->items[$key]) && $key < $bean->limit) {
        $item = $feed->items[$key];
        $items[] = array(
          'bean' => $bean,
          'item' => $item,
          'link' => $item->getUrl(),
          'title' => $item->getTitle(),
          'description' => $item->getContent(),
          'date' => $item->getDate(),
        );
        $key++;
      }

      $data = array(
        'items' => array(),
      );
      // Serialize feed items as json, since simplexml doesn't support
      // serialization.
      foreach ($items as $item) {
        $item['item'] = json_encode($item['item']);
        $data['items'][] = $item;
      }
      cache_set($cache_id, $data, 'cache', CACHE_TEMPORARY);
    }
    catch (\PicoFeed\PicoFeedException $e) {
      watchdog('bean_feed', 'Invalid feed URI: !message', array('!message' => $e->getMessage()));
    }

    return $items;
  }
}
