<?php

namespace Drupal\dds_layout\Form;

use Drupal\Core\Render\Element;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Trusted callbacks for allowed languages form alters.
 */
class MungoInspirationPrerender implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['deckPrerender'];
  }

  /**
   * Removes any languages that the user is not allowed to create content for.
   */
  public static function deckPrerender($build) {
    if (!empty($build['field_main_media'])) {
      foreach (Element::children($build['field_main_media']) as $name) {
        if (!isset($build['field_main_media'][$name]['#view_mode']) || $build['field_main_media'][$name]['#view_mode'] === 'teaser') {
          $elements['field_main_media'][$name]['#view_mode'] = 'extended_teaser';
          // Make sure this field is going to be rendered again if it is
          // rendered without this prerender.
          $elements['field_main_media'][$name]['#cache']['keys'][] = 'mungo_inspirational_prerender';
        }
      }
    }
    return $build;

  }

}
