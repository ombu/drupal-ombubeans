<?php
/**
 * @file
 * FeaturedBean
 */

/**
 * Featured Content Bean.
 *
 * Placeholder class.  The link field is applied to the bean via features.
 */
class FeaturedBean extends BeanPlugin {
  /**
   * Implements parent::view().
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {
    $featured_content = field_get_items('bean', $bean, 'field_featured_content');

    if ($featured_content) {
      // Ensure that all nodes are published before printing and that the proper
      // node translation is loaded.
      foreach ($featured_content as $key => $value) {
        $featured_content[$key]['entity'] = $node = $this->getTranslatedNode($value['entity']);

        if (empty($node) || !$node->status) {
          unset($featured_content[$key]);
        }
      }

      $content['bean'][$bean->delta]['#featured_content'] = $featured_content;

      // Allow bean styles to alter build.
      if (module_exists('bean_style')) {
        bean_style_view_alter($content, $bean);
      }
    }

    return $content;
  }

  /**
   * Returns the proper translated node.
   */
  protected function getTranslatedNode($node) {
    global $language;

    // If ombutranslation is enabled, swap out translated nodes.
    if (module_exists('ombutranslation')) {
      if ($node->language != $language->language) {
        // First check if there's mirrors.
        $mirrors = ombutranslation_node_translation_mirrors($node->nid);

        // If current entity is mirroring to this language, then show as is.
        if (isset($mirrors[$language->language]) && $mirrors[$language->language]->source == $node->language) {
          return $node;
        }

        // Otherwise, try and load up translation.
        $translations = translation_node_get_translations(!empty($node->tnid) ? $node->tnid : $node->nid);
        if (isset($translations[$language->language])) {
          return node_load($translations[$language->language]->nid);
        }
        else {
          // No translation has been found, hide node from list.
          return FALSE;
        }
      }
    }
    else {
      return $node;
    }
  }
}
