<?php

/**
 * @file
 * Shows YouTube videos in a bean.
 */

class YouTubeBean extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();

    $values += array(
      'playlist' => '',
      'limit' => 10,
      'pager' => TRUE,
    );
    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {

    $form['playlist'] = array(
      '#type' => 'textfield',
      '#title' => t('Playlist ID'),
      '#description' => t('The ID of the playlist to show videos from. The id for a playlist can be found in the URL when viewing a playlist on youtube.com.  For example, if on a playlist URL of <a href="https://www.youtube.com/playlist?list=PLE73A9F5749971C16">https://www.youtube.com/playlist?list=PLE73A9F5749971C16</a> then the playlist id is <em>PLE73A9F5749971C16</em>'),
      '#default_value' => $bean->playlist,
    );

    $form['limit'] = array(
      '#type' => 'select',
      '#title' => t('Item count'),
      '#description' => t('The number of videos to display.'),
      '#options' => range(1, 10),
      '#default_value' => $bean->limit,
    );

    $form['pager'] = array(
      '#type' => 'checkbox',
      '#title' => t('Show pager?'),
      '#description' => t('If left unchecked then pager will be hidden.'),
      '#default_value' => $bean->pager,
    );

    return $form;
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $api_key = variable_get('youtube_bean_api_key', FALSE);
    if ($api_key) {

      // Include Google client library.
      $path = drupal_get_path('module', 'youtube_bean') . '/google-api-php-client/src/';
      require_once $path . 'Google_Client.php';
      require_once $path . 'contrib/Google_YouTubeService.php';

      $client = new Google_Client();
      $client->setDeveloperKey($api_key);

      $youtube = new Google_YoutubeService($client);

      $filters = array(
        'playlistId' => 'PLE73A9F5749971C16',
        'maxResults' => $bean->limit,
      );

      // Get next page if necessary.
      if (isset($_GET['youtube-' . $bean->bid])) {
        $filters['pageToken'] = $_GET['youtube-' . $bean->bid];
      }

      $result = $youtube->playlistItems->listPlaylistItems('snippet,contentDetails', $filters);

      $items = array();
      foreach ($result['items'] as $item) {
        // Retrieve additional content details, since only video ID is retrieved
        // in first request.
        $video_result = $youtube->videos->listVideos('contentDetails', array('id' => $item['snippet']['resourceId']['videoId']));
        $item['contentDetails'] = $video_result['items'][0]['contentDetails'];

        $items[] = theme('youtube_bean_item', array('item' => $item));
      }

      $content['bean'][$bean->delta]['items'] = array(
        '#theme' => 'item_list',
        '#items' => $items,
        '#attributes' => array(
          'class' => array('videos'),
        ),
      );

      // Add pager if there is more than one page. Google uses page tokens to
      // retrieve pages, so the default Drupal pager can't be used here.
      if ($bean->pager) {
        $pager_items = array();
        if (isset($result['prevPageToken'])) {
          $pager_items[] = l('Previous', current_path(), array(
            'query' => array(
              'youtube-' . $bean->bid => $result['prevPageToken'],
            ),
            'attributes' => array(
              'class' => array('previous'),
            ),
          ));
        }
        if (isset($result['nextPageToken'])) {
          $pager_items[] = l('Next', current_path(), array(
            'query' => array(
              'youtube-' . $bean->bid => $result['nextPageToken'],
            ),
            'attributes' => array(
              'class' => array('next'),
            ),
          ));
        }

        if ($pager_items) {
          $content['bean'][$bean->delta]['pager'] = array(
            '#theme' => 'item_list',
            '#items' => $pager_items,
            '#attributes' => array(
              'class' => array('pager'),
            ),
          );
        }
      }
    }

    return $content;
  }
}
