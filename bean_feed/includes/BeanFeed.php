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
      'title_only' => FALSE,
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

    $form['feed_urls']['add'] = array(
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
      '#options' => range(1, 10),
      '#default_value' => $bean->limit,
    );

    $form['title_only'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show only title'),
      '#description' => t('Hide feed item body and only show title.'),
      '#default_value' => $bean->title_only,
    );

    return $form;
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

      $content['bean'][$bean->bid]['form'] = drupal_get_form('bean_feed_select_form', $bean);

      $rendered_items = array();
      foreach ($items as $item) {
        $rendered_items[] = theme('bean_feed_item', $item);
      }

      // @todo: make this a dedicated theme function, instead of relying on
      // theme_item_list().
      $content['bean'][$bean->bid]['items'] = array(
        '#theme' => 'item_list',
        '#items' => $rendered_items,
        '#attributes' => array(
          'id' => 'bean-feed-wrapper-' . $bean->bid,
        ),
      );
    }

    return $content;
  }

  /**
   * Returns feed items given an URL.
   *
   * Since feed is queried using Zend_Feed, which is rather expensive, feed
   * results are cached for 5 minutes.
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

    // Cache the expensive Zend_Feed operations.
    $cache_id = md5('bean_feed_' . $bean->bid . '_' . $url);
    if (FALSE && $cache = cache_get($cache_id)) {
      $items = $cache->data;
    }
    else {
      // Load up the feed using Zend_Feed, which requires Zend_Loader_Autoloader.
      $path = drupal_get_path('module', 'bean_feed') . '/includes';
      set_include_path($path . PATH_SEPARATOR . get_include_path());
      include_once 'Zend/Loader/Autoloader.php';
      $loader = Zend_Loader_Autoloader::getInstance();

      try {
        $feed = Zend_Feed::import($url);

        $items = array();
        $i = 0;
        foreach ($feed as $item) {
          $items[] = array(
            'bean' => $bean,
            'link' => $item->link(),
            'title' => $item->title(),
            'description' => $bean->title_only ? NULL : $item->description(),
            'date' => $item->pubDate(),
          );
          if ($i++ == $bean->limit) {
            break;
          }
        }

        // Cache results for 5 minutes.
        cache_set($cache_id, $items, 'cache', time() + 300);
      }
      catch (Zend_Uri_Exception $e) {
        watchdog('bean_feed', 'Invalid feed URI: !message', array('!message' => $e->getMessage()));
      }
      catch (Exception $e) {
        watchdog('bean_feed', 'Exception caught importing feed: !message', array('!message' => $e->getMessage()));
      }
    }

    return $items;
  }
}
