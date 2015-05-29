<?php

/**
 * @file
 * Media Bean.
 */

class MediaBean extends BeanPlugin {
  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values();
    $values += array(
      'fid' => NULL,
      'file_view_mode' => NULL,
    );

    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {
    $form['fid'] = array(
      '#type' => 'media',
      '#title' => t('Media file'),
      '#media_options' => array(
        'global' => array(
          'types' => array(
            'image' => 'image',
            'document' => 'document',
            'video' => 'video',
          ),
          'enabledPlugins' => array(),
          'schemes' => array('public' => 'public'),
          'uri_scheme' => 'public',
        ),
      ),
      '#default_value' => array(
        'fid' => isset($bean->fid) ? $bean->fid : NULL,
      ),
    );

    // Load up file so view modes can be retrieved. If no file is selected,
    // default to an image file.
    if (isset($bean->fid)) {
      $file = file_load($bean->fid);
    }
    else {
      $file = (object) array(
        'type' => 'image',
      );
    }

    $modes = media_get_wysiwyg_allowed_view_modes($file);
    foreach ($modes as $mode => $info) {
      $options[$mode] = $info['label'];
    }
    $form['file_view_mode'] = array(
      '#type' => 'select',
      '#title' => t('File display mode'),
      '#options' => $options,
      '#default_value' => isset($bean->file_view_mode) ? $bean->file_view_mode : NULL,
      '#attached' => array(
        'js' => array(
          drupal_get_path('module', 'media_bean') . '/js/media-bean.drupal.js',
        ),
      ),
    );

    return $form;
  }

  /**
   * Implements parent::submit().
   */
  public function submit(Bean $bean) {
    $file = (object) array(
      'fid' => $bean->fid,
    );
    file_usage_add($file, 'file', 'bean', $bean->bid);
  }

  /**
    * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    if (!empty($bean->fid)) {
      $file = file_load($bean->fid);
      if ($file) {
        $content['bean'][$bean->delta]['file'] = file_view($file, isset($bean->file_view_mode) ? $bean->file_view_mode : 'default');
      }
    }

    return $content;
  }
}
