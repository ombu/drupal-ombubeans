<?php

/**
 * @file
 * Menu Block Bean class.
 */

class MenuBlockBean extends BeanPlugin {
  /**
   * Menu to pull links from.
   *
   * Currently is hardcoded to main menu.
   *
   * @var string
   */
  protected $menuName;

  /**
   * Constructor.
   */
  public function __construct($plugin_info) {
    parent::__construct($plugin_info);

    $this->menuName = variable_get('menu_main_links_source', 'main-menu');
  }

  /**
   * Implements parent::values().
   */
  public function values() {
    $values = parent::values() + array(
      'parent_mlid' => $this->menuName . ':0',
      // @todo: allow this to be configurable.
      'depth' => 1,
    );

    return $values;
  }

  /**
   * Implements parent::form().
   */
  public function form($bean, $form, &$form_state) {
    $form = parent::form($bean, $form, $form_state);

    $options = menu_parent_options(array($this->menuName => 'Main menu'), array('mlid' => 0));
    $form['parent_mlid'] = array(
      '#type' => 'select',
      '#title' => t('Parent link'),
      '#default_value' => $bean->parent_mlid,
      '#options' => $options,
      '#description' => t('Select the menu item for which to show children of.'),
      '#attributes' => array('class' => array('menu-title-select')),
    );

    return $form;
  }

  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    // Todo: allow other menus to be selected.
    $tree = menu_tree_all_data($this->menuName);

    if ($bean->parent_mlid) {
      list(, $parent_mlid) = explode(':', $bean->parent_mlid);

      $this->pruneTree($tree, $parent_mlid);
    }

    $this->pruneDepth($tree, $depth);

    return menu_tree_output($tree);
  }

  /**
   * Prune tree to menu item.
   */
  protected function pruneTree(&$tree, $parent_mlid) {
    for ($i = 1; $i <= MENU_MAX_DEPTH; $i++) {
      $found_active_trail = FALSE;
      foreach ($tree as $key => $value) {
        if ($value['link']['mlid'] == $parent_mlid) {
          $tree = $tree[$key]['below'] ? $tree[$key]['below'] : array();
          $found_active_trail = TRUE;
          break 2;
        }
      }
      if (!$found_active_trail) {
        $tree = array();
        break;
      }
    }
  }

  /**
   * Prune children to a specific depth.
   */
  protected function pruneDepth(&$tree, $depth) {
    foreach ($tree as $key => $item) {
      if ($depth > 1) {
        $this->pruneDepth($item['below'], $depth--);
      }
      else {
        $tree[$key]['below'] = array();
      }
    }
  }

}
