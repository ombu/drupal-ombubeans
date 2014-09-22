<?php
/**
 * @file
 * Provides the OmbubeansPopularContent class.
 */

/**
 * Social Media Links bean.
 *
 * Placeholder class.  The field is applied to the entity via features.
 */
class OmbubeansPopularContent extends BeanPlugin {

  /**
   * Implements the value() method.
   */
  public function values() {
    $values = parent::values();
    $values += array(
      'bundle' => 'page',
      'num' => 5,
      'time_range' => array(
        'enable' => 0,
      ),
    );
    return $values;
  }

  /**
   * Implements the form() method.
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $bundles = array();
    foreach (node_type_get_types() as $type_obj) {
      $bundles[$type_obj->type] = $type_obj->name;
    }

    $form['bundle'] = array(
      '#title' => t('Type of Content'),
      '#type' => 'select',
      '#required' => TRUE,
      '#options' => $bundles,
      '#default_value' => $bean->bundle,
    );

    $form['num'] = array(
      '#title' => t('Number of Results'),
      '#type' => 'select',
      '#required' => TRUE,
      '#options' => array(
        5 => '5',
        10 => '10',
      ),
      '#default_value' => $bean->num,
    );

    $form['time_range'] = array(
      '#tree' => TRUE,
    );

    $form['time_range']['enable'] = array(
      '#type' => 'checkbox',
      '#title' => t('Limit results to time range'),
      '#description' => t('Check to limit results to a relative time range'),
      '#default_value' => isset($bean->time_range['enable']) ? $bean->time_range['enable'] : 0,
    );
    $form['time_range']['range'] = array(
      '#type' => 'textfield',
      '#title' => 'Range',
      '#description' => t('Combined with granularity, restricts popular posts to date range.'),
      '#default_value' => isset($bean->time_range['range']) ? $bean->time_range['range'] : NULL,
      '#states' => array(
        'visible' => array(
          'input[name="time_range[enable]"]' => array('checked' => TRUE),
        ),
      ),
      '#size' => 2,
    );
    $form['time_range']['granularity'] = array(
      '#type' => 'select',
      '#title' => 'Granularity',
      '#options' => array(
        'day' => t('Day(s)'),
        'week' => t('Week(s)'),
        'month' => t('Month(s)'),
        'year' => t('Year(s)'),
      ),
      '#default_value' => isset($bean->time_granularity['granularity']) ? $bean->time_granularity['granularity'] : NULL,
      '#states' => array(
        'visible' => array(
          'input[name="time_range[enable]"]' => array('checked' => TRUE),
        ),
      ),
    );

    return $form;
  }

  /**
   * Implements parent::validate().
   */
  public function validate($values, &$form_state) {
    if (!empty($values['time_range']['range']) && !is_numeric($values['time_range']['range'])) {
      form_set_error('time_range][range]', 'Only numeric values are allowed for time range');
    }
  }

  /**
   * Implements the view() method.
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $query = db_select('node', 'n');
    $query->join('node_counter', 'nc', 'n.nid = nc.nid');
    $query->fields('n', array('nid', 'title', 'status', 'type'));
    $query->fields('nc', array('totalcount'));
    $query->condition('status', 0, '>');
    $query->condition('type', $bean->bundle, '=');
    $query->orderBy('totalcount', 'DESC');
    $query->range(0, $bean->num);

    // Apply time range to restrict posts to a relative date range.
    if ($bean->time_range['enable']) {
      $start_time = strtotime(sprintf('%d %s ago',
        $bean->time_range['range'],
        $bean->time_range['granularity']
      ));

      $query->condition('created', $start_time, '>');
    }

    $content['most_popular'] = array(
      '#theme' => 'item_list',
      '#items' => array(),
    );

    foreach ($query->execute() as $row) {
      $content['most_popular']['#items'][] = array(
        'data' => l($row->title, 'node/' . $row->nid),
        '#row' => (array) $row,
      );
    }

    return $content;
  }
}
