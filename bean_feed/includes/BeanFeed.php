<?php

/**
 * @file
 * Bean plugin object for bean feed.
 */

class BeanFeed extends ombubeans_color {
  /**
   * Implements BeanPlugin::values().
   */
  public function values() {
    $values = parent::values();

    $values += array(
      'feed_url' => '',
      'limit' => 10,
    );
    return $values;
  }

  /**
   * Implements BeanPlugin::form().
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $form['feed_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Feed URL'),
      '#description' => t('The URL to the rss feed.'),
      '#required' => TRUE,
      '#default_value' => $bean->feed_url,
    );

    $form['limit'] = array(
      '#type' => 'select',
      '#title' => t('Item count'),
      '#descripton' => t('The number of items to display from the feed'),
      '#options' => range(1, 10),
      '#default_value' => $bean->limit,
    );

    return $form;
  }

  /**
   * Implements BeanPlugin::submit().
   */
  function submit(Bean $bean) {
    cache_clear_all('bean_feed_' . $bean->bid, 'cache');
  }

  /**
   * Implements BeanPlugin::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    // Cache the expensive Zend_Feed operations.
    $cache_id = md5('bean_feed_' . $bean->bid . '_' . $bean->feed_url);
    if ($cache = cache_get($cache_id)) {
      $items = $cache->data;
    }
    else {
      // Load up the feed using Zend_Feed, which requires Zend_Loader_Autoloader.
      $path = drupal_get_path('module', 'bean_feed') . '/includes';
      set_include_path($path . PATH_SEPARATOR . get_include_path());
      include_once 'Zend/Loader/Autoloader.php';
      $loader = Zend_Loader_Autoloader::getInstance();

      try {
        $feed = Zend_Feed::import($bean->feed_url);

        $items = array();
        $i = 0;
        foreach ($feed as $item) {
          $items[] = array(
            'link' => $item->link(),
            'title' => $item->title(),
            // 'description' => $item->description(),
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
        $content = '';
      }
      catch (Exception $e) {
        watchdog('bean_feed', 'Exception caught importing feed: !message', array('!message' => $e->getMessage()));
        $content = '';
      }
    }

    if (!empty($items)) {
      $rendered_items = array();
      foreach ($items as $item) {
        $rendered_items[] = theme('bean_feed_item', $item);
      }

      // @todo: make this a dedicated theme function, instead of relying on
      // theme_item_list().
      $content['bean'][$bean->bid]['items'] = array(
        '#weight' => -10,
        '#theme' => 'item_list',
        '#items' => $rendered_items,
      );
    }
    else {
      $content = '';
    }

    return $content;
  }
}
