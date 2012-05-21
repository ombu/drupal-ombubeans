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

    return $form;
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
